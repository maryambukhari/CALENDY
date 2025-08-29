<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
$user_id = $_SESSION['user_id'];

// Get user slug
$user_sql = "SELECT slug FROM users WHERE id=$user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();
$slug = $user['slug'];

// Get bookings
$bookings_sql = "SELECT * FROM bookings WHERE host_id=$user_id ORDER BY booking_date DESC, start_time DESC";
$bookings_result = $conn->query($bookings_sql);

// Upcoming notifications: bookings in future with pending/confirmed
$upcoming_sql = "SELECT * FROM bookings WHERE host_id=$user_id AND booking_date >= CURDATE() AND status IN ('pending', 'confirmed') ORDER BY booking_date ASC, start_time ASC LIMIT 5";
$upcoming_result = $conn->query($upcoming_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ScheduleMeet</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: #fff; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        header { text-align: center; padding: 20px; }
        h1, h2 { text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .links { display: flex; justify-content: center; gap: 20px; margin: 20px; }
        a.btn { padding: 10px 20px; background: #fff; color: #2575fc; border-radius: 20px; text-decoration: none; transition: transform 0.3s; }
        a.btn:hover { transform: scale(1.05); }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.2); }
        th { background: rgba(0,0,0,0.2); }
        button { padding: 5px 10px; background: #ff4d4d; color: #fff; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #cc0000; }
        .edit-btn { background: #4d79ff; }
        .edit-btn:hover { background: #3366ff; }
        .notifications { max-width: 800px; margin: 20px auto; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 10px; }
        ul { list-style: none; padding: 0; }
        li { padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.2); animation: fadeInList 0.5s ease-in; }
        @keyframes fadeInList { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        @media (max-width: 768px) { table { width: 100%; font-size: 0.8em; } .links { flex-direction: column; } }
    </style>
</head>
<body>
    <header>
        <h1>Your Dashboard</h1>
        <p>Your booking link: <a href="book.php?slug=<?php echo $slug; ?>" target="_blank">schedulemeet/book.php?slug=<?php echo $slug; ?></a></p>
        <div class="links">
            <a href="set_availability.php" class="btn">Set Availability</a>
            <a href="logout.php" class="btn">Log Out</a>
        </div>
    </header>
    <h2 style="text-align: center;">Upcoming Notifications</h2>
    <div class="notifications">
        <ul>
            <?php if ($upcoming_result->num_rows > 0): ?>
                <?php while ($up = $upcoming_result->fetch_assoc()): ?>
                    <li><?php echo $up['booker_name']; ?> booked on <?php echo $up['booking_date']; ?> from <?php echo $up['start_time']; ?> to <?php echo $up['end_time']; ?> (Status: <?php echo $up['status']; ?>)</li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No upcoming meetings.</li>
            <?php endif; ?>
        </ul>
    </div>
    <h2 style="text-align: center;">Your Bookings</h2>
    <table>
        <tr><th>ID</th><th>Booker</th><th>Email</th><th>Date</th><th>Start</th><th>End</th><th>Status</th><th>Actions</th></tr>
        <?php if ($bookings_result->num_rows > 0): ?>
            <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $booking['id']; ?></td>
                    <td><?php echo $booking['booker_name']; ?></td>
                    <td><?php echo $booking['booker_email']; ?></td>
                    <td><?php echo $booking['booking_date']; ?></td>
                    <td><?php echo $booking['start_time']; ?></td>
                    <td><?php echo $booking['end_time']; ?></td>
                    <td><?php echo $booking['status']; ?></td>
                    <td>
                        <button onclick="cancelBooking(<?php echo $booking['id']; ?>)">Cancel</button>
                        <button class="edit-btn" onclick="rescheduleBooking(<?php echo $booking['id']; ?>)">Reschedule</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No bookings yet.</td></tr>
        <?php endif; ?>
    </table>
    <script>
        function cancelBooking(id) {
            if (confirm('Cancel this booking?')) {
                window.location.href = `cancel.php?id=${id}`;
            }
        }
        function rescheduleBooking(id) {
            const newDate = prompt('Enter new date (YYYY-MM-DD):');
            const newStart = prompt('Enter new start time (HH:MM):');
            const newEnd = prompt('Enter new end time (HH:MM):');
            if (newDate && newStart && newEnd) {
                window.location.href = `reschedule.php?id=${id}&date=${newDate}&start=${newStart}&end=${newEnd}`;
            }
        }
    </script>
</body>
</html>
