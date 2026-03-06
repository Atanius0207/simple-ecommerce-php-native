<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>ShopNeo | Login</title>
</head>
<body>
    <div class="form-container">
        <h2>Login ShopNeo</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="proses-login.php" method="post">
            <input type="text" name="username" placeholder="Username Anda" Required>
            <input type="password" name="password" placeholder="Password Anda" required>
            <button type="submit">Login</button>
            <p>Belum Punya Akun? <a href="register.php">Daftar</a></p>
        </form>
    </div>
<script src="login.js"></script>
</body>
</html>