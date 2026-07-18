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
    <?php include_once '../chatbot_widget.php'; ?>
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
            <a href="patient_profile.php">Patient Profile</a>
            <a href="patient_request consultation.php">Request Consultation</a>
            <a href="patient_view request status.php"class="active">View Request Status</a>
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

    <main class="appointments-wrap">
        <h1>View Consultation Requests</h1>
        <p class="subtitle">Select a request that you want to view or edit.</p>
        <hr class="top-rule">

        <div class="info-banner">
            <p>Select a request to view or edit</p>
            <div class="en">Choose one of the requests below from the Outstanding or Past tab.</div>
        </div>

        <div class="tabs-container">
            <button type="button" id="tab-outstanding" class="tab-btn active" onclick="switchTab('outstanding')">Outstanding Requests</button>
            <button type="button" id="tab-past" class="tab-btn" onclick="switchTab('past')">Past Requests</button>
        </div>

        <div class="tab-content">
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Consult Reference Number</th>
                        <th>Patient Code</th>
                        <th>Chief Complaint</th>
                        <th>Date of Complaint</th>
                        <th>Status</th>
                        <th id="action-header">Select</th>
                    </tr>
                </thead>
                <tbody id="requests-tbody"></tbody>
            </table>
        </div>
    </main>

<script>
const outstandingRequests = [
    {
        ref: 'REQ-2026-000123',
        patient_code: 'PAT-99102',
        complaint: 'Persistent cough',
        date: '2026-07-10',
        status: 'Pending Review'
    }
];

const pastRequests = [
    {
        ref: 'REQ-2025-004821',
        patient_code: 'PAT-88301',
        complaint: 'Follow-up checkup',
        date: '2025-11-02',
        status: 'Completed'
    }
];

function switchTab(tabType) {
    document.getElementById('tab-outstanding').classList.toggle('active', tabType === 'outstanding');
    document.getElementById('tab-past').classList.toggle('active', tabType === 'past');

    const tbody = document.getElementById('requests-tbody');
    const actionHeader = document.getElementById('action-header');
    tbody.innerHTML = '';

    const data = tabType === 'outstanding' ? outstandingRequests : pastRequests;

    if (tabType === 'outstanding') {
        actionHeader.style.display = '';
    } else {
        actionHeader.style.display = 'none';
    }

    if (data.length === 0) {
        const colSpan = tabType === 'outstanding' ? 6 : 5;
        tbody.innerHTML = `
            <tr class="empty-row">
                <td colspan="${colSpan}">No ${tabType} consultation requests found.</td>
            </tr>
        `;
    } else {
        data.forEach(row => {
            let actionCell = '';
            if (tabType === 'outstanding') {
                actionCell = `<td><a class="btn-view" href="view_request.html?ref=${encodeURIComponent(row.ref)}">Select</a></td>`;
            }

            const statusClass = row.status.toLowerCase().includes('complete') ? 'completed' : 'pending';

            tbody.innerHTML += `
                <tr>
                    <td>${escapeHtml(row.ref)}</td>
                    <td><b>${escapeHtml(row.patient_code)}</b></td>
                    <td>${escapeHtml(row.complaint)}</td>
                    <td>${escapeHtml(row.date)}</td>
                    <td><span class="status-badge ${statusClass}">${escapeHtml(row.status)}</span></td>
                    ${actionCell}
                </tr>
            `;
        });
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

document.addEventListener('DOMContentLoaded', () => {
    switchTab('outstanding');
});
</script>

</body>
</html>