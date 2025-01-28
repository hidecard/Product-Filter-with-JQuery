<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'your_database_name');

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST data
    $product_id = $_POST['product_id'];
    $customer_id = $_POST['customer_id'];
    $rating_value = $_POST['rating_value'];

    // Insert or update rating
    $sql = "INSERT INTO ratings (product_id, customer_id, rating_value, created_at, updated_at)
            VALUES ('$product_id', '$customer_id', '$rating_value', NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
            rating_value = '$rating_value', updated_at = NOW()";

    if ($conn->query($sql) === TRUE) {
        $message = "Rating saved successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Rating System</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <!-- Font Awesome for stars -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .star {
      font-size: 24px;
      color: #ccc;
      cursor: pointer;
    }

    .star.hovered,
    .star.selected {
      color: #ffc107;
    }
  </style>
</head>
<body class="bg-light py-4">

  <div class="container">
    <h1 class="text-center mb-4">Rate Our Products</h1>

    <div class="row g-4">
      <!-- Product 1 -->
      <div class="col-md-4">
        <div class="card">
          <img src="./img/water.jpeg" class="card-img-top" alt="Watermelon">
          <div class="card-body text-center">
            <h5 class="card-title">Watermelon</h5>
            <form action="rate_product.php" method="POST">
              <div class="rating" data-product-id="1">
                <i class="fas fa-star star" data-value="1"></i>
                <i class="fas fa-star star" data-value="2"></i>
                <i class="fas fa-star star" data-value="3"></i>
                <i class="fas fa-star star" data-value="4"></i>
                <i class="fas fa-star star" data-value="5"></i>
              </div>
              <input type="hidden" name="product_id" value="1">
              <input type="hidden" name="customer_id" value="123"> <!-- Example customer ID -->
              <input type="hidden" name="rating_value" id="rating_value" value="0">
              <p class="mt-2 text-muted">Your Rating: <span class="rating-value">0</span>/5</p>
              <button type="submit" class="btn btn-primary mt-3">Submit Rating</button>
            </form>
            <?php
              if (isset($message)) {
                echo "<p class='text-success'>$message</p>";
              }
            ?>
          </div>
        </div>
      </div>
      <!-- Add more products -->
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <script>
    $(document).ready(function () {
      let selectedRating = 0;

      // Hover effect for stars
      $('.star').hover(
        function () {
          $(this).prevAll().addBack().addClass('hovered'); // Add hovered class to current and previous stars
        },
        function () {
          $(this).prevAll().addBack().removeClass('hovered'); // Remove hovered class
        }
      );

      // Click event for rating
      $('.star').click(function () {
        selectedRating = $(this).data('value'); // Get the rating value
        const parent = $(this).closest('.rating');
        parent.find('.star').removeClass('selected'); // Clear previous selection
        $(this).prevAll().addBack().addClass('selected'); // Add selected class

        // Update the rating text
        parent.next('.text-muted').find('.rating-value').text(selectedRating);

        // Set the hidden input value for rating_value
        $('#rating_value').val(selectedRating);
      });
    });
  </script>

</body>
</html>
