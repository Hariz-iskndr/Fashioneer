<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Fashioneer";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch sales data
$sql = "SELECT sales_id, customer_details, total_item, total_fee, shipping_address FROM sales ORDER BY sales_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management</title>
    <link href="Admin.css" rel="stylesheet" type="text/css">
</head>
<body>

<input type="checkbox" id="sidebar-toggle">

<div class="sidebar">
  <label for="sidebar-toggle" class="closebtn">×</label>
  
  <div class="menu-links">
    <a href="admin_page.php">Main Menu</a>
    <a href="product.php">Product</a>
    <a href="Sales.php">Sales</a>
    <a href="user_management.php">Users Management</a>
  </div>
  
  <button class="bottom-button" onclick="window.location.href='Home.php'">Logout</button>
</div>

<div id="main">
  <label for="sidebar-toggle" class="openbtn">☰ Menu</label>
</div>

    <div class="container">
        <h1>Sales Management</h1>
        
        <!-- Display Sales Table -->
        <h2>Sales Records</h2>
        <table>
            <thead>
                <tr>
                    <th>Sales ID</th>
                    <th>Customer Details</th>
                    <th>Total Items</th>
                    <th>Total Fee</th>
                    <th>Shipping Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['sales_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['customer_details']); ?></td>
                            <td><?php echo $row['total_item']; ?></td>
                            <td>RM<?php echo number_format($row['total_fee'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No sales records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php $conn->close(); ?>
</body>
</html>
