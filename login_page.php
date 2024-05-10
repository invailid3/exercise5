<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form action="./login.php" method="POST">
        <div class="input-field">
            <label for="field-login">Login: </label>
            <input type="text" name="login" id="field-login">
        </div>
        <div class="input-field">
            <label for="field-password">Password: </label>
            <input type="password" name="password" id="field-password">
        </div>
        <button type="submit">Submit</button>
    </form>
</body>
</html>