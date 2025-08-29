<?php
include 'db.php';
$user_id = intval($_GET['user_id']);
$date = $_GET['date'];
$day_of_week = date('w', strtotime($date)); // 0=Sun, etc.

$sql = "SELECT start_time, end_time FROM availability WHERE user_id=$user_id AND day_of_week=$day_of_week";
$result = $conn->query($sql);

$slots = [];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // For simplicity, assume one slot per day, but could generate intervals if needed
    $slots[] = ['start' => $row['start_time'], 'end' => $row['end_time']];
}

header('Content-Type: application/json');
echo json_encode($slots);
?>
