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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$db_name", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $productName = $_POST['product_name'];
        $productQuantity = $_POST['product_quantity'];
        $productTypeId = $_POST['product_type']; 

        $stmt = $pdo->prepare("INSERT INTO products (Food_name, Quantity, date_inserted, product_type_id, user_id) 
                                VALUES (:product_name, :product_quantity, NOW(), :product_type_id, :user_id)");
        $stmt->bindParam(':product_name', $productName);
        $stmt->bindParam(':product_quantity', $productQuantity);
        $stmt->bindParam(':product_type_id', $productTypeId);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();

        $_SESSION['message'] = "Record Added successfully!";
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
</head>
<body>
    <div class="container">
        <h2>Add Product</h2>
        <div class="form-container">
            <form action="add_product.php" method="post">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required><br><br>
                <label for="product_quantity">Quantity:</label>
                <input type="text" id="product_quantity" name="product_quantity" required><br><br>
                <label for="product_type">Product Type:</label>
                <select name="product_type" id="product_type">
                    
                    <?php
                    try {
                        $pdo = new PDO("mysql:host=$servername;dbname=$db_name", $username, $password);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $productTypesStmt = $pdo->query("SELECT * FROM product_type");
                        $productTypes = $productTypesStmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($productTypes as $type) {
                            echo "<option value='" . $type['id'] . "'>" . $type['type_name'] . "</option>";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                    ?>
                </select><br><br>
                <input type="submit" name="add_product" value="Add Product">
            </form>
        </div>
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
        .form-container select{
            border: none;
            border-bottom: 1px solid #fff;
            background: transparent;
            outline: none;
            width: 250px;
            height: 40px;
            color: white;
            font-size: 16px;
        }
        .form-container select {
            color: white;
        }
        .form-container option{
            background-color: rgb(71, 98, 213);
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
