<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Košík</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    
    <section class="cart-container">
        <header>
            <h1>Nákupní košík</h1>
        </header>
        
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <h2>Váš košík je prázdný</h2>
                <p>Procházejte naše produkty a přidávejte je do košíku</p>
                <a href="/mprojekt/public/products" class="btn btn-primary">Procházet produkty</a>
            </div>
        <?php else: ?>
            <form action="/mprojekt/public/cart/update" method="POST">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produkt</th>
                            <th>Cena</th>
                            <th>Množství</th>
                            <th>Celkem</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="cart-product">
                                        <img src="/mprojekt/public/assets/images/<?= htmlspecialchars($item['image'] ?? 'default.png') ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>">
                                        <div class="product-details">
                                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                                            <?php if(isset($item['category'])): ?>
                                                <p><?= htmlspecialchars($item['category']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= number_format($item['price'], 0, ',', ' ') ?> Kč</td>
                                <td>
                                    <input type="number" name="quantities[<?= $item['id'] ?>]" 
                                           value="<?= $item['quantity'] ?>" min="1" max="10" 
                                           class="quantity-input">
                                </td>
                                <td><?= number_format($item['total'], 0, ',', ' ') ?> Kč</td>
                                <td>
                                    <a href="/mprojekt/public/cart/remove?id=<?= $item['id'] ?>" 
                                       class="remove-button">Odstranit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-summary">
                    <h2>Souhrn objednávky</h2>
                    <div class="summary-row">
                        <span>Mezisoučet:</span>
                        <strong><?= number_format($totalPrice, 0, ',', ' ') ?> Kč</strong>
                    </div>
                    <div class="summary-row">
                        <span>Doprava:</span>
                        <span>Bude vypočítáno v dalším kroku</span>
                    </div>
                    <div class="summary-row" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                        <span>Celková cena:</span>
                        <strong><?= number_format($totalPrice, 0, ',', ' ') ?> Kč</strong>
                    </div>
                </div>
                
                <div class="cart-actions">
                    <a href="/mprojekt/public/products" class="btn btn-secondary">Pokračovat v nákupu</a>
                    <button type="submit" class="">Aktualizovat košík</button>
                    <a href="/mprojekt/public/checkout" class="btn btn-primary">Pokračovat k pokladně</a>
                </div>
            </form>
        <?php endif; ?>
    </section>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Najít všechny prvky pro množství
        const quantityInputs = document.querySelectorAll('.quantity-input');
        
        // Přidat event listener pro každý input
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Získat ID produktu z názvu inputu - quantities[ID]
                const productId = this.name.match(/quantities\[(\d+)\]/)[1];
                
                // Získat cenu za kus - najít odpovídající řádek v tabulce
                const row = this.closest('tr');
                const priceCell = row.querySelector('td:nth-child(2)');
                const priceText = priceCell.textContent;
                const price = parseInt(priceText.replace(/\s+/g, '').replace('Kč', ''));
                
                // Vypočítat novou cenu za položku
                const quantity = parseInt(this.value);
                const totalItemPrice = price * quantity;
                
                // Aktualizovat cenu položky v tabulce
                const totalCell = row.querySelector('td:nth-child(4)');
                totalCell.textContent = totalItemPrice.toLocaleString('cs-CZ') + ' Kč';
                
                // Přepočítat celkovou cenu košíku
                recalculateCartTotal();
                
                // Aktualizovat počet položek v navigaci
                updateCartBadge();
                
                // Odeslat AJAX request pro aktualizaci košíku na serveru
                updateCartOnServer(productId, quantity);
            });
        });
        
        // Funkce pro přepočítání celkové ceny
        function recalculateCartTotal() {
            let total = 0;
            
            // Projít všechny položky a sečíst jejich ceny
            document.querySelectorAll('tr').forEach(row => {
                const totalCell = row.querySelector('td:nth-child(4)');
                if (totalCell) {
                    const totalText = totalCell.textContent;
                    const itemTotal = parseInt(totalText.replace(/\s+/g, '').replace('Kč', ''));
                    total += itemTotal;
                }
            });
            
            // Aktualizovat zobrazení mezisoučtu
            const subtotalElement = document.querySelector('.summary-row:first-child strong');
            if (subtotalElement) {
                subtotalElement.textContent = total.toLocaleString('cs-CZ') + ' Kč';
            }
            
            // Aktualizovat zobrazení celkové ceny
            const totalPriceElement = document.querySelector('.summary-row:last-child strong');
            if (totalPriceElement) {
                totalPriceElement.textContent = total.toLocaleString('cs-CZ') + ' Kč';
            }
        }
        
        // Funkce pro aktualizaci počtu položek v košíku v navigaci
        function updateCartBadge() {
            let totalItems = 0;
            
            // Součet množství všech položek
            document.querySelectorAll('.quantity-input').forEach(input => {
                totalItems += parseInt(input.value) || 0;
            });
            
            // Aktualizovat číslo v navigaci
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = totalItems;
                
                // Zobrazit nebo skrýt podle počtu
                if (totalItems > 0) {
                    cartCountElement.style.display = 'flex';
                } else {
                    cartCountElement.style.display = 'none';
                }
            }
        }
        
        // Funkce pro AJAX aktualizaci košíku
        function updateCartOnServer(productId, quantity) {
            // Vytvořit FormData objekt pro odeslání dat
            const formData = new FormData();
            formData.append('quantities[' + productId + ']', quantity);
            
            // Odeslat AJAX požadavek
            fetch('/mprojekt/public/cart/update', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    console.error('Chyba při aktualizaci košíku');
                }
            })
            .catch(error => {
                console.error('AJAX chyba:', error);
            });
        }
    });
    </script>
</body>
</html>