<?php
require_once __DIR__ . '/../../../app/models/Product.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<script>alert("Neplatné ID produktu."); window.location.href="/mprojekt/public/products";</script>';
    exit();
}

$productId = (int) $_GET['id'];
$product = Product::getProductById($productId);

if (!$product) {
    echo '<script>alert("Produkt nenalezen."); window.location.href="/mprojekt/public/products";</script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> | Detail produktu</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>

<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <section class="product-detail-container">
        <div class="product-hero">
            <div class="product-image-container">
                <img class="product-image"
                    src="/mprojekt/public/assets/images/<?= htmlspecialchars($product['image'] ?? 'default.png') ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <div class="product-info">
                <h1 class="product-name"><?= htmlspecialchars($product['name']) ?></h1>

                <?php if (isset($product['category']) && !empty($product['category'])): ?>
                    <div class="product-category">
                        <?= htmlspecialchars($product['category']) ?>
                    </div>
                <?php endif; ?>

                <div class="product-description">
                    <?= htmlspecialchars($product['description']) ?>
                </div>

                <div class="product-price">
                    <?= number_format($product['price'], 0, ',', ' ') ?> Kč
                </div>
                <div class="actions">
                    <form action="/mprojekt/public/cart/add" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="quantity-selector">
                            <label for="quantity">Množství:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="10">
                        </div>
                        <button type="submit" class="btn btn-primary">Přidat do košíku</button>
                    </form>
                    <a href="/mprojekt/public/products" class="btn btn-secondary">Zpět na seznam produktů</a>
                </div>
            </div>
        </div>

        <div class="related-products">
            <h2>Mohlo by vás také zajímat</h2>

            <div class="product-grid">
                <?php
                // Get a list of other products
                $otherProducts = Product::getAllProducts();
                $displayed = 0;

                // Show up to 4 other products
                foreach ($otherProducts as $relatedProduct):
                    // Skip current product
                    if ($relatedProduct['id'] == $productId)
                        continue;
                    if ($displayed >= 4)
                        break;
                    $displayed++;
                    ?>
                    <div class="product-card">
                        <img src="/mprojekt/public/assets/images/<?= htmlspecialchars($relatedProduct['image'] ?? 'default.png') ?>"
                            alt="<?= htmlspecialchars($relatedProduct['name']) ?>">
                        <h3><?= htmlspecialchars($relatedProduct['name']) ?></h3>
                        <p class="price"><?= number_format($relatedProduct['price'], 0, ',', ' ') ?> Kč</p>
                        <a href="/mprojekt/public/products/show?id=<?= $relatedProduct['id'] ?>"
                            class="btn btn-primary">Zobrazit detail</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</body>

</html>