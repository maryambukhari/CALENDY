<?php
include 'db.php';
$slug = mysqli_real_escape_string($conn, $_GET['slug']);
$sql = "SELECT id, username FROM users WHERE slug='$slug'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("User not found.");
}
$user = $result->fetch_assoc();
$user_id = $user['id'];
$username = $user['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booker_name = mysqli_real_escape_string($conn, $_POST['name']);
    $booker_email = mysqli_real_escape_string($conn, $_POST['email']);
    $date = $_POST['date'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    // Check if slot available (simple check, no overlap check for now)
    $check_sql = "SELECT * FROM bookings WHERE host_id=$user_id AND booking_date='$date' AND start_time='$start'";
    if ($conn->query($check_sql)->num_rows == 0) {
        $insert_sql = "INSERT INTO bookings (host_id, booker_name, booker_email, booking_date, start_time, end_time, status) VALUES ($user_id, '$booker_name', '$booker_email', '$date', '$start', '$end', 'confirmed')";
        if ($conn->query($insert_sql)) {
            // Send emails
            $host_sql = "SELECT email FROM users WHERE id=$user_id";
            $host_email = $conn->query($host_sql)->fetch_assoc()['email'];
            mail($host_email, "New Booking", "You have a new booking from $booker_name on $date at $start.");
            mail($booker_email, "Booking Confirmation", "Your booking with $username on $date at $start is confirmed.");
            echo "<script>alert('Booking confirmed!'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Error booking.');</script>";
        }
    } else {
        echo "<script>alert('Slot already booked.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book with <?php echo $username; ?> - ScheduleMeet</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: #fff; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        h1 { text-align: center; padding: 20px; }
        #calendar { max-width: 400px; margin: 20px auto; background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: center; padding: 10px; }
        td { cursor: pointer; transition: background 0.3s; }
        td:hover { background: rgba(255,255,255,0.2); }
        .available { background: #4caf50; }
        #slots { margin: 20px auto; max-width: 400px; }
        button.slot { display: block; width: 100%; padding: 10px; margin: 5px 0; background: #fff; color: #2575fc; border: none; border-radius: 5px; cursor: pointer; transition: transform 0.3s; }
        button.slot:hover { transform: scale(1.02); }
        form { max-width: 400px; margin: 20px auto; }
        input { width: 100%; padding: 10px; margin: 5px 0; border: none; border-radius: 5px; }
        #nav { text-align: center; margin: 10px; }
        button.nav { padding: 5px 10px; background: #fff; color: #2575fc; border: none; border-radius: 5px; cursor: pointer; }
        @media (max-width: 768px) { #calendar { max-width: 100%; } }
    </style>
</head>
<body>
    <h1>Book a Meeting with <?php echo $username; ?></h1>
    <div id="nav">
        <button class="nav" onclick="prevMonth()">Prev</button>
        <span id="monthYear"></span>
        <button class="nav" onclick="nextMonth()">Next</button>
    </div>
    <div id="calendar">
        <table>
            <thead><tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr></thead>
            <tbody id="calendarBody"></tbody>
        </table>
    </div>
    <div id="slots" style="display:none;">
        <h2>Available Slots on <span id="selectedDate"></span></h2>
        <div id="slotList"></div>
        <form id="bookForm" method="POST" style="display:none;">
            <input type="hidden" name="date" id="bookDate">
            <input type="hidden" name="start" id="bookStart">
            <input type="hidden" name="end" id="bookEnd">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <button type="submit">Confirm Booking</button>
        </form>
    </div>
    <script>
        let currentDate = new Date();
        const userId = <?php echo $user_id; ?>;
        
        function renderCalendar() {
            const monthYear = document.getElementById('monthYear');
            const calendarBody = document.getElementById('calendarBody');
            monthYear.textContent = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });
            calendarBody.innerHTML = '';
            
            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).getDay();
            const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
            
            let row = document.createElement('tr');
            for (let i = 0; i < firstDay; i++) {
                row.innerHTML += '<td></td>';
            }
            
            for (let day = 1; day <= daysInMonth; day++) {
                const td = document.createElement('td');
                td.textContent = day;
                td.onclick = () => selectDate(day);
                // To mark available, but since we fetch on click, no need here
                row.appendChild(td);
                if ((day + firstDay) % 7 === 0) {
                    calendarBody.appendChild(row);
                    row = document.createElement('tr');
                }
            }
            if (row.children.length > 0) calendarBody.appendChild(row);
        }
        
        function prevMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        }
        
        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        }
        
        function selectDate(day) {
            const date = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            document.getElementById('selectedDate').textContent = date;
            fetchSlots(date);
        }
        
        function fetchSlots(date) {
            fetch(`get_slots.php?user_id=${userId}&date=${date}`)
                .then(response => response.json())
                .then(slots => {
                    const slotList = document.getElementById('slotList');
                    slotList.innerHTML = '';
                    if (slots.length > 0) {
                        slots.forEach(slot => {
                            const btn = document.createElement('button');
                            btn.classList.add('slot');
                            btn.textContent = `${slot.start} - ${slot.end}`;
                            btn.onclick = () => selectSlot(date, slot.start, slot.end);
                            slotList.appendChild(btn);
                        });
                    } else {
                        slotList.innerHTML = '<p>No slots available.</p>';
                    }
                    document.getElementById('slots').style.display = 'block';
                });
        }
        
        function selectSlot(date, start, end) {
            document.getElementById('bookDate').value = date;
            document.getElementById('bookStart').value = start;
            document.getElementById('bookEnd').value = end;
            document.getElementById('bookForm').style.display = 'block';
        }
        
        renderCalendar();
    </script>
</body>
</html>
