<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';

$database = new Database();
$conn = $database->connect();
$appointment = new Appointment($conn);
$user = new User($conn);

$user_info = $user->getUserById($_SESSION['user_id']);

$appointment_id = (int)$_GET['id'];
$appointment_data = $appointment->getAppointmentById($appointment_id);

$consultation = null;
$medicines = null;
$recommendations = null;

if ($appointment_data) {
    $consultation = $appointment->getConsultationForAppointment($appointment_id, $appointment_data['patient_id']);
    
    if ($consultation) {
        $medicines = $appointment->getMedicinesByConsultationId($consultation['id']);
        $recommendations = $appointment->getRecommendationsByConsultationId($consultation['id']);
    }
}

$doctors = $user->getAllDoctors();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Appointment</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin_info.css">
</head>

<body>

<div class="dashboard-container">
    <aside class="sidebar-simple">
        <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="back-btn-top">
            <div class="icon">←</div>
            <span>Back</span>
        </a>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Appointment Details</h1>
                </div>
            </div>
            
            <div class="user-menu">
                <button type="button" class="user-menu-toggle" onclick="toggleUserMenu(this)">
                    <?php if(!empty($user_info['profile_photo'])): ?>
                    <img src="../../uploads/<?php echo $user_info['profile_photo']; ?>" class="user-avatar" style="object-fit: cover;">
                    <?php else: ?>
                    <div class="user-avatar"></div>
                    <?php endif; ?>
                    <span class="user-name"><?php echo $_SESSION['name']; ?></span>
                    <span class="user-role-badge">Admin</span>
                    <span class="dropdown-arrow">&#9662;</span>
                </button>
                <div class="user-dropdown">
                    <a href="http://localhost/clinic1/view/admin/my_profile.php" class="dropdown-item">My Profile</a>
                    <a href="http://localhost/clinic1/controller/logoutController.php" class="dropdown-item signout" onclick="return confirm('Logout?')">Sign Out</a>
                </div>
            </div>
        </header>

        <!-- Edit Form -->
        <?php if($appointment_data['status'] != 'completed'): ?>
        <form method="POST" action="http://localhost/clinic1/controller/AppointmentController.php">
            <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
            <section class="table-section section-gap">
                <div class="section-header">Appointment Information</div>
                <div class="info-grid info-grid-pad">
                    <div class="info-item"><label>Patient</label><span><?php echo $appointment_data['patient_name']; ?></span></div>
                    <div class="info-item">
                        <label>Date</label>
                        <input type="date" name="appointment_date" id="editDate"
                               value="<?php echo $appointment_data['appointment_date']; ?>"
                               min="<?php echo date('Y-m-d'); ?>"
                               onchange="editLoadDoctors()">
                    </div>
                    <div class="info-item">
                        <label>Doctor</label>
                        <select name="doctor_id" id="editDoctorSel" onchange="editLoadTimes()">
                            <option value="">Pick a date first</option>
                        </select>
                    </div>
                    <div class="info-item">
                        <label>Time</label>
                        <select name="appointment_time" id="editTimeSel">
                            <option value="">Pick a doctor first</option>
                        </select>
                    </div>
                    <div class="info-item"><label>Purpose</label><span><?php echo $appointment_data['purpose']; ?></span></div>
                    <div class="info-item"><label>Status</label><span class="status <?php echo $appointment_data['status']; ?>"><?php echo ucfirst($appointment_data['status']); ?></span></div>
                </div>
            </section>
            <div class="btn-row">
                <button type="submit" name="updateAppointment" class="btn-save">Save Changes</button>
                <a href="http://localhost/clinic1/controller/AppointmentController.php?delete=<?php echo $appointment_id; ?>" class="btn-delete" onclick="return confirm('Delete this appointment?')">Delete</a>
            </div>
        </form>
        <?php else: ?>
        <!-- read-only -->
        <section class="table-section section-gap">
            <div class="section-header">Appointment Information</div>
            <div class="info-grid info-grid-pad">
                <div class="info-item"><label>Patient</label><span><?php echo $appointment_data['patient_name']; ?></span></div>
                <div class="info-item"><label>Doctor</label><span>Dr. <?php echo $appointment_data['doctor_name']; ?></span></div>
                <div class="info-item"><label>Date</label><span><?php echo $appointment_data['appointment_date']; ?></span></div>
                <div class="info-item"><label>Time</label><span><?php echo $appointment_data['appointment_time']; ?></span></div>
                <div class="info-item"><label>Purpose</label><span><?php echo $appointment_data['purpose']; ?></span></div>
                <div class="info-item"><label>Status</label><span class="status <?php echo $appointment_data['status']; ?>"><?php echo ucfirst($appointment_data['status']); ?></span></div>
            </div>
        </section>
        <?php endif; ?>

        <!-- read only consultation -->
        <?php if($consultation): ?>
        <section class="table-section rx-section">
            <div class="section-header">Consultation Details</div>
            <div class="info-grid info-grid-pad">
                <div class="info-item" style="background: white;"><label>Findings</label><span><?php echo nl2br($consultation['findings']); ?></span></div>
            </div>
            
            <?php if($medicines && $medicines->rowCount() > 0): ?>
            <div class="info-grid-pad">
                <label class="rx-label">Medicines</label>
                <?php while($med = $medicines->fetch(PDO::FETCH_ASSOC)): 
                    $is_done = isset($med['is_done']) ? $med['is_done'] : 0;
                    $rx_class = $is_done ? 'rx-item-checked' : 'rx-item'; 
                ?>
                <div class="<?php echo $rx_class; ?>">
                    <input type="checkbox" <?php echo $is_done ? 'checked' : ''; ?> disabled class="rx-checkbox">
                    <span class="rx-flex"><?php echo $med['medicine_name']; ?> - <?php echo $med['dosage']; ?> x <?php echo $med['frequency']; ?> for <?php echo $med['duration']; ?></span>
                    <?php if($is_done): ?><span class="rx-check">✓</span><?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
            
            <?php if($recommendations && $recommendations->rowCount() > 0): ?>
            <div class="info-grid-pad">
                <label class="rx-label">Recommendations</label>
                <?php while($rec = $recommendations->fetch(PDO::FETCH_ASSOC)): 
                    $is_done = isset($rec['is_done']) ? $rec['is_done'] : 0;
                    $rx_class = $is_done ? 'rx-item-checked' : 'rx-item'; 
                ?>
                <div class="<?php echo $rx_class; ?>">
                    <input type="checkbox" <?php echo $is_done ? 'checked' : ''; ?> disabled class="rx-checkbox">
                    <span class="rx-flex"><?php echo $rec['recommendation']; ?></span>
                    <?php if($is_done): ?><span class="rx-check">✓</span><?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    </main>
