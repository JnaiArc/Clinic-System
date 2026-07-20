<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/model/User.php';
require_once 'c:/xampp/htdocs/clinic1/model/Appointment.php';

header('Content-Type: application/json');

$database = new Database();
$conn = $database->connect();
$userModel = new User($conn);
$appointmentModel = new Appointment($conn);

$action = $_GET['action'] ?? '';
$doctor_id = $_GET['doctor_id'] ?? 0;

$doctor = $doctor_id ? $userModel->getUserById($doctor_id) : null;

if (!$doctor || $doctor['role'] !== 'doctor') {
    echo json_encode(['error' => 'Invalid doctor']);
    exit();
}

$scheduleDays = array_filter(array_map('trim', explode(',', $doctor['schedule_days'] ?? '')));
$slotLabels = Appointment::generateSlots($doctor['schedule_time_start'], $doctor['schedule_time_end']);
$totalSlots = count($slotLabels);
$today = date('Y-m-d');

// === CALENDAR: remaining-slot count per date for a given month, for the "big calendar" view
if ($action === 'calendar') {
    $year = (int)($_GET['year'] ?? date('Y'));
    $month = (int)($_GET['month'] ?? date('n'));
    if ($month < 1 || $month > 12) $month = (int)date('n');

    $firstDay = sprintf('%04d-%02d-01', $year, $month);
    $numDays = (int)date('t', strtotime($firstDay));
    $lastDay = sprintf('%04d-%02d-%02d', $year, $month, $numDays);

    $bookedCounts = $appointmentModel->getBookedCountsForDoctorRange($doctor_id, $firstDay, $lastDay);

    $days = [];
    for ($d = 1; $d <= $numDays; $d++) {
        $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
        $dayName = date('l', strtotime($dateStr));
        $isScheduled = in_array($dayName, $scheduleDays);
        $booked = $bookedCounts[$dateStr] ?? 0;
        $remaining = $isScheduled ? max(0, $totalSlots - $booked) : 0;

        $days[$dateStr] = [
            'scheduled'  => $isScheduled,
            'total'      => $totalSlots,
            'booked'     => $booked,
            'remaining'  => $remaining,
            'past'       => $dateStr < $today,
        ];
    }

    echo json_encode([
        'year' => $year,
        'month' => $month,
        'total_slots' => $totalSlots,
        'days' => $days,
    ]);
    exit();
}

// === SLOTS: time-slot availability for a chosen date
if ($action === 'slots') {
    $date = $_GET['date'] ?? '';
    if (!$date) {
        echo json_encode(['error' => 'Missing date']);
        exit();
    }

    $dayName = date('l', strtotime($date));
    $isScheduled = in_array($dayName, $scheduleDays);

    if (!$isScheduled || $date < $today) {
        echo json_encode(['scheduled' => false, 'slots' => []]);
        exit();
    }

    $bookedTimes = $appointmentModel->getBookedTimesForDoctorDate($doctor_id, $date);

    $result = [];
    foreach ($slotLabels as $label) {
        $result[] = [
            'time' => $label,
            'available' => !in_array($label, $bookedTimes),
        ];
    }

    echo json_encode(['scheduled' => true, 'slots' => $result]);
    exit();
}

echo json_encode(['error' => 'Unknown action']);
