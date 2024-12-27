<?php
// Start session
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <div class="login-container">
        <div class="form-container">
            <div class="image-content">
                <img src="images/6963.jpg" alt="Image" class="login-image">
            </div>
            <div class="form-content">
                <h2>Hello again!</h2>
                <p class="welcome-text">Welcome back, you've been missed</p>

                <!-- Display error message if credentials are invalid -->
                <?php if (!empty($error_message)): ?>
                    <p style="color: red;"><?php echo $error_message; ?></p>
                <?php endif; ?>

                <form action="loggin.php" method="POST">
                    <input type="text" name="username" placeholder="Username" class="input-field" required>
                    <input type="password" name="password" placeholder="Password" class="input-field" required>

                    <a href="#" class="forgot-password">Forgot your password?</a>

                    <button type="submit" class="btn-get-started">Get Started</button>
                </form>

                <p class="sign-up-text">Not a member? <a href="signup.php">Sign up</a></p>
            </div>
        </div>
    </div>
</body>
</html>
