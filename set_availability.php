<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete old availability
    $conn->query("DELETE FROM availability WHERE user_id=$user_id");
    
    // Insert new
    for ($day = 0; $day < 7; $day++) {
        if (isset($_POST["day_$day"])) {
            $start = $_POST["start_$day"];
            $end = $_POST["end_$day"];
            if ($start && $end) {
                $sql = "INSERT INTO availability (user_id, day_of_week, start_time, end_time) VALUES ($user_id, $day, '$start', '$end')";
                $conn->query($sql);
            }
        }
    }
    echo "<script>alert('Availability updated!'); window.location.href = 'dashboard.php';</script>";
}

// Get current availability
$avail_sql = "SELECT * FROM availability WHERE user_id=$user_id";
$avail_result = $conn->query($avail_sql);
$avail = [];
while ($row = $avail_result->fetch_assoc()) {
    $avail[$row['day_of_week']] = ['start' => $row['start_time'], 'end' => $row['end_time']];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Availability - ScheduleMeet</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: #fff; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        form { max-width: 600px; margin: 50px auto; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        label { display: block; margin: 10px 0; }
        input[type="checkbox"] { margin-right: 10px; }
        input[type="time"] { padding: 5px; border-radius: 5px; border: none; }
        button { width: 100%; padding: 15px; background: #fff; color: #2575fc; border: none; border-radius: 10px; cursor: pointer; transition: background 0.3s, transform 0.3s; margin-top: 20px; }
        button:hover { background: #e0e0e0; transform: scale(1.02); }
        h2 { text-align: center; }
        .day-group { margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; }
        @media (max-width: 768px) { form { margin: 20px; padding: 10px; } }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Set Your Availability</h2>
        <?php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; ?>
        <?php for ($day = 0; $day < 7; $day++): ?>
            <div class="day-group">
                <label>
                    <input type="checkbox" name="day_<?php echo $day; ?>" <?php echo isset($avail[$day]) ? 'checked' : ''; ?>>
                    <?php echo $days[$day]; ?>
                </label>
                Start: <input type="time" name="start_<?php echo $day; ?>" value="<?php echo $avail[$day]['start'] ?? ''; ?>">
                End: <input type="time" name="end_<?php echo $day; ?>" value="<?php echo $avail[$day]['end'] ?? ''; ?>">
            </div>
        <?php endfor; ?>
        <button type="submit">Save Availability</button>
    </form>
</body>
</html>
