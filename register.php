<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>ShopNeo | Register</title>
</head>
<body>
<div class="form-container">
    <h2>Daftar Akun ShopNeo</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form action="proses-register.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Daftar</button>
        <p>Sudah punya akun? <a href="login.php">Login</a></p>
    </form>
</div>
<script src="login.js"></script>
</body>
</html>