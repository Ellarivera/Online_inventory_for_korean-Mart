<?php
session_start();
if (isset($_SESSION['message'])) {
    echo "<p>{$_SESSION['message']}</p>";
    unset($_SESSION['message']);
}
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "inventory";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $enteredPassword = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $enteredPassword === $user['password']) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $errorMessage = "Password verification failed. Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if(isset($errorMessage)) { ?>
            <p><?php echo $errorMessage; ?></p>
        <?php } ?>
        <div class="form-container">
            <form action="login.php" method="post">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required><br><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br><br>
                <input type="submit" value="Login">
            </div>
        </form>
    </div>
    <style>
        *{
            font-family: Arial, Helvetica, sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            height: 100vh;
        }

        .container {
            display: flex;
            flex-direction: column;
            background: linear-gradient(to bottom, rgb(48, 72, 180), rgb(71, 98, 213));
            align-items: center;
            height: 400px;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2, label {
            color: #fff;
        }

        .form-container input[type="text"],
        .form-container input[type="password"] {
            border: none;
            border-bottom: 1px solid #fff;
            background: transparent;
            outline: none;
            width: 250px;
            height: 40px;
            color: white;
            font-size: 16px;

        }

        .form-container input[type="text"]:focus,
        .form-container input[type="password"]:focus {
            background: transparent;
            color: white;
        }

        .form-container input[type="submit"] {
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
            display: block;
            margin: 0 auto;
            font-weight: bold;
            background: #9BBEC8;
            color: white;
        }

        .form-container input[type="submit"]:hover {
            background: white;
            color: black;
        }
    </style>
</body>
</html>
