<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "inventory";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$productId = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :product_id");
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();
    $_SESSION['message'] = "Record Deleted successfully!";
    header("Location: dashboard.php");
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
