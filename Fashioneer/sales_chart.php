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

// Fetch sales data for the chart
$sql = "SELECT 
            DATE_FORMAT(sale_date, '%Y-%m') as month,
            COUNT(*) as total_orders,
            SUM(total_item) as total_items,
            SUM(total_fee) as total_revenue
        FROM sales 
        GROUP BY DATE_FORMAT(sale_date, '%Y-%m')
        ORDER BY month DESC
        LIMIT 6";

$result = $conn->query($sql);

// Prepare data for the chart
$months = array();
$orders = array();
$items = array();
$revenue = array();

while($row = $result->fetch_assoc()) {
    $months[] = $row['month'];
    $orders[] = $row['total_orders'];
    $items[] = $row['total_items'];
    $revenue[] = $row['total_revenue'];
}

// Reverse arrays to show oldest to newest
$months = array_reverse($months);
$orders = array_reverse($orders);
$items = array_reverse($items);
$revenue = array_reverse($revenue);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analytics</title>
    <link href="Admin.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chart-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .chart-wrapper {
            width: 48%;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
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
        <h1>Sales Analytics</h1>
        
        <div class="chart-row">
            <div class="chart-wrapper">
                <h3>Sales Revenue</h3>
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="chart-wrapper">
                <h3>Number of Items Sold</h3>
                <canvas id="itemsChart"></canvas>
            </div>
        </div>
        
        <div class="chart-row">
            <div class="chart-wrapper">
                <h3>Number of Orders</h3>
                <canvas id="ordersChart"></canvas>
            </div>
            <div class="chart-wrapper">
                <h3>Average Order Value</h3>
                <canvas id="avgOrderChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Total Revenue ($)',
                    data: <?php echo json_encode($revenue); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Items Sold Chart
        new Chart(document.getElementById('itemsChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Total Items Sold',
                    data: <?php echo json_encode($items); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Orders Chart
        new Chart(document.getElementById('ordersChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Number of Orders',
                    data: <?php echo json_encode($orders); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Average Order Value Chart
        new Chart(document.getElementById('avgOrderChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Average Order Value ($)',
                    data: <?php 
                        $avgOrder = array();
                        for($i = 0; $i < count($revenue); $i++) {
                            $avgOrder[] = $orders[$i] > 0 ? $revenue[$i] / $orders[$i] : 0;
                        }
                        echo json_encode($avgOrder);
                    ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    
    <?php $conn->close(); ?>
</body>
</html> 