<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam produktů</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <header>
        <h1>Naše produkty</h1>
    </header>
    <main class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p><?= htmlspecialchars($product['description']) ?></p>
                <p><strong>Cena: <?= number_format($product['price'], 2) ?> Kč</strong></p>
                <a href="/mprojekt/public/products/show?id=<?= $product['id'] ?>" class="btn">Zobrazit detail</a>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>
