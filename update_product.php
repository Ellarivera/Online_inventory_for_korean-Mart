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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$db_name", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $productId = $_POST['product_id'];
        $productName = $_POST['product_name'];
        $productType = $_POST['product_type'];
        $productQuantity = $_POST['product_quantity'];

        $stmt = $pdo->prepare("UPDATE products SET Food_name = :product_name, product_type_id = :product_type, Quantity = :product_quantity, date_updated = NOW() WHERE id = :product_id");
        $stmt->bindParam(':product_name', $productName);
        $stmt->bindParam(':product_type', $productType);
        $stmt->bindParam(':product_quantity', $productQuantity);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();

        $_SESSION['message'] = "Record Updated successfully!";
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
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
        
        $stmt = $pdo->prepare("SELECT p.*, pt.type_name FROM products p
                INNER JOIN product_type pt ON p.product_type_id = pt.id
                WHERE p.id = :product_id");
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            echo "Product not found.";
            exit();
        }

       
        $productTypesStmt = $pdo->query("SELECT * FROM product_type");
        $productTypes = $productTypesStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Product</title>
</head>
<body>
    <div class="container">
        <h2>Update Product</h2>
        <div class="form-container">
            <form action="update_product.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <label for="product_name">Product Name:</label><br>
                <input type="text" id="product_name" name="product_name" value="<?php echo $product['Food_name']; ?>"><br><br>
                
                <label for="product_type">Product Type:</label><br>
                <select name="product_type" id="product_type">
                    <?php foreach ($productTypes as $type): ?>
                        <option value="<?php echo $type['id']; ?>" <?php if ($product['product_type_id'] == $type['id']) echo 'selected'; ?>>
                            <?php echo $type['type_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>
                
                <label for="product_quantity">Quantity:</label><br>
                <input type="text" id="product_quantity" name="product_quantity" value="<?php echo $product['Quantity']; ?>"><br><br>

                <input type="submit" name="update_product" value="Update Product">
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