</div>

<script>
var _curDocId   = <?php echo (int)$appointment_data['doctor_id']; ?>;
var _curTime    = "<?php echo addslashes($appointment_data['appointment_time']); ?>";

function _dayName(ds){var d=new Date(ds+'T00:00:00');return['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d.getDay()];}
function _t2h(t){if(!t)return 8;var p=t.match(/(\d+):(\d+)\s*(AM|PM)/i);if(!p)return 8;var h=parseInt(p[1]),ap=p[3].toUpperCase();if(ap==='PM'&&h!==12)h+=12;if(ap==='AM'&&h===12)h=0;return h;}

function editLoadDoctors(){
    var dv=document.getElementById('editDate').value;
    var ds=document.getElementById('editDoctorSel');
    var ts=document.getElementById('editTimeSel');
    if(!dv){ds.innerHTML='<option value="">Pick a date first</option>';ts.innerHTML='<option value="">Pick a doctor first</option>';return;}
    ds.innerHTML='<option value="">Loading...</option>';
    ts.innerHTML='<option value="">Pick a doctor first</option>';
    fetch('http://localhost/clinic1/controller/GetDoctors.php?day='+encodeURIComponent(_dayName(dv)))
        .then(function(r){return r.json();})
        .then(function(docs){
            ds.innerHTML='<option value="">Select Doctor</option>';
            if(!docs.length){ds.innerHTML='<option value="">No doctors this day</option>';return;}
            docs.forEach(function(doc){
                var o=document.createElement('option');
                o.value=doc.id;
                o.text='Dr. '+doc.first_name+' '+doc.last_name;
                o.dataset.start=doc.schedule_time_start;
                o.dataset.end=doc.schedule_time_end;
                if(parseInt(doc.id)===_curDocId) o.selected=true;
                ds.appendChild(o);
            });
            editLoadTimes();
        });
}

function editLoadTimes(){
    var ds=document.getElementById('editDoctorSel');
    var ts=document.getElementById('editTimeSel');
    var sel=ds.options[ds.selectedIndex];
    if(!sel||!sel.value){ts.innerHTML='<option value="">Pick a doctor first</option>';return;}
    var sh=_t2h(sel.dataset.start),eh=_t2h(sel.dataset.end);
    ts.innerHTML='<option value="">Select Time</option>';
    for(var h=sh;h<eh;h++){
        var ho=h>12?h-12:(h===0?12:h),ap=h>=12?'PM':'AM';
        [':00',':30'].forEach(function(m){
            var lbl=ho+m+' '+ap;
            var o=document.createElement('option');
            o.value=lbl;o.text=lbl;
            if(lbl===_curTime) o.selected=true;
            ts.appendChild(o);
        });
    }
}

document.addEventListener('DOMContentLoaded', editLoadDoctors);
</script>

<script>
function toggleUserMenu(btn){
    var menu = btn.closest('.user-menu');
    var isOpen = menu.classList.contains('open');
    document.querySelectorAll('.user-menu.open').forEach(function(m){ m.classList.remove('open'); });
    if(!isOpen){ menu.classList.add('open'); }
}
document.addEventListener('click', function(e){
    if(!e.target.closest('.user-menu')){
        document.querySelectorAll('.user-menu.open').forEach(function(m){ m.classList.remove('open'); });
    }
});
</script>
</body>

</html>