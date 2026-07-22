<?php
// Centralized lists for dropdowns/checkboxes that are reused across multiple pages.
class DropdownOptions {
    // Doctor specialization dropdown
    const SPECIALIZATIONS = [
        'Pediatrics',
        'General Medicine',
        'Internal Medicine',
        'Obstetrics & Gynecology',
    ];

    // Short explanation shown beside each specialization so patients know which one to pick
    const SPECIALIZATION_DESCRIPTIONS = [
        'Pediatrics'               => 'Care for infants, children, and teens',
        'General Medicine'         => 'General check-ups and common illnesses',
        'Internal Medicine'        => 'Diagnosis and treatment of adult diseases',
        'Obstetrics & Gynecology'  => "Women's health, pregnancy, and reproductive care",
    ];

    // Safely look up a specialization's short description (falls back to empty string)
    public static function specializationDescription($specialization) {
        return self::SPECIALIZATION_DESCRIPTIONS[$specialization] ?? '';
    }

    // Appointment "Purpose" dropdown
    const PURPOSES = [
        'Check-up',
        'Consultation',
        'Follow-up',
    ];

    // Appointment "Consultation Type" dropdown
    const CONSULTATION_TYPES = [
        'In Person',
        'Online',
    ];

    // Doctor "Schedule Days" checkboxes
    const SCHEDULE_DAYS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    // Doctor "Start Time" dropdown
    const SCHEDULE_TIME_START = [
        '7:00 AM',
        '8:00 AM',
        '9:00 AM',
        '10:00 AM',
        '11:00 AM',
    ];

    // Doctor "End Time" dropdown
    const SCHEDULE_TIME_END = [
        '12:00 PM',
        '1:00 PM',
        '2:00 PM',
        '3:00 PM',
        '4:00 PM',
        '5:00 PM',
        '6:00 PM',
        '7:00 PM',
        '8:00 PM',
    ];
}