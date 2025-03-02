<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'Seznam produktů' ?></title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <header>
        <h1>
            <?php if (isset($_GET['category'])): ?>
                <?= htmlspecialchars($_GET['category']) ?>
            <?php else: ?>
                Naše produkty
            <?php endif; ?>
        </h1>
    </header>
    <main class="product-list">
        <?php if (empty($products)): ?>
            <p>V této kategorii nejsou žádné produkty.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <h2><?= htmlspecialchars($product['name']) ?></h2>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p><strong>Cena: <?= number_format($product['price'], 2) ?> Kč</strong></p>
                    <a href="/mprojekt/public/products/show?id=<?= $product['id'] ?>" class="btn">Zobrazit detail</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>
