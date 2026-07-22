// Restricts what characters can actually be typed into certain form fields, based on the
// input's `name` attribute. Include this script on any page with these fields and it will
// auto-attach — no per-page wiring needed.
//
//   first_name / last_name             -> letters, spaces, hyphens, apostrophes only
//   phone / emergency_contact          -> digits only (max 11 characters)
//   username                           -> letters, numbers, underscore only
//   license_number                    -> letters, numbers, dash only
//
(function () {
    function stripDisallowed(el, regex) {
        var start = el.selectionStart, end = el.selectionEnd;
        var original = el.value;
        var filtered = original.replace(regex, '');
        if (filtered !== original) {
            var removedBeforeCaret = original.slice(0, start).replace(regex, '').length;
            el.value = filtered;
            var pos = removedBeforeCaret;
            try { el.setSelectionRange(pos, pos); } catch (e) { /* some input types don't support this */ }
        }
    }

    var RULES = [
        { names: ['first_name', 'last_name'], regex: /[^A-Za-z\s\-']/g },
        { names: ['phone', 'emergency_contact'], regex: /[^0-9]/g, maxLength: 11 },
        { names: ['username'], regex: /[^A-Za-z0-9_]/g, maxLength: 30 },
        { names: ['license_number'], regex: /[^A-Za-z0-9\-]/g }
    ];

    function attach(el, rule) {
        if (el.dataset.restrictAttached) return; // avoid double-binding
        el.dataset.restrictAttached = '1';

        el.addEventListener('input', function () {
            stripDisallowed(el, rule.regex);
            if (rule.maxLength && el.value.length > rule.maxLength) {
                el.value = el.value.slice(0, rule.maxLength);
            }
        });

        el.addEventListener('paste', function () {
            setTimeout(function () {
                stripDisallowed(el, rule.regex);
                if (rule.maxLength && el.value.length > rule.maxLength) {
                    el.value = el.value.slice(0, rule.maxLength);
                }
            }, 0);
        });
    }

    function init() {
        RULES.forEach(function (rule) {
            rule.names.forEach(function (name) {
                document.querySelectorAll('input[name="' + name + '"]').forEach(function (el) {
                    attach(el, rule);
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
