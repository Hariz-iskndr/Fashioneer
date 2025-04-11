<?php
session_start();

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: Cart.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and process the form data
    $shipping_address = $_POST['shipping_address'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    
    if (empty($shipping_address) || empty($payment_method)) {
        $error = "Please fill in all required fields";
    } else {
        // Process the order based on payment method
        $_SESSION['order'] = [
            'shipping_address' => $shipping_address,
            'payment_method' => $payment_method,
            'items' => $_SESSION['cart'],
            'total' => array_sum(array_column($_SESSION['cart'], 'price'))
        ];
        
        // Always redirect to OrderConfirmation.php
        header('Location: OrderConfirmation.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Fashioneer</title>
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

    <div class="payment-container">
        <h1>Payment & Shipping</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="" class="payment-form">
            <div class="form-section">
                <h2>Shipping Address</h2>
                <div class="form-group">
                    <label for="shipping_address">Full Address *</label>
                    <textarea id="shipping_address" name="shipping_address" rows="4" required></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2>Payment Method</h2>
                <div class="payment-options">
                    <div class="payment-option">
                        <input type="radio" id="credit_card" name="payment_method" value="credit_card" required>
                        <label for="credit_card">
                            <img src="credit_card.png" alt="Credit Card">
                            <span>Credit Card</span>
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" id="qr_code" name="payment_method" value="qr_code">
                        <label for="qr_code">
                            <img src="qr.png" alt="QR Code">
                            <span>QR Code</span>
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" id="cod" name="payment_method" value="cod">
                        <label for="cod">
                            <img src="cash.png" alt="Cash on Delivery">
                            <span>Cash on Delivery</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="summary-items">
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item) {
                        $total += $item['price'];
                        echo '<div class="summary-item">';
                        echo '<span>' . htmlspecialchars($item['name']) . '</span>';
                        echo '<span>RM' . number_format($item['price'], 2) . '</span>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span>RM<?php echo number_format($total, 2); ?></span>
                </div>
            </div>

            <div class="form-actions">
                <a href="Cart.php" class="back-button">Back to Cart</a>
                <button type="submit" class="proceed-button">Proceed to Payment</button>
            </div>
        </form>
    </div>
</body>
</html> 