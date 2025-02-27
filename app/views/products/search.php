<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Výsledky vyhledávání</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <header>
        <h1>Výsledky vyhledávání</h1>
        <?php if(isset($_GET['q'])): ?>
            <p>Pro výraz: <strong><?= htmlspecialchars($_GET['q']); ?></strong></p>
        <?php endif; ?>
    </header>
    <main class="product-list">
        <?php if (empty($products)): ?>
            <p>Nenalezeny žádné produkty.</p>
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
