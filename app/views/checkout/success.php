<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Objednávka dokončena</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <h1>Děkujeme za vaši objednávku!</h1>

    <?php if (isset($_GET['order_id'])): ?>
        <p>Číslo vaší objednávky je: <strong><?= htmlspecialchars($_GET['order_id']) ?></strong></p>
    <?php endif; ?>

    <p>Brzy vás budeme kontaktovat s dalšími informacemi o doručení.</p>

    <a href="/mprojekt/public/products" class="btn btn-primary">Zpět na produkty</a>
</body>
</html>
