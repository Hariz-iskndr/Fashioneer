<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="Admin.css" rel="stylesheet" type="text/css">
<style>
    .stock-chart {
        width: 100%;
        margin: 20px 0;
    }
    .chart-item {
        margin-bottom: 15px;
    }
    .chart-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    .chart-bar-container {
        width: 100%;
        height: 20px;
        background-color: #f0f0f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .chart-bar {
        height: 100%;
        background-color: #4CAF50;
        border-radius: 10px;
        transition: width 0.3s ease;
    }
    .low-stock .chart-bar {
        background-color: #ffc107;
    }
    .out-of-stock .chart-bar {
        background-color: #dc3545;
    }
    .stock-value {
        font-weight: bold;
    }
</style>
</head>

<body>
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

// Fetch product data
$sql = "SELECT product_name, stock FROM products ORDER BY stock DESC";
$result = $conn->query($sql);

// Find maximum stock for percentage calculation
$maxStock = 0;
$products = array();
while($row = $result->fetch_assoc()) {
    $products[] = $row;
    if ($row['stock'] > $maxStock) {
        $maxStock = $row['stock'];
    }
}
?>

<input type="checkbox" id="sidebar-toggle">

<div class="sidebar">
  <label for="sidebar-toggle" class="closebtn">×</label>
  
  <div class="menu-links">
    <a href="admin_page.php">Main Menu</a>
    <a href="Product.php">Product</a>
    <a href="Sales.php">Sales</a>
    <a href="user_management.php">Users Management</a>
  </div>
  
  <button class="bottom-button" onclick="window.location.href='Home.php'">Logout</button>
</div>

<div id="main">
  <label for="sidebar-toggle" class="openbtn">☰ Menu</label>
  
  <div class="container">
    <h2>Product Stock Levels</h2>
    <div class="stock-chart">
      <?php
      foreach ($products as $product) {
          $percentage = ($maxStock > 0) ? ($product['stock'] / $maxStock * 100) : 0;
          $statusClass = $product['stock'] > 10 ? "" : ($product['stock'] > 0 ? "low-stock" : "out-of-stock");
          echo "<div class='chart-item " . $statusClass . "'>";
          echo "<div class='chart-label'>";
          echo "<span>" . htmlspecialchars($product['product_name']) . "</span>";
          echo "<span class='stock-value'>" . $product['stock'] . "</span>";
          echo "</div>";
          echo "<div class='chart-bar-container'>";
          echo "<div class='chart-bar' style='width: " . $percentage . "%'></div>";
          echo "</div>";
          echo "</div>";
      }
      ?>
    </div>
  </div>
</div>
   
</body>
</html>