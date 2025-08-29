<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit;
}
include 'db.php';
$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$sql = "UPDATE bookings SET status='cancelled' WHERE id=$id AND host_id=$user_id";
$conn->query($sql);
?>
<script>
window.location.href = 'dashboard.php';
</script>
