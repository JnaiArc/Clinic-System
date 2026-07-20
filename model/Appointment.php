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
    // $consultation_type: 'Online' or 'In Person' (defaults to 'In Person' for admin/front-desk bookings)
    // $complaint: free-text symptom the patient is booking for (e.g. "Fever", "Headache")
    function addAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $purpose, $consultation_type = 'In Person', $complaint = ''){
        $query = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, purpose, complaint, consultation_type, status) 
                  VALUES (:patient_id, :doctor_id, :appointment_date, :appointment_time, :purpose, :complaint, :consultation_type, 'pending')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":appointment_date", $appointment_date);
        $stmt->bindParam(":appointment_time", $appointment_time);
        $stmt->bindParam(":purpose", $purpose);
        $stmt->bindParam(":complaint", $complaint);
        $stmt->bindParam(":consultation_type", $consultation_type);

        return $stmt->execute();
    }

    // CHECK IF A PATIENT ALREADY HAS AN UPCOMING (PENDING/CONFIRMED) APPOINTMENT (prevents double-booking)
    function hasActiveAppointment($patient_id){
        $query = "SELECT COUNT(*) as total FROM appointments WHERE patient_id = :patient_id AND status IN ('pending','confirmed')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    // === AVAILABILITY / SLOT HELPERS (specialization -> doctor -> calendar -> time booking flow)

    // Build the list of bookable time-slot labels (e.g. "1:00 PM", "1:30 PM", ... ) for a
    // doctor's schedule window, in 30-minute increments, INCLUSIVE of the end time.
    // e.g. 1:00 PM - 5:00 PM => 9 slots (1:00,1:30,2:00,2:30,3:00,3:30,4:00,4:30,5:00)
    public static function generateSlots($start_time, $end_time){
        $slots = [];
        if (empty($start_time) || empty($end_time)) return $slots;

        $startTs = strtotime($start_time);
        $endTs = strtotime($end_time);
        if ($startTs === false || $endTs === false || $endTs < $startTs) return $slots;

        for ($t = $startTs; $t <= $endTs; $t += 1800) {
            $slots[] = date('g:i A', $t);
        }
        return $slots;
    }

    // GET LIST OF ALREADY-BOOKED TIME LABELS FOR A DOCTOR ON A SPECIFIC DATE
    function getBookedTimesForDoctorDate($doctor_id, $date){
        $query = "SELECT appointment_time FROM appointments 
                  WHERE doctor_id = :doctor_id AND appointment_date = :date AND status IN ('pending','confirmed')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":date", $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // GET BOOKED APPOINTMENT COUNTS PER DATE FOR A DOCTOR, WITHIN A DATE RANGE (used to render the calendar)
    // Returns an associative array: ['2026-07-21' => 3, '2026-07-22' => 1, ...]
    function getBookedCountsForDoctorRange($doctor_id, $start_date, $end_date){
        $query = "SELECT appointment_date, COUNT(*) as total FROM appointments 
                  WHERE doctor_id = :doctor_id AND appointment_date BETWEEN :start_date AND :end_date 
                  AND status IN ('pending','confirmed')
                  GROUP BY appointment_date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":start_date", $start_date);
        $stmt->bindParam(":end_date", $end_date);
        $stmt->execute();

        $counts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $counts[$row['appointment_date']] = (int)$row['total'];
        }
        return $counts;
    }

    // CHECK IF A SPECIFIC DOCTOR/DATE/TIME SLOT IS ALREADY TAKEN (server-side guard against race conditions/double-booking)
    function isSlotTaken($doctor_id, $date, $time){
        $query = "SELECT COUNT(*) as total FROM appointments 
                  WHERE doctor_id = :doctor_id AND appointment_date = :date AND appointment_time = :time 
                  AND status IN ('pending','confirmed')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":time", $time);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    // === PATIENT PORTAL SPECIFIC

    // READ/GET THE SOONEST UPCOMING (NOT CANCELLED/COMPLETED) APPOINTMENT FOR THE HOME PAGE CARD
    function getNextAppointmentForPatient($patient_id){
        $query = "SELECT a.*, 
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.patient_id = :patient_id AND a.status IN ('pending','confirmed') AND DATE(a.appointment_date) >= CURDATE()
                  ORDER BY a.appointment_date ASC, a.appointment_time ASC
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ/GET PATIENT'S CURRENT (UPCOMING, NOT CANCELLED/COMPLETED) APPOINTMENTS
    function getPatientCurrentAppointments($patient_id){
        $query = "SELECT a.*, 
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.patient_id = :patient_id AND a.status IN ('pending','confirmed')
                  ORDER BY a.appointment_date ASC, a.appointment_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET PATIENT'S PAST (COMPLETED OR MISSED) APPOINTMENTS
    function getPatientPastAppointments($patient_id){
        $query = "SELECT a.*, 
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.patient_id = :patient_id AND a.status IN ('completed','missed')
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET PATIENT'S CANCELLED APPOINTMENTS
    function getPatientCancelledAppointments($patient_id){
        $query = "SELECT a.*, 
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.patient_id = :patient_id AND a.status = 'cancelled'
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET A SINGLE APPOINTMENT, SCOPED TO A PATIENT (ownership check for patient portal "View")
    function getPatientAppointmentById($id, $patient_id){
        $query = "SELECT a.*, 
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.id = :id AND a.patient_id = :patient_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    // READ/GET ALL (appointments still pending — excludes missed, cancelled, completed)
    function getAllTodayAndFuture(){
        $query = "SELECT a.*, 
                  CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM appointments a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN users d ON a.doctor_id = d.id
                  WHERE a.status = 'pending'
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
    function updateAppointmentSimple($id, $doctor_id, $appointment_date, $appointment_time, $purpose, $complaint = ''){
        $query = "UPDATE appointments SET doctor_id = :doctor_id, appointment_date = :appointment_date, appointment_time = :appointment_time, purpose = :purpose, complaint = :complaint WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":appointment_date", $appointment_date);
        $stmt->bindParam(":appointment_time", $appointment_time);
        $stmt->bindParam(":purpose", $purpose);
        $stmt->bindParam(":complaint", $complaint);

        return $stmt->execute();
    }

    // CANCEL APPOINTMENT (patient-portal action, scoped to the owning patient so patients can't cancel others' bookings)
    function cancelAppointment($id, $patient_id){
        $query = "UPDATE appointments SET status = 'cancelled' WHERE id = :id AND patient_id = :patient_id AND status IN ('pending','confirmed')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":patient_id", $patient_id);
        return $stmt->execute();
    }

    // CANCEL APPOINTMENT (admin/front-desk action)
    function cancelAppointmentAdmin($id){
        $query = "UPDATE appointments SET status = 'cancelled' WHERE id = :id AND status IN ('pending','confirmed','missed')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
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

    // READ/GET DOCTOR ALL (appointments still pending — excludes missed, cancelled, completed)
    function getDoctorAllTodayAndFuture($doctor_id){
        $query = "SELECT a.*, 
                CONCAT(p.first_name, ' ', p.last_name) as patient_name
                FROM appointments a
                LEFT JOIN patients p ON a.patient_id = p.id
                WHERE a.doctor_id = :doctor_id AND a.status = 'pending'
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