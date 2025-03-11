<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../models/Product.php';

// Získání seznamu produktů
$products = Product::getAllProducts();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
<div class="adminpanel">
    <div class="container">
        <h1>Admin Panel</h1>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert"><?= htmlspecialchars($_SESSION['message']); ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <h2>Přidat nový produkt</h2>
        <form action="/mprojekt/public/admin/create" method="POST" enctype="multipart/form-data">
            <label for="name">Název:</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Popis:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="price">Cena:</label>
            <input type="number" id="price" name="price" step="0.01" min="0" required>

            <label for="category">Kategorie:</label>
            <select id="category" name="category" required>
                <option value="Doplňky stravy">Doplňky stravy</option>
                <option value="Fitness vybavení">Fitness vybavení</option>
                <option value="Oblečení">Oblečení</option>
                <option value="Příslušenství">Příslušenství</option>
            </select>

            <label for="image">Obrázek:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <button type="submit">Vytvořit produkt</button>
        </form>

        <h2>Správa produktů</h2>
        <table class="admin-products">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Název</th>
                    <th>Popis</th>
                    <th>Cena</th>
                    <th>Kategorie</th>
                    <th>Obrázek</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= number_format($product['price'], 2) ?> Kč</td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><img src="/mprojekt/public/assets/images/<?= htmlspecialchars($product['image'] ?? 'default.png') ?>" width="50"></td>
                        <td>
                            <a href="/mprojekt/public/admin/edit?id=<?= $product['id'] ?>" class="btn btn-warning">Upravit</a>
                            <a href="/mprojekt/public/admin/delete?id=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('Opravdu chcete tento produkt smazat?');">Smazat</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
</body>
</html>
