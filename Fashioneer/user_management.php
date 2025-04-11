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

// Process form submission for adding new user
if (isset($_POST['add_user'])) {
    $new_username = $_POST['new_username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hash the password
    
    $sql = "INSERT INTO admin_users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_username, $new_password);
    
    if ($stmt->execute()) {
        $message = "New user added successfully";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Process form submission for updating user
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin_users SET username=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $hashed_password, $user_id);
    } else {
        $sql = "UPDATE admin_users SET username=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $username, $user_id);
    }
    
    if ($stmt->execute()) {
        $message = "User updated successfully";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Process deletion if requested
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    $sql = "DELETE FROM admin_users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $message = "User deleted successfully";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch users to display in table
$sql = "SELECT id, username FROM admin_users"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
        <h1>User Management</h1>
        
        <?php if(isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Add New User Form -->
        <div class="add-form">
            <h2>Add New User</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="new_username">Username:</label>
                    <input type="text" id="new_username" name="new_username" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <button type="submit" name="add_user">Add User</button>
            </form>
        </div>
        
        <!-- Display Users Table -->
        <h2>Users List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="action-links">
                                <a href="?edit=<?php echo $row['id']; ?>#edit-form">Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Edit User Form (shown only when edit is clicked) -->
        <?php
        if(isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $edit_sql = "SELECT id, username FROM admin_users WHERE id=?";
            $edit_stmt = $conn->prepare($edit_sql);
            $edit_stmt->bind_param("i", $edit_id);
            $edit_stmt->execute();
            $edit_result = $edit_stmt->get_result();
            
            if($edit_row = $edit_result->fetch_assoc()) {
        ?>
            <div class="edit-form" id="edit-form">
                <h2>Edit User</h2>
                <form method="post" action="">
                    <input type="hidden" name="user_id" value="<?php echo $edit_row['id']; ?>">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($edit_row['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password (leave blank to keep current):</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>
                    <button type="submit" name="update_user">Update User</button>
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