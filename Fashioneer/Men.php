<?php
session_start();
?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <link href="Fashioneer.css" rel="stylesheet" type="text/css">
        <title>Fashioneer - Men's Collection</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <nav>
            <div id="navbar">
                <div id="navbar-logo">
                    <a href="Home.php"><img src="Fashioneer.png" alt="Logo" width="300" height="50"></a>
                </div>
                <div id="navbar-links">
                    <ul>
                        <li>
                            <a href="About.php">About Us</a>
                        </li>
                        <li>
                            <a href="Men.php">Shop</a>
                        </li>
                        <li>
                            <a href="Location.php">Location</a>
                        </li>
                        <li>
                            <a href="Contact.php">Contact Us</a>
                        </li>
                    </ul>
                </div>
                <button class="Button-Fill">
                    <a href="Admin.php">Admin</a>
                </button>
            </div>
        </nav>

        <div class="mini-nav">
            <div class="nav-container">
                <a href="Men.php" class="nav-item active">Men</a>
                <a href="Women.php" class="nav-item">Women</a>
                <a href="Accessories.php" class="nav-item">Accessories</a>
                <a href="Cart.php" class="cart-button">Cart (<span id="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>)</a>
            </div>
        </div>

        <div class="products-grid">
            <?php
            require_once 'db_connection.php';
            
            // Handle adding to cart
            if (isset($_POST['add_to_cart'])) {
                $product_id = $_POST['product_id'];
                $product_name = $_POST['product_name'];
                $product_price = $_POST['product_price'];
                
                // Check if product exists and has stock
                $stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product && $product['stock'] > 0) {
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = array();
                    }
                    
                    // Add to cart
                    $_SESSION['cart'][] = array(
                        'id' => $product_id,
                        'name' => $product_name,
                        'price' => $product_price
                    );
                    
                    // Decrease stock
                    $stmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE product_id = ?");
                    $stmt->execute([$product_id]);
                    
                    // Set the previous page
                    $_SESSION['previous_page'] = 'Men.php';
                    
                    // Redirect back to the same page to show updated cart count
                    header('Location: Men.php');
                    exit();
                }
            }
            
            try {
                $stmt = $pdo->prepare("SELECT * FROM products WHERE category = 'Men'");
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($products as $product) {
                    $isOutOfStock = $product['stock'] <= 0;
                    echo '<div class="product-card' . ($isOutOfStock ? ' out-of-stock' : '') . '">';
                    echo '<img src="uploads/' . htmlspecialchars($product['product_image']) . '" alt="' . htmlspecialchars($product['product_name']) . '">';
                    echo '<h3>' . htmlspecialchars($product['product_name']) . '</h3>';
                    echo '<p class="price">RM' . number_format($product['price'], 2) . '</p>';
                    if ($isOutOfStock) {
                        echo '<div class="sold-out-overlay">SOLD OUT</div>';
                    } else {
                        echo '<form method="post" action="">';
                        echo '<input type="hidden" name="product_id" value="' . $product['product_id'] . '">';
                        echo '<input type="hidden" name="product_name" value="' . htmlspecialchars($product['product_name']) . '">';
                        echo '<input type="hidden" name="product_price" value="' . $product['price'] . '">';
                        echo '<button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>';
                        echo '</form>';
                    }
                    echo '</div>';
                }
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </div>
    </body>
</html>