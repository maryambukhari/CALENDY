<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $username)); // Generate slug

    // Check unique
    $check = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email' OR slug='$slug'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Username, email, or slug already exists.');</script>";
    } else {
        $sql = "INSERT INTO users (username, email, password, slug) VALUES ('$username', '$email', '$password', '$slug')";
        if ($conn->query($sql)) {
            echo "<script>window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ScheduleMeet</title>
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
        <h2>Sign Up</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
