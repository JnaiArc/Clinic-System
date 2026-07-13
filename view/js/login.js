// Show/Hide Password
function togglePw(id, btn) {
    var inp = document.getElementById(id);
    if (inp.type === 'password') {
        inp.type = 'text';
        btn.style.color = '#02529c';
    } else {
        inp.type = 'password';
        btn.style.color = '#64748b';
    }
}

// REGISTER: show role-specific fields
function showFields() {
    var role = document.getElementById('role');
    if (!role) return;
    var adminFields   = document.getElementById('adminFields');
    var doctorFields  = document.getElementById('doctorFields');
    var usernameInput = document.getElementById('usernameInput');
    var licenseInput  = document.getElementById('licenseInput');

    if (role.value === 'admin') {
        if (adminFields)  adminFields.style.display  = 'block';
        if (doctorFields) doctorFields.style.display = 'none';
        if (usernameInput) usernameInput.setAttribute('required', 'required');
        if (licenseInput)  { licenseInput.removeAttribute('required'); licenseInput.value = ''; }
    } else if (role.value === 'doctor') {
        if (adminFields)  adminFields.style.display  = 'none';
        if (doctorFields) doctorFields.style.display = 'block';
        if (licenseInput)  licenseInput.setAttribute('required', 'required');
        var ts = document.getElementById('timeStart');
        var te = document.getElementById('timeEnd');
        if (ts) ts.setAttribute('required', 'required');
        if (te) te.setAttribute('required', 'required');
        if (usernameInput) { usernameInput.removeAttribute('required'); usernameInput.value = ''; }
    }
}
