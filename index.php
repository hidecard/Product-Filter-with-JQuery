<?php
// Database connection
$conn = new mysqli('localhost', 'root', 'hidecard', 'testrate');

$customer_id = 2;

$sql = "
    SELECT p.*,
           COALESCE(AVG(r.rating_value), 0) AS avg_rating,
           COALESCE(cr.rating_value, 0) AS user_rating
    FROM product p
    LEFT JOIN ratings r ON p.id = r.product_id
    LEFT JOIN ratings cr ON p.id = cr.product_id AND cr.customer_id = $customer_id
    GROUP BY p.id
";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Rating System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .star { font-size: 24px; color: #ccc; cursor: pointer; }
        .star.selected, .star.hovered { color: #ffc107; }
        .disabled { pointer-events: none; opacity: 0.5; }
    </style>
</head>
<body class="bg-light py-4">
    <div class="container">
        <h1 class="text-center mb-4">Rate Our Products</h1>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="./img/<?= htmlspecialchars($product['img']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($product['category']) ?></p>
                        <p class="fw-bold"><?= number_format($product['price']) ?> MMK</p>
                        <div class="rating" data-product-id="<?= $product['id'] ?>">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star star <?= ($i <= $product['user_rating']) ? 'selected' : '' ?>" data-value="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="mt-2 text-muted">Your Rating: <span class="rating-value"><?= $product['user_rating'] ?: 0 ?></span>/5</p>
                        <p class="mt-2"><strong>Average Rating: <?= round($product['avg_rating'], 1) ?>/5</strong></p>
                        <form action="rate.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="customer_id" value="<?= $customer_id ?>">
                            <input type="hidden" name="rating_value" class="rating_value" value="<?= $product['user_rating'] ?>">
                            <button type="submit" class="btn btn-primary mt-3 <?= $product['user_rating'] ? 'disabled' : '' ?>">Submit Rating</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.star').hover(
                function () { $(this).prevAll().addBack().addClass('hovered'); },
                function () { $(this).prevAll().addBack().removeClass('hovered'); }
            );

            $('.star').click(function () {
                let selectedRating = $(this).data('value');
                let parent = $(this).closest('.rating');
                parent.find('.star').removeClass('selected');
                $(this).prevAll().addBack().addClass('selected');
                parent.siblings('.text-muted').find('.rating-value').text(selectedRating);
                parent.siblings('form').find('.rating_value').val(selectedRating);
            });
        });
    </script>
</body>
</html>
