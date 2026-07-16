<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | SwiftCare</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/patient.css">
    <link rel="stylesheet" href="../css/patient_dashboardd.css">
</head>

<body>

    <!-- TOP NAVBAR-->
    <header class="topbar">

        <!-- Logo -->
        <div class="logo">
            <a href="patient_dashboard.php">
                <img src="../../img/logo.png" alt="SwiftCare Logo">
            </a>
            <span>SwiftCare</span>
        </div>

        <!-- Navigation -->
        <nav class="top-nav">
            <a href="patient_dashboard.php">Home</a>
            <a href="patient_profile.php"class="active">Patient Profile</a>
            <a href="patient_request consultation.php">Request Consultation</a>
            <a href="patient_view request status.php">View Request Status</a>
            <a href="patient_view_appointment.php">Appointment</a>
            <a href="patient_request medical.php">Request Medical Documents</a>
            <a href="patient_services.php">Services</a>
            <a href="all doctors.php"> All Doctors</a>
        </nav>


        <!-- Profile -->
        <div class="profile">
            <img src="../../img/Taroy.jpg" alt="Patient Profile">
        </div>

    </header>

    <main class="main-content">

        <header class="topbar">
            <h1>Patient Profile</h1>
        </header>

        <div class="profile-wrap">

            <div id="successAlert" class="alert-success">Your profile details have been updated successfully!</div>

            <form id="profileForm" autocomplete="off" onsubmit="handleFormSubmit(event)">

                <div class="profile-card">
                    <h2>Update Information</h2>
                    <p class="subtitle">Manage your clinic record details</p>
                    <p class="required-note"><span style="color:red">*</span> Required Fields</p>

                    <div class="form-grid">
                        <!-- First Name -->
                        <div class="field">
                            <label for="first_name">First Name <span style="color:red">*</span></label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>

                        <!-- Last Name -->
                        <div class="field">
                            <label for="last_name">Last Name <span style="color:red">*</span></label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>

                        <!-- Gender -->
                        <div class="field">
                            <label for="gender">Gender <span style="color:red">*</span></label>
                            <select id="gender" name="gender" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <!-- Birthdate -->
                        <div class="field">
                            <label for="birthdate">Birthdate <span style="color:red">*</span></label>
                            <input type="date" id="birthdate" name="birthdate" required max="2026-07-14">
                        </div>

                        <!-- Contact Number -->
                        <div class="field">
                            <label for="phone">Contact Number <span style="color:red">*</span></label>
                            <input type="text" id="phone" name="phone" required maxlength="11" pattern="[0-9]{11}" placeholder="e.g. 09123456789">
                        </div>

                        <!-- Email -->
                        <div class="field">
                            <label for="email">Email <span style="color:red">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <!-- Address -->
                        <div class="field full">
                            <label for="address">Address <span style="color:red">*</span></label>
                            <input type="text" id="address" name="address" required>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="field full">
                            <label for="emergency_contact">Emergency Contact Number</label>
                            <input type="text" id="emergency_contact" name="emergency_contact" maxlength="11" pattern="[0-9]{11}" placeholder="e.g. 09123456789">
                        </div>

                        <!-- Allergies -->
                        <div class="field full">
                            <label for="allergies">Allergies</label>
                            <input type="text" id="allergies" name="allergies" placeholder="e.g. Penicillin, Peanuts (Leave blank if none)">
                        </div>

                        <!-- Medical History -->
                        <div class="field full">
                            <label for="medical_history">Medical History</label>
                            <textarea id="medical_history" name="medical_history" placeholder="Previous conditions, surgeries, or ongoing treatments..."></textarea>
                        </div>
                    </div>

                    <br>
                    <div class="button-group">
                        <button type="submit" class="btn-save">Save Changes</button>
                        <a href="patient_dashboard.php" class="btn-cancel">Cancel</a>
                    </div>
                </div>

            </form>

        </div>

    </main>

</div>

<script>
function handleFormSubmit(event) {
    event.preventDefault();
    
    // Display the success banner confirmation
    const alertBox = document.getElementById('successAlert');
    alertBox.style.display = 'block';
    
    // Smooth scroll back to the banner view
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

</body>
</html>