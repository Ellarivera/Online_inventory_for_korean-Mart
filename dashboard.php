<?php
session_start();
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

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $productTypesStmt = $pdo->query("SELECT * FROM product_type");
    $productTypes = $productTypesStmt->fetchAll(PDO::FETCH_ASSOC);

    $selectedProductType = isset($_GET['product_type']) ? $_GET['product_type'] : '';

    $whereClause = '';
    $params = ['userId' => $userId];
    if (!empty($selectedProductType)) {
        $whereClause = ' AND p.product_type_id = :productTypeId';
        $params['productTypeId'] = $selectedProductType;
    }

    $orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'date_inserted'; 

    // gang eneka mag if else kumaba yamu
    switch ($orderBy) {
        case 'newest':
            $orderByClause = 'p.date_inserted DESC';
            break;
        case 'oldest':
            $orderByClause = 'p.date_inserted ASC';
            break;
        default:
            $orderByClause = 'p.date_inserted';
    }

    $stmt = $pdo->prepare("SELECT p.*, pt.type_name FROM products p
            INNER JOIN users u ON p.user_id = u.id
            INNER JOIN product_type pt ON p.product_type_id = pt.id
            WHERE u.id = :userId $whereClause
            ORDER BY $orderByClause");

    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['logout'])) {
        $_SESSION = array();
    
        session_destroy();
    
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Products</title>
</head>
<body>
    <div class="side-bar">
        <h2>Products</h2>
        <form action="" method="post">
            <input type="submit" name="logout" value="Logout">
        </form>
        
        <form action="dashboard.php" method="get">
            <label for="product_type">Filter by Product Type:</label><br>
            <select name="product_type" id="product_type">
                <option value="">All</option>
                <?php foreach ($productTypes as $type): ?>
                    <option value="<?php echo $type['id']; ?>" <?php if ($selectedProductType == $type['id']) echo 'selected'; ?>>
                        <?php echo $type['type_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Filter">
        </form>

        <a class="sort" href="dashboard.php?orderby=newest">Sort by Newest</a><br>
        <a class="sort" href="dashboard.php?orderby=oldest">Sort by Oldest</a><br>
        <a class="add-new-btn" href="add_product.php">Add New Product</a><br>

    </div>
    <div class="records">
        
    <?php 
    if (isset($_SESSION['message'])) {
        echo "<p>{$_SESSION['message']}</p>";
        unset($_SESSION['message']);
    }
    if (count($products) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Food Name</th>
                    <th>Quantity</th>
                    <th>Date Inserted</th>
                    <th>Product Type</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['Food_name']; ?></td>
                        <td><?php echo $product['Quantity']; ?></td>
                        <td><?php echo $product['date_inserted']; ?></td>
                        <td><?php echo $product['type_name']; ?></td>
                        <td class="btn" ><a class="update" href="update_product.php?id=<?php echo $product['id']; ?>">Update</a></td>
                        <td class="btn" ><a class="delete" href="delete_product.php?id=<?php echo $product['id']; ?>">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No products found for this user.</p>
    <?php endif; ?>
    </div>

    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    display: flex;
}

.side-bar {
    flex: 1;
    background: linear-gradient(to bottom, rgb(48, 72, 180), rgb(71, 98, 213));
    height: 100vh;
    padding: 20px;
    box-sizing: border-box;
}

.records {
    flex: 5;
    padding: 20px;
    box-sizing: border-box;
}

.sort {
    color: #ddd;
    text-decoration: none;
    display: block;
    margin-bottom: 10px;
}

.sort:hover {
    color: white;
}

h2 {
    color: white;
}

form {
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    color: white;
}

select,
input[type="submit"] {
    padding: 8px;
    margin-right: 10px;
}
.add-new-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #28a745; /* Green color, you can customize it */
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
}

.add-new-btn:hover {
    background-color: #218838; /* Darker green color on hover */
    color: white;
}


a {
    text-decoration: none;
    color: #007bff;
    margin-right: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 10px;
    border-top: 1px solid #ddd; /* Add this line to create top border */
    border-bottom: 1px solid #ddd; /* Add this line to create bottom border */
    text-align: left;
}

th {
    border-bottom: 2px solid #007bff; /* Customize the bottom border of header cells */
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #e2e2e2;
}

.btn {
    /* Add any common button styles here */
    text-align: center; /* Align text within the button to center */
}

.update,
.delete {
    padding: 8px 16px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
    display: block; /* Make the button a block element for centering */
    margin: 0 auto; /* Automatically center the block element */
    font-weight: bold;
}

.update {
    background: #9BBEC8;
    color: white;
}

.delete {
    background: red;
    color: white;
}

.update:hover,
.delete:hover {
    background: white;
    color: black;
}


.logout-form {
    margin-top: 20px;
}

.logout-btn {
    display: inline-block;
    padding: 8px 16px;
    background: red;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}

.logout-btn:hover {
    background: darkred;
}

    </style>
</body>
</html>
