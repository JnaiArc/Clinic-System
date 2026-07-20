// Shared booking flow: Specialization -> Doctor -> Calendar (date w/ remaining slots) -> Time slot
// Expects a window.BookingConfig object to be defined on the page before this script runs:
// {
//   getDoctorsUrl:      'http://localhost/clinic1/controller/GetDoctors.php',
//   getAvailabilityUrl: 'http://localhost/clinic1/controller/GetAvailability.php',
//   uploadsPath:         '../../uploads/',
//   defaultAvatarPath:   '../../img/user.png',
//   dateInputId:         'appointmentDate',   // hidden input that holds the chosen date (YYYY-MM-DD)
//   timeInputId:         'appointmentTime',   // hidden input that holds the chosen time label
// }
(function () {
    const cfg = window.BookingConfig;
    if (!cfg) return;

    const specializationSelect = document.getElementById('specializationSelect');
    const doctorSelect = document.getElementById('doctorSelect');
    const doctorSummary = document.getElementById('doctorSummary');
    const doctorSummaryPhoto = document.getElementById('doctorSummaryPhoto');
    const doctorSummaryName = document.getElementById('doctorSummaryName');
    const doctorSummarySpec = document.getElementById('doctorSummarySpec');
    const calendarWrap = document.getElementById('bookingCalendar');
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarMonthLabel = document.getElementById('calendarMonthLabel');
    const calendarPrevBtn = document.getElementById('calendarPrevBtn');
    const calendarNextBtn = document.getElementById('calendarNextBtn');
    const timeSlotsWrap = document.getElementById('timeSlotsWrap');
    const timeSlotsGrid = document.getElementById('timeSlotsGrid');
    const selectedDateLabel = document.getElementById('selectedDateLabel');
    const dateHiddenInput = document.getElementById(cfg.dateInputId || 'appointmentDate');
    const timeHiddenInput = document.getElementById(cfg.timeInputId || 'appointmentTime');
    const submitBtn = document.getElementById('confirmBookingBtn');

    if (!specializationSelect || !doctorSelect || !calendarGrid) return;

    const today = new Date();
    const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
    let viewYear = today.getFullYear();
    let viewMonth = today.getMonth() + 1; // 1-12
    let selectedDate = null;

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    function resetDownstream(fromStep) {
        if (fromStep <= 1) {
            doctorSelect.innerHTML = '<option value="">Select a specialization first</option>';
            doctorSelect.disabled = true;
        }
        if (fromStep <= 2) {
            doctorSummary.classList.remove('show');
            calendarWrap.classList.remove('show');
            calendarGrid.innerHTML = '';
        }
        if (fromStep <= 3) {
            timeSlotsWrap.classList.remove('show');
            timeSlotsGrid.innerHTML = '';
            selectedDate = null;
            if (selectedDateLabel) selectedDateLabel.textContent = '';
        }
        if (dateHiddenInput) dateHiddenInput.value = '';
        if (timeHiddenInput) timeHiddenInput.value = '';
        if (submitBtn) submitBtn.disabled = true;
    }

    // STEP 1: Specialization -> load doctors under it
    specializationSelect.addEventListener('change', function () {
        resetDownstream(1);
        const spec = this.value;
        if (!spec) {
            doctorSelect.innerHTML = '<option value="">Select a specialization first</option>';
            return;
        }
        doctorSelect.innerHTML = '<option value="">Loading doctors...</option>';
        fetch(cfg.getDoctorsUrl + '?specialization=' + encodeURIComponent(spec))
            .then(function (r) { return r.json(); })
            .then(function (doctors) {
                if (!doctors || !doctors.length) {
                    doctorSelect.innerHTML = '<option value="">No doctors available under this specialization</option>';
                    return;
                }
                doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
                doctors.forEach(function (doc) {
                    const opt = document.createElement('option');
                    opt.value = doc.id;
                    opt.text = 'Dr. ' + doc.first_name + ' ' + doc.last_name;
                    opt.dataset.photo = doc.profile_photo || '';
                    opt.dataset.spec = doc.specialization || '';
                    opt.dataset.start = doc.schedule_time_start || '';
                    opt.dataset.end = doc.schedule_time_end || '';
                    opt.dataset.days = doc.schedule_days || '';
                    doctorSelect.appendChild(opt);
                });
                doctorSelect.disabled = false;
            })
            .catch(function () {
                doctorSelect.innerHTML = '<option value="">Failed to load doctors. Please try again.</option>';
            });
    });

    // STEP 2: Doctor -> show summary + calendar
    doctorSelect.addEventListener('change', function () {
        resetDownstream(2);
        const opt = this.options[this.selectedIndex];
        if (!this.value) return;

        if (doctorSummaryPhoto) {
            doctorSummaryPhoto.src = opt.dataset.photo ? (cfg.uploadsPath + opt.dataset.photo) : cfg.defaultAvatarPath;
        }
        if (doctorSummaryName) doctorSummaryName.textContent = opt.text;
        if (doctorSummarySpec) doctorSummarySpec.textContent = opt.dataset.spec || '';
        doctorSummary.classList.add('show');

        viewYear = today.getFullYear();
        viewMonth = today.getMonth() + 1;
        calendarWrap.classList.add('show');
        loadCalendar();
    });

    // STEP 3: Calendar (date + remaining slots)
    function loadCalendar() {
        calendarMonthLabel.textContent = monthNames[viewMonth - 1] + ' ' + viewYear;
        calendarGrid.innerHTML = '<div class="calendar-loading">Loading availability...</div>';

        const isCurrentMonth = (viewYear === today.getFullYear() && viewMonth === (today.getMonth() + 1));
        calendarPrevBtn.disabled = isCurrentMonth;

        const doctorId = doctorSelect.value;
        fetch(cfg.getAvailabilityUrl + '?action=calendar&doctor_id=' + encodeURIComponent(doctorId) + '&year=' + viewYear + '&month=' + viewMonth)
            .then(function (r) { return r.json(); })
            .then(function (data) { renderCalendar(data); })
            .catch(function () {
                calendarGrid.innerHTML = '<div class="calendar-empty-note">Could not load the calendar. Please try again.</div>';
            });
    }

    function renderCalendar(data) {
        calendarGrid.innerHTML = '';
        if (!data || !data.days) {
            calendarGrid.innerHTML = '<div class="calendar-empty-note">Could not load the calendar.</div>';
            return;
        }

        const firstOfMonth = new Date(viewYear, viewMonth - 1, 1);
        const leadingBlanks = firstOfMonth.getDay(); // 0 = Sunday

        for (let i = 0; i < leadingBlanks; i++) {
            const blank = document.createElement('div');
            blank.className = 'calendar-day-empty';
            calendarGrid.appendChild(blank);
        }

        Object.keys(data.days).forEach(function (dateStr) {
            const info = data.days[dateStr];
            const dayNum = parseInt(dateStr.split('-')[2], 10);

            const cell = document.createElement('div');
            cell.className = 'calendar-day';
            cell.dataset.date = dateStr;

            const disabled = info.past || !info.scheduled || info.remaining <= 0;
            if (disabled) cell.classList.add('is-disabled');

            const numEl = document.createElement('div');
            numEl.className = 'day-number';
            numEl.textContent = dayNum;
            cell.appendChild(numEl);

            const slotsEl = document.createElement('div');
            slotsEl.className = 'day-slots';
            if (!info.scheduled) {
                slotsEl.textContent = 'Closed';
            } else if (!info.past && info.remaining <= 0) {
                slotsEl.textContent = 'Full';
            } else if (!info.past) {
                slotsEl.textContent = info.remaining + ' left';
            }
            cell.appendChild(slotsEl);

            if (selectedDate === dateStr) cell.classList.add('is-selected');

            if (!disabled) {
                cell.addEventListener('click', function () {
                    document.querySelectorAll('.calendar-day.is-selected').forEach(function (el) { el.classList.remove('is-selected'); });
                    cell.classList.add('is-selected');
                    selectedDate = dateStr;
                    if (dateHiddenInput) dateHiddenInput.value = dateStr;
                    if (selectedDateLabel) selectedDateLabel.textContent = dateStr;
                    loadTimeSlots(dateStr);
                });
            }

            calendarGrid.appendChild(cell);
        });
    }

    if (calendarPrevBtn) {
        calendarPrevBtn.addEventListener('click', function () {
            if (this.disabled) return;
            viewMonth--;
            if (viewMonth < 1) { viewMonth = 12; viewYear--; }
            loadCalendar();
        });
    }

    if (calendarNextBtn) {
        calendarNextBtn.addEventListener('click', function () {
            viewMonth++;
            if (viewMonth > 12) { viewMonth = 1; viewYear++; }
            loadCalendar();
        });
    }

    // STEP 4: Time slots for the chosen date
    function loadTimeSlots(dateStr) {
        if (timeHiddenInput) timeHiddenInput.value = '';
        if (submitBtn) submitBtn.disabled = true;
        timeSlotsWrap.classList.add('show');
        timeSlotsGrid.innerHTML = '<div class="calendar-loading">Loading time slots...</div>';

        const doctorId = doctorSelect.value;
        fetch(cfg.getAvailabilityUrl + '?action=slots&doctor_id=' + encodeURIComponent(doctorId) + '&date=' + encodeURIComponent(dateStr))
            .then(function (r) { return r.json(); })
            .then(function (data) { renderTimeSlots(data); })
            .catch(function () {
                timeSlotsGrid.innerHTML = '<div class="time-slots-empty">Could not load time slots. Please try again.</div>';
            });
    }

    function renderTimeSlots(data) {
        timeSlotsGrid.innerHTML = '';
        if (!data || !data.slots || !data.slots.length) {
            timeSlotsGrid.innerHTML = '<div class="time-slots-empty">No time slots available for this date.</div>';
            return;
        }

        data.slots.forEach(function (slot) {
            const el = document.createElement('div');
            el.className = 'time-slot';
            el.textContent = slot.time;
            if (!slot.available) {
                el.classList.add('is-taken');
            } else {
                el.addEventListener('click', function () {
                    document.querySelectorAll('.time-slot.is-selected').forEach(function (s) { s.classList.remove('is-selected'); });
                    el.classList.add('is-selected');
                    if (timeHiddenInput) timeHiddenInput.value = slot.time;
                    if (submitBtn) submitBtn.disabled = false;
                });
            }
            timeSlotsGrid.appendChild(el);
        });
    }

    // Initial state
    resetDownstream(1);
})();
