<?php
session_start();
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            echo "<script>window.location.href = 'dashboard.php';</script>";
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('No user found.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - ScheduleMeet</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: #fff; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        form { max-width: 400px; margin: 100px auto; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        input { display: block; width: 100%; padding: 15px; margin: 10px 0; border: none; border-radius: 10px; }
        button { width: 100%; padding: 15px; background: #fff; color: #2575fc; border: none; border-radius: 10px; cursor: pointer; transition: background 0.3s, transform 0.3s; }
        button:hover { background: #e0e0e0; transform: scale(1.02); }
        h2 { text-align: center; animation: slideIn 1s ease-out; }
        @keyframes slideIn { from { transform: translateY(-50px); } to { transform: translateY(0); } }
        @media (max-width: 768px) { form { margin: 50px 20px; } }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Log In</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Log In</button>
    </form>
</body>
</html>
