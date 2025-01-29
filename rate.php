<?php
$conn = new mysqli('localhost', 'root', 'hidecard', 'testrate');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $customer_id = $_POST['customer_id'];
    $rating_value = $_POST['rating_value'];

    $sql = "INSERT INTO ratings (product_id, customer_id, rating_value, created_at, updated_at)
            VALUES ('$product_id', '$customer_id', '$rating_value', NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
            rating_value = '$rating_value', updated_at = NOW()";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>
