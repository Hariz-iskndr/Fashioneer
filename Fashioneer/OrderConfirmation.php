<?php
session_start();

// Check if there's an order in session
if (!isset($_SESSION['order'])) {
    header('Location: Cart.php');
    exit();
}

// Get order details
$order = $_SESSION['order'];
$shipping_address = $order['shipping_address'];
$payment_method = $order['payment_method'];
$items = $order['items'];
$total = $order['total'];

// Generate a random serial number (8 characters)
$serial_number = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

// Update sales record
require_once 'db_connection.php';

try {
    // Calculate total items
    $total_items = count($items);
    
    // Create customer details string
    $customer_details = "Order #" . $serial_number . " - " . $payment_method;
    
    // Insert into sales table
    $stmt = $pdo->prepare("INSERT INTO sales (customer_details, total_item, total_fee, shipping_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$customer_details, $total_items, $total, $shipping_address]);
    
    // Clear the cart and order after successful database update
    unset($_SESSION['cart']);
    unset($_SESSION['order']);
} catch (PDOException $e) {
    // Handle database error
    die("Error updating sales record: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Fashioneer</title>
    <link href="Fashioneer.css" rel="stylesheet" type="text/css">
</head>
<body>
    <nav>
        <div id="navbar">
            <div id="navbar-logo">
                <a href="Home.php"><img src="Fashioneer.png" alt="Logo" width="300" height="50"></a>
            </div>
            <div id="navbar-links">
                <ul>
                    <li><a href="About.php">About Us</a></li>
                    <li><a href="Men.php">Shop</a></li>
                    <li><a href="Location.php">Location</a></li>
                    <li><a href="Contact.php">Contact Us</a></li>
                </ul>
            </div>
            <button class="Button-Fill">
                <a href="Admin.php">Admin</a>
            </button>
        </div>
    </nav>

    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="confirmation-header">
                <h1>Order Confirmed!</h1>
                <p>Thank you for your purchase</p>
            </div>

            <div class="confirmation-details">
                <div class="detail-section">
                    <h2>Order Details</h2>
                    <div class="order-info">
                        <p><strong>Order Number:</strong> <?php echo $serial_number; ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                        <p><strong>Total Items:</strong> <?php echo $total_items; ?></p>
                    </div>
                </div>

                <div class="detail-section">
                    <h2>Order Summary</h2>
                    <div class="order-items">
                        <?php foreach ($items as $item): ?>
                            <div class="order-item">
                                <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                <span class="item-price">RM<?php echo number_format($item['price'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="order-total">
                            <span>Total Amount</span>
                            <span>RM<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h2>Shipping Details</h2>
                    <div class="shipping-info">
                        <p><strong>Delivery Address:</strong></p>
                        <p><?php echo nl2br(htmlspecialchars($shipping_address)); ?></p>
                    </div>
                </div>

                <div class="detail-section">
                    <h2>Payment Method</h2>
                    <div class="payment-info">
                        <?php
                        switch ($payment_method) {
                            case 'credit_card':
                                echo '<p>Credit Card Payment</p>';
                                break;
                            case 'qr_code':
                                echo '<p>QR Code Payment</p>';
                                break;
                            case 'cod':
                                echo '<p>Cash on Delivery</p>';
                                break;
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="confirmation-footer">
                <p>We'll send you an email with your order details and tracking information.</p>
                <p>Please keep your order number (<strong><?php echo $serial_number; ?></strong>) for future reference.</p>
                <a href="Men.php" class="continue-shopping">Continue Shopping</a>
            </div>
        </div>
    </div>
</body>
</html> 