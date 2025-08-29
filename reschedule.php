<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit;
}
include 'db.php';
$id = intval($_GET['id']);
$date = mysqli_real_escape_string($conn, $_GET['date']);
$start = mysqli_real_escape_string($conn, $_GET['start']);
$end = mysqli_real_escape_string($conn, $_GET['end']);
$user_id = $_SESSION['user_id'];

// Simple update, no conflict check for demo
$sql = "UPDATE bookings SET booking_date='$date', start_time='$start', end_time='$end' WHERE id=$id AND host_id=$user_id";
if ($conn->query($sql)) {
    // Notify booker
    $booking_sql = "SELECT booker_email FROM bookings WHERE id=$id";
    $booker_email = $conn->query($booking_sql)->fetch_assoc()['booker_email'];
    mail($booker_email, "Booking Rescheduled", "Your booking has been rescheduled to $date at $start - $end.");
}
?>
<script>
window.location.href = 'dashboard.php';
</script>
