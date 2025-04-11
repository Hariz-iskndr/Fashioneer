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

// Process form submission for adding new product
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    // Handle image upload
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $image_name = basename($_FILES["product_image"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if image file is valid
    $uploadOk = 1;
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["product_image"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "File is not an image.";
            $uploadOk = 0;
        }
    }
    
    // Check file size
    if ($_FILES["product_image"]["size"] > 500000) {
        $error = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO products (product_name, category, price, stock, product_image) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdis", $product_name, $category, $price, $stock, $image_name);
            
            if ($stmt->execute()) {
                $message = "New product added successfully";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }
}

// Process form submission for updating product
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    // Handle image update if a new image is uploaded
    if (!empty($_FILES["product_image"]["name"])) {
        $target_dir = "uploads/";
        $image_name = basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $sql = "UPDATE products SET product_name=?, category=?, price=?, stock=?, product_image=? WHERE product_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdiss", $product_name, $category, $price, $stock, $image_name, $product_id);
        }
    } else {
        $sql = "UPDATE products SET product_name=?, category=?, price=?, stock=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdis", $product_name, $category, $price, $stock, $product_id);
    }
    
    if ($stmt->execute()) {
        $message = "Product updated successfully";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Process deletion if requested
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    
    // First, get the image name to delete it
    $sql = "SELECT product_image FROM products WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Delete the image file
    if ($row && $row['product_image']) {
        $image_path = "uploads/" . $row['product_image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete the product from database
    $sql = "DELETE FROM products WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        $message = "Product deleted successfully";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch products to display in table
$sql = "SELECT product_id, product_name, category, price, stock, product_image FROM products";
$result = $conn->query($sql);

// Define categories
$categories = array(
    "Men",
    "Women",
    "Accessories",
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
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
        <h1>Product Management</h1>
        
        <?php if(isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Add New Product Form -->
        <div class="add-form">
            <h2>Add New Product</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name:</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select a category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Price (RM):</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="product_image">Product Image:</label>
                    <input type="file" id="product_image" name="product_image" accept="image/*" required>
                </div>
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>
        
        <!-- Display Products Table -->
        <h2>Products List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['product_id']; ?></td>
                            <td>
                                <?php if($row['product_image']): ?>
                                    <img src="uploads/<?php echo $row['product_image']; ?>" alt="Product Image" class="product-image">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td>RM<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo $row['stock']; ?></td>
                            <td class="action-links">
                                <a href="?edit=<?php echo $row['product_id']; ?>#edit-form">Edit</a>
                                <a href="?delete=<?php echo $row['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No products found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Edit Product Form (shown only when edit is clicked) -->
        <?php
        if(isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $edit_sql = "SELECT product_id, product_name, category, price, stock, product_image FROM products WHERE product_id=?";
            $edit_stmt = $conn->prepare($edit_sql);
            $edit_stmt->bind_param("i", $edit_id);
            $edit_stmt->execute();
            $edit_result = $edit_stmt->get_result();
            
            if($edit_row = $edit_result->fetch_assoc()) {
        ?>
            <div class="edit-form" id="edit-form">
                <h2>Edit Product</h2>
                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $edit_row['product_id']; ?>">
                    <div class="form-group">
                        <label for="edit_product_name">Product Name:</label>
                        <input type="text" id="edit_product_name" name="product_name" value="<?php echo htmlspecialchars($edit_row['product_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_category">Category:</label>
                        <select id="edit_category" name="category" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo ($cat == $edit_row['category']) ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_price">Price:</label>
                        <input type="number" id="edit_price" name="price" step="0.01" value="<?php echo $edit_row['price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_stock">Stock:</label>
                        <input type="number" id="edit_stock" name="stock" value="<?php echo $edit_row['stock']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_product_image">Product Image (leave empty to keep current):</label>
                        <input type="file" id="edit_product_image" name="product_image" accept="image/*">
                        <?php if($edit_row['product_image']): ?>
                            <p>Current image: <?php echo $edit_row['product_image']; ?></p>
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="update_product">Update Product</button>
                </form>
            </div>
        <?php
            }
            $edit_stmt->close();
        }
        ?>
    </div>
    
    <?php $conn->close(); ?>
</body>
</html>
