<?php
session_start();
require_once 'db_connection.php';

// Store the previous page if it's not already set
if (!isset($_SESSION['previous_page'])) {
    $_SESSION['previous_page'] = 'Men.php'; // Default to Men.php if no previous page
}

// Handle removing items from cart
if (isset($_POST['remove_item'])) {
    $index = $_POST['item_index'];
    if (isset($_SESSION['cart'][$index])) {
        // Get the product ID before removing from cart
        $product_id = $_SESSION['cart'][$index]['id'];
        
        // Remove from cart
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
        
        // Restore stock
        $stmt = $pdo->prepare("UPDATE products SET stock = stock + 1 WHERE product_id = ?");
        $stmt->execute([$product_id]);
    }
    header('Location: Cart.php');
    exit();
}

// Handle checkout
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $error = "Your cart is empty!";
    } else {
        // Here you would typically process the order
        // For now, we'll just clear the cart
        unset($_SESSION['cart']);
        $success = "Order placed successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="Fashioneer.css" rel="stylesheet" type="text/css">
    <title>Fashioneer - Shopping Cart</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <a href="<?php echo $_SESSION['previous_page']; ?>" class="back-button">← Back</a>
            <h2>Your Shopping Cart</h2>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div id="cart-items">
            <?php
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $total = 0;
                foreach ($_SESSION['cart'] as $index => $item) {
                    $total += $item['price'];
                    ?>
                    <div class="cart-item">
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p>RM<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <form method="post" action="">
                            <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                            <button type="submit" name="remove_item" class="remove-item">Remove</button>
                        </form>
                    </div>
                    <?php
                }
                ?>
                <div class="cart-total">
                    <p>Total: RM<?php echo number_format($total, 2); ?></p>
                    <form method="post" action="Payment.php">
                        <button type="submit" class="Button-Fill">Proceed to Checkout</button>
                    </form>
                </div>
                <?php
            } else {
                echo '<p>Your cart is empty.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html> 