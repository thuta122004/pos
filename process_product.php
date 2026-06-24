<?php
require_once 'dbconnect.php';

$target_dir = "uploads/";

if (isset($_POST['add'])) {
    $image_db_value = "default.png";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_db_value = $image_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, price, stock_quantity, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $_POST['name'], $_POST['price'], $_POST['stock'], $image_db_value);
    $stmt->execute();
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $add = (int)$_POST['add_stock'];
    
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ? WHERE product_id = ?");
    $stmt->bind_param("sdi", $name, $price, $id);
    $stmt->execute();
    
    if (isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] === UPLOAD_ERR_OK) {
        $new_image_name = time() . '_' . basename($_FILES["edit_image"]["name"]);
        $target_file = $target_dir . $new_image_name;
        
        if (move_uploaded_file($_FILES["edit_image"]["tmp_name"], $target_file)) {
            $query = $conn->prepare("SELECT image_path FROM products WHERE product_id = ?");
            $query->bind_param("i", $id);
            $query->execute();
            $result = $query->get_result()->fetch_assoc();
            
            if ($result && !empty($result['image_path']) && $result['image_path'] !== 'default.png') {
                $old_file = $target_dir . $result['image_path'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $stmt = $conn->prepare("UPDATE products SET image_path = ? WHERE product_id = ?");
            $stmt->bind_param("si", $new_image_name, $id);
            $stmt->execute();
        }
    }
    
    if ($add > 0) {
        $stmt = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?");
        $stmt->bind_param("ii", $add, $id);
        $stmt->execute();
    }
}

if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("UPDATE products SET is_active = 0 WHERE product_id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
}

header("Location: products.php");
exit();
?>