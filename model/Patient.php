<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';

class Patient {
    private $conn;
    
    function __construct($db){
        $this->conn = $db;
    }

    // === PATIENTS
    // === CRUD OPERATIONS

    // CREATE/ADD PATIENT
    // $user_id links this record to a patient account (users table) created via self-registration.
    // Left null/omitted for records added manually by admin/front-desk staff.
    function addPatient($first_name, $last_name, $gender, $birthdate, $phone, $email, $address, $emergency_contact, $allergies, $medical_history, $user_id = null){
        $query = "INSERT INTO patients (first_name, last_name, gender, birthdate, phone, email, address, emergency_contact, allergies, medical_history, user_id) 
                  VALUES (:first_name, :last_name, :gender, :birthdate, :phone, :email, :address, :emergency_contact, :allergies, :medical_history, :user_id)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":gender", $gender);
        $stmt->bindParam(":birthdate", $birthdate);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":emergency_contact", $emergency_contact);
        $stmt->bindParam(":allergies", $allergies);
        $stmt->bindParam(":medical_history", $medical_history);
        $stmt->bindParam(":user_id", $user_id);

        return $stmt->execute();
    }

    // READ/GET PATIENT RECORD LINKED TO A PATIENT USER ACCOUNT
    function getPatientByUserId($user_id){
        $query = "SELECT * FROM patients WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CHECK IF A PATIENT PROFILE HAS ALL REQUIRED FIELDS FILLED
    function isProfileComplete($patient_data){
        if (!$patient_data) return false;
        $required = ['first_name', 'last_name', 'gender', 'birthdate', 'phone', 'email', 'address'];
        foreach ($required as $field){
            if (empty($patient_data[$field])) return false;
        }
        return true;
    }

    // READ/GET ALL PATIENTS
    function getAllPatients(){
        $query = "SELECT * FROM patients ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET PATIENT BY ID
    function getPatientById($id){
        $query = "SELECT * FROM patients WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ/COUNT TOTAL
    function countTotal(){
        $query = "SELECT COUNT(*) as total FROM patients";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // READ/SEARCH PATIENTS
    function searchPatients($search){
        $query = "SELECT * FROM patients WHERE first_name LIKE :search OR last_name LIKE :search ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":search", "%$search%");
        $stmt->execute();
        return $stmt;
    }

    // UPDATE PATIENT
    function updatePatient($id, $first_name, $last_name, $gender, $birthdate, $phone, $email, $address, $emergency_contact, $allergies, $medical_history){
        $query = "UPDATE patients SET first_name = :first_name, last_name = :last_name, gender = :gender, birthdate = :birthdate, phone = :phone, email = :email, address = :address, emergency_contact = :emergency_contact, allergies = :allergies, medical_history = :medical_history WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":gender", $gender);
        $stmt->bindParam(":birthdate", $birthdate);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":emergency_contact", $emergency_contact);
        $stmt->bindParam(":allergies", $allergies);
        $stmt->bindParam(":medical_history", $medical_history);

        return $stmt->execute();
    }

    // DELETE PATIENT
    function deletePatient($id){
        $query = "DELETE FROM patients WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }


    // === HISTORY & RECORDS

    // READ/GET CONSULTATION HISTORY FOR PATIENT (completed only)
    function getPatientConsultationHistory($patient_id){
        $query = "SELECT c.*, 
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                  a.appointment_date, a.appointment_time, a.purpose
                  FROM consultations c
                  LEFT JOIN users d ON c.doctor_id = d.id
                  LEFT JOIN appointments a ON c.appointment_id = a.id
                  WHERE c.patient_id = :patient_id AND a.status = 'completed'
                  ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET MEDICINES FOR CONSULTATION
    function getConsultationMedicines($consultation_id){
        $query = "SELECT * FROM consultation_medicines WHERE consultation_id = :cid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cid", $consultation_id);
        $stmt->execute();
        return $stmt;
    }

    // READ/GET RECOMMENDATIONS FOR CONSULTATION
    function getConsultationRecommendations($consultation_id){
        $query = "SELECT * FROM consultation_recommendations WHERE consultation_id = :cid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cid", $consultation_id);
        $stmt->execute();
        return $stmt;
    }
}