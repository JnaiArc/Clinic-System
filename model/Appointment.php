<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';

class Appointment {
    private $conn;
    
    function __construct($db){
        $this->conn = $db;
    }

    // === APPOINTMENTS
    // === CRUD OPERATIONS

    // CREATE/ADD APPOINTMENT
    function addAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $purpose){
        $query = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, purpose, status) 
                  VALUES (:patient_id, :doctor_id, :appointment_date, :appointment_time, :purpose, 'pending')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":appointment_date", $appointment_date);
        $stmt->bindParam(":appointment_time", $appointment_time);
        $stmt->bindParam(":purpose", $purpose);

        return $stmt->execute();
    }

    // READ/GET TODAY'S APPOINTMENTS
    function getTodayAppointments(){
        $query = "SELECT a.*, 
                  CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE DATE(a.appointment_date) = DATE(NOW())
                  ORDER BY a.appointment_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET APPOINTMENT BY ID
    function getAppointmentById($id){
        $query = "SELECT a.*, 
                  CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                  p.phone as patient_phone,
                  p.email as patient_email,
                  p.address as patient_address,
                  p.gender as patient_gender,
                  p.birthdate as patient_birthdate,
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ/GET FOLLOW-UP APPOINTMENTS 
    function getFollowUpAppointments(){
        $query = "SELECT a.*, 
                  CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.purpose = 'Follow-up' AND a.status = 'pending' AND DATE(a.appointment_date) >= DATE(NOW())
                  ORDER BY a.appointment_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET UPCOMING(FUTURE DATES)
    function getUpcomingOnlyAppointments(){
        $query = "SELECT a.*, 
                  CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE DATE(a.appointment_date) > CURDATE() AND a.status = 'pending'
                  ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET ALL
    function getAllTodayAndFuture(){
        $query = "SELECT a.*, 
                  CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.status IN ('pending', 'missed')
                  ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET COMPLETED APPOINTMENTS
    function getCompletedAppointments(){
        $query = "SELECT a.*, 
                  CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.status = 'completed'
                  ORDER BY a.appointment_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ/COUNT TODAY
    function countToday(){
        $query = "SELECT COUNT(*) as total FROM appointments WHERE DATE(appointment_date) = DATE(NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // READ/COUNT FOLLOW-UPS
    function countFollowUps(){
        $query = "SELECT COUNT(*) as total FROM appointments WHERE purpose = 'Follow-up' AND DATE(appointment_date) >= DATE(NOW()) AND status != 'completed'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // READ/GET FOLLOW-UP LIST
    function getFollowUpList(){
        $query = "SELECT a.*, 
                 CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                 CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                 FROM appointments a
                 LEFT JOIN patients p ON a.patient_id = p.id
                 LEFT JOIN users d ON a.doctor_id = d.id
                 WHERE a.purpose = 'Follow-up' AND DATE(a.appointment_date) >= DATE(NOW()) AND a.status != 'completed'
                 ORDER BY a.appointment_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // DELETE APPOINTMENT
    function deleteAppointment($id){
        $query = "DELETE FROM appointments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // UPDATE APPOINTMENT
    function updateAppointmentSimple($id, $doctor_id, $appointment_date, $appointment_time, $purpose){
        $query = "UPDATE appointments SET doctor_id = :doctor_id, appointment_date = :appointment_date, appointment_time = :appointment_time, purpose = :purpose WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":appointment_date", $appointment_date);
        $stmt->bindParam(":appointment_time", $appointment_time);
        $stmt->bindParam(":purpose", $purpose);

        return $stmt->execute();
    }

    // UPDATE MARK PAST PENDING AS MISSED
    function markMissedAppointments(){
        $query = "UPDATE appointments SET status = 'missed' 
                  WHERE status = 'pending' AND DATE(appointment_date) < CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }


    // === DOCTOR SPECIFIC

    // READ/GET DOCTOR'S TODAY APPOINTMENTS
    function getDoctorTodayAppointments($doctor_id){
        $query = "SELECT a.*, 
                CONCAT(p.first_name, ' ', p.last_name) as patient_name, p.phone, p.email
                FROM appointments a
                LEFT JOIN patients p ON a.patient_id = p.id
                WHERE a.doctor_id = :doctor_id AND DATE(a.appointment_date) = DATE(NOW())
                ORDER BY a.appointment_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET DOCTOR'S FOLLOW-UPS
    function getDoctorFollowUps($doctor_id){
        $query = "SELECT a.*, 
                CONCAT(p.first_name, ' ', p.last_name) as patient_name, p.phone, p.email
                FROM appointments a
                LEFT JOIN patients p ON a.patient_id = p.id
                WHERE a.doctor_id = :doctor_id AND DATE(a.appointment_date) >= CURDATE() AND a.purpose = 'Follow-up' AND a.status = 'pending'
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET DOCTOR UPCOMING ONLY
    function getDoctorUpcomingOnly($doctor_id){
        $query = "SELECT a.*, 
                CONCAT(p.first_name, ' ', p.last_name) as patient_name
                FROM appointments a
                LEFT JOIN patients p ON a.patient_id = p.id
                WHERE a.doctor_id = :doctor_id AND DATE(a.appointment_date) > CURDATE() AND a.status = 'pending'
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET DOCTOR ALL
    function getDoctorAllTodayAndFuture($doctor_id){
        $query = "SELECT a.*, 
                CONCAT(p.first_name, ' ', p.last_name) as patient_name
                FROM appointments a
                LEFT JOIN patients p ON a.patient_id = p.id
                WHERE a.doctor_id = :doctor_id AND a.status IN ('pending', 'missed')
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET DOCTOR COMPLETED APPOINTMENTS
    function getDoctorCompletedAppointments($doctor_id){
        $query = "SELECT a.*,
                CONCAT(p.first_name, ' ', p.last_name) as patient_name
                FROM appointments a
                LEFT JOIN patients p ON a.patient_id = p.id
                WHERE a.doctor_id = :doctor_id AND a.status = 'completed'
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->execute();
        return $stmt;
    }


    // === CONSULTATION RECORDS

    // READ/GET PREVIOUS CONSULTATION FOR PATIENT
    function getPreviousConsultation($patient_id, $doctor_id, $current_date) {

        $query = "SELECT c.* FROM consultations c
                JOIN appointments a ON c.appointment_id = a.id
                WHERE c.patient_id = :patient_id AND c.doctor_id = :doctor_id AND a.appointment_date < :current_date
                ORDER BY c.created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":current_date", $current_date);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) return $result;

        $query = "SELECT c.* FROM consultations c
                WHERE c.patient_id = :patient_id AND c.doctor_id = :doctor_id
                ORDER BY c.created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) return $result;

        $query = "SELECT c.* FROM consultations c
                WHERE c.patient_id = :patient_id
                ORDER BY c.created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ/GET CONSULTATION FOR APPOINTMENT
    function getConsultationForAppointment($appointment_id, $patient_id) {

        $query = "SELECT * FROM consultations WHERE appointment_id = :appointment_id ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":appointment_id", $appointment_id);
        $stmt->execute();
        $consultation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$consultation) {
            $query = "SELECT c.* FROM consultations c
                    JOIN appointments a ON c.appointment_id = a.id
                    WHERE a.patient_id = :patient_id
                    ORDER BY c.id DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":patient_id", $patient_id);
            $stmt->execute();
            $consultation = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $consultation;
    }

    // READ/GET MEDICINES BY CONSULTATION ID
    function getMedicinesByConsultationId($consultation_id) {
        $query = "SELECT * FROM consultation_medicines WHERE consultation_id = :consultation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":consultation_id", $consultation_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET RECOMMENDATIONS BY CONSULTATION ID
    function getRecommendationsByConsultationId($consultation_id) {
        $query = "SELECT * FROM consultation_recommendations WHERE consultation_id = :consultation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":consultation_id", $consultation_id);
        $stmt->execute();
        return $stmt;
    }
}