<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Upravit produkt</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1>Upravit produkt</h1>

        <form action="/mprojekt/public/admin/update" method="POST">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">

            <label for="name">Název:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label for="description">Popis:</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

            <label for="price">Cena:</label>
            <input type="number" id="price" name="price" value="<?= $product['price'] ?>" required>

            <label for="category">Kategorie:</label>
            <select id="category" name="category" required>
                <option value="Doplňky stravy">Doplňky stravy</option>
                <option value="Fitness vybavení">Fitness vybavení</option>
                <option value="Oblečení">Oblečení</option>
                <option value="Příslušenství">Příslušenství</option>
            </select>

            <button type="submit" class="btn btn-primary">Uložit změny</button>
        </form>

        <a href="/mprojekt/public/admin" class="btn btn-secondary">Zpět</a>
    </div>
</body>
</html>
