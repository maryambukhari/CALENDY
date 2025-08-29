<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScheduleMeet - Easy Scheduling Like Calendly</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: #fff; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        header { text-align: center; padding: 50px 20px; }
        h1 { font-size: 3em; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); animation: slideIn 1s ease-out; }
        @keyframes slideIn { from { transform: translateY(-50px); } to { transform: translateY(0); } }
        p { font-size: 1.2em; max-width: 800px; margin: 20px auto; }
        .buttons { display: flex; justify-content: center; gap: 20px; margin: 30px 0; }
        button, a.btn { padding: 15px 30px; background: #fff; color: #2575fc; border: none; border-radius: 50px; font-size: 1em; cursor: pointer; transition: transform 0.3s, box-shadow 0.3s; text-decoration: none; }
        button:hover, a.btn:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .book-form { text-align: center; margin: 20px; }
        input { padding: 10px; width: 300px; border-radius: 20px; border: none; margin-right: 10px; }
        @media (max-width: 768px) { .buttons { flex-direction: column; } input { width: 100%; margin-bottom: 10px; } }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to ScheduleMeet</h1>
        <p>Schedule meetings effortlessly. Set your availability, share your link, and let others book time with you. Just like Calendly, but custom-built!</p>
        <div class="buttons">
            <a href="signup.php" class="btn">Sign Up</a>
            <a href="login.php" class="btn">Log In</a>
        </div>
        <div class="book-form">
            <p>Book a meeting with someone? Enter their username:</p>
            <input type="text" id="slug" placeholder="e.g., john_doe">
            <button onclick="redirectToBook()">Book Now</button>
        </div>
    </header>
    <script>
        function redirectToBook() {
            const slug = document.getElementById('slug').value.trim();
            if (slug) {
                window.location.href = `book.php?slug=${slug}`;
            } else {
                alert('Please enter a username.');
            }
        }
    </script>
</body>
</html>
