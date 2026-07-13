// TABS 
function showDocTab(tabName) {
    var sections = ['today', 'upcoming', 'followup', 'all', 'completed'];
    sections.forEach(function(s) {
        var sec = document.getElementById(s + '-section');
        var btn = document.getElementById('tab-' + s);
        if (sec) sec.style.display = 'none';
        if (btn) btn.classList.remove('active');
    });
    var target = document.getElementById(tabName + '-section');
    var tabBtn = document.getElementById('tab-' + tabName);
    if (target) target.style.display = 'block';
    if (tabBtn) tabBtn.classList.add('active');
}

// MEDICINES 
function addMedicine() { 
    var c = document.getElementById('medicines-container'); 
    var r = document.createElement('div'); 
    r.className = 'medicine-row'; 
    r.innerHTML = '<input type="text" name="medicine_name[]" placeholder="Medicine Name">' +
        '<input type="text" name="dosage[]" placeholder="Dosage">' +
        '<input type="text" name="frequency[]" placeholder="Frequency">' +
        '<input type="text" name="duration[]" placeholder="Duration">' +
        '<button type="button" onclick="removeMedicine(this)" class="remove-btn">×</button>'; 
    c.appendChild(r); 
}

function removeMedicine(b) { 
    if (document.getElementById('medicines-container').children.length > 1) b.parentElement.remove(); 
}

// RECOMMENDATIONS 
function addRecommendation() {
    var c = document.getElementById('recommendations-container');
    var r = document.createElement('div');
    r.className = 'recommendation-row';
    r.innerHTML = '<input type="text" placeholder="Enter recommendation" name="recommendation[]">' +
        '<button type="button" onclick="removeRecommendation(this)" class="remove-btn">×</button>';
    c.appendChild(r);
}

function removeRecommendation(b) {
    if (document.getElementById('recommendations-container').children.length > 1) b.parentElement.remove();
}

// FOLLOW-UP DATE
function toggleFollowupDate() {
    var r = document.getElementsByName('followup_needed');
    var dc = document.getElementById('followup-date-container');
    var tc = document.getElementById('followup-time-container');
    var isYes = false;
    for (var i = 0; i < r.length; i++) {
        if (r[i].checked && r[i].value === 'yes') {
            isYes = true;
            break;
        }
    }
    dc.style.display = isYes ? 'block' : 'none';
    if (!isYes) tc.style.display = 'none';
}

// DATE TIME
function getDayName(d) {
    var da = new Date(d + 'T00:00:00');
    return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][da.getDay()];
}

function timeToHour(t) {
    if (!t) return 8;
    var p = t.match(/(\d+):(\d+)\s*(AM|PM)/i);
    if (!p) return 8;
    var h = parseInt(p[1]);
    var ap = p[3].toUpperCase();
    if (ap === 'PM' && h !== 12) h += 12;
    if (ap === 'AM' && h === 12) h = 0;
    return h;
}

// GENERATE FOLLOW-UP TIMES
function generateFollowupTimes() {
    var dv = document.getElementById('followupDate').value;
    var tc = document.getElementById('followup-time-container');
    var ts = document.getElementById('followupTime');
    
    if (!dv) {
        tc.style.display = 'none';
        return;
    }
    
    var dn = getDayName(dv);
    if (doctorScheduleDays.indexOf(dn) === -1) {
        tc.style.display = 'none';
        alert('The selected date (' + dn + ') is not a scheduled day for this doctor.');
        document.getElementById('followupDate').value = '';
        return;
    }
    
    var sh = timeToHour(doctorStartTime);
    var eh = timeToHour(doctorEndTime);
    ts.innerHTML = '<option value="">Select Time</option>';
    
    for (var h = sh; h < eh; h++) {
        var ho = h > 12 ? h - 12 : (h === 0 ? 12 : h);
        var ampm = h >= 12 ? 'PM' : 'AM';
        var sl = [':00', ':30'];
        for (var s = 0; s < sl.length; s++) {
            var la = ho + sl[s] + ' ' + ampm;
            var opt = document.createElement('option');
            opt.value = la;
            opt.text = la;
            ts.appendChild(opt);
        }
    }
    tc.style.display = 'block';
}

// DOCTOR CONS
document.addEventListener('DOMContentLoaded', function() {
    var fd = document.getElementById('followupDate');
    if (fd) fd.min = new Date().toISOString().split('T')[0];

    // Validate followup date+time before submit
    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            var radios = document.getElementsByName('followup_needed');
            var isYes = false;
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked && radios[i].value === 'yes') { isYes = true; break; }
            }
            if (isYes) {
                var dateVal = document.getElementById('followupDate') ? document.getElementById('followupDate').value : '';
                var timeEl = document.getElementById('followupTime');
                var timeVal = timeEl ? timeEl.value : '';
                if (!dateVal) {
                    e.preventDefault();
                    alert('Please select a follow-up date before completing the consultation.');
                    return false;
                }
                if (!timeVal) {
                    e.preventDefault();
                    alert('Please select a follow-up time before completing the consultation.');
                    return false;
                }
            }
        });
    }
});


