// TABS
function showTab(tabName) {
    var sections = ['today', 'upcoming', 'followup', 'all', 'completed'];
    sections.forEach(function(s) {
        var sec = document.getElementById(s + '-section');
        var btn = document.getElementById('tab-' + s);
        if (sec) { sec.classList.remove('show'); sec.style.display = ''; }
        if (btn) btn.classList.remove('active');
    });
    var target = document.getElementById(tabName + '-section');
    var tabBtn = document.getElementById('tab-' + tabName);
    if (target) { target.classList.add('show'); }
    if (tabBtn) tabBtn.classList.add('active');
}

// LOGOUT
function confirmLogout() {
    return confirm("Are you sure you want to logout?");
}

// DATE TIME
function getDayName(dateStr) {
    const date = new Date(dateStr);
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return days[date.getDay()];
}

function timeToHour(timeStr) {
    if (!timeStr) return 8;
    var parts = timeStr.match(/(\d+):(\d+)\s*(AM|PM)/i);
    if (!parts) return 8;
    var h = parseInt(parts[1]);
    var ap = parts[3].toUpperCase();
    if (ap === 'PM' && h !== 12) h += 12;
    if (ap === 'AM' && h === 12) h = 0;
    return h;
}

function timeToNumber(time) {
    const match = time.match(/(\d+):(\d+)\s+(AM|PM)/i);
    if (!match) return 0;
    let hour = parseInt(match[1]);
    const ampm = match[3].toUpperCase();
    if (ampm === 'PM' && hour !== 12) hour += 12;
    if (ampm === 'AM' && hour === 12) hour = 0;
    return hour;
}

function generateTimeSlots(startTime, endTime) {
    const slots = [];
    const start = parseInt(startTime);
    const end = parseInt(endTime);
    
    for (let i = start; i < end; i++) {
        const hour = i > 12 ? i - 12 : i;
        const ampm = i >= 12 ? 'PM' : 'AM';
        slots.push(hour + ':00 ' + ampm);
        slots.push(hour + ':30 ' + ampm);
    }
    return slots;
}

// VIEW APPOINTMENT FOLLOW-UP
function handleDoctorChange() {
    handleDateChange();
}

function handleDateChange() {
    var docSelect = document.getElementById('doctorSelect');
    var dateInput = document.getElementById('appointmentDate');
    var timeSelect = document.getElementById('appointmentTime');
    
    var selectedOption = docSelect.options[docSelect.selectedIndex];
    var scheduleDays = JSON.parse(selectedOption.getAttribute('data-days'));
    var startTime = selectedOption.getAttribute('data-start');
    var endTime = selectedOption.getAttribute('data-end');
    var selectedDate = dateInput.value;
    var currentTimeVal = "<?php echo isset($appointment_data['appointment_time']) ? $appointment_data['appointment_time'] : ''; ?>";

    timeSelect.innerHTML = '<option value="">Select Time</option>';

    if (!selectedDate) return;

    var dayName = getDayName(selectedDate);
    
    if (scheduleDays.indexOf(dayName) === -1) {
        alert('The selected date (' + dayName + ') is not a scheduled day for this doctor. Scheduled days: ' + scheduleDays.join(', ') + '.');
    } else {
        var startH = timeToHour(startTime);
        var endH = timeToHour(endTime);

        for (var h = startH; h < endH; h++) {
            var hour = h > 12 ? h - 12 : (h === 0 ? 12 : h);
            var ampm = h >= 12 ? 'PM' : 'AM';
            var slots = [':00', ':30'];
            
            for (var s = 0; s < slots.length; s++) {
                var timeLabel = hour + slots[s] + ' ' + ampm;
                var opt = document.createElement('option');
                opt.value = timeLabel;
                opt.text = timeLabel;
                
                if (timeLabel === currentTimeVal) {
                    opt.selected = true;
                }
                
                timeSelect.appendChild(opt);
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointmentDate');
    if (dateInput) {
        handleDateChange();
    }
});

// VIEW FOLLOW-UP EDIT 
function updateRxStyle(checkbox) {
    const row = checkbox.closest('.rx-item-edit');
    if (checkbox.checked) {
        row.classList.add('is-checked');
    } else {
        row.classList.remove('is-checked');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.rx-item-edit input[type="checkbox"]').forEach(function(cb) {
        updateRxStyle(cb);
        cb.addEventListener('change', function() { updateRxStyle(this); });
    });
});

// FOLLOW-UP EDIT
function initFollowUpEdit() {
    const dateInput = document.getElementById('appointmentDate');
    if (dateInput) {
        dateInput.min = new Date().toISOString().split('T')[0];
        dateInput.onchange = generateFollowUpTimeSlots;
        generateFollowUpTimeSlots();
    }
}

function generateFollowUpTimeSlots() {
    const dateInput = document.getElementById('appointmentDate');
    const timeSelect = document.getElementById('appointmentTime');
    
    if (!dateInput || !timeSelect) return;
    
    const selectedDate = dateInput.value;
    if (!selectedDate) return;
    
    const dayName = getDayName(selectedDate);
    
    // Check if selected day is in doctor's schedule
    if (window.doctorScheduleDays.indexOf(dayName) === -1) {
        alert('The selected date (' + dayName + ') is not a scheduled day for this doctor. Scheduled days: ' + window.doctorScheduleDays.join(', '));
        dateInput.value = ''; // Clear the date
        timeSelect.innerHTML = '<option value="">Select Time</option>';
        return;
    }
    
    const startH = timeToHour(window.doctorStartTime);
    const endH = timeToHour(window.doctorEndTime);
    const currentTime = window.currentTimeVal || "";
    
    timeSelect.innerHTML = '<option value="">Select Time</option>';
    
    for (let h = startH; h < endH; h++) {
        const hour = h > 12 ? h - 12 : (h === 0 ? 12 : h);
        const ampm = h >= 12 ? 'PM' : 'AM';
        const slots = [':00', ':30'];
        
        for (let s = 0; s < slots.length; s++) {
            const timeLabel = hour + slots[s] + ' ' + ampm;
            const opt = document.createElement('option');
            opt.value = timeLabel;
            opt.text = timeLabel;
            
            if (timeLabel === currentTime) {
                opt.selected = true;
            }
            
            timeSelect.appendChild(opt);
        }
    }
}

document.addEventListener('DOMContentLoaded', initFollowUpEdit);



