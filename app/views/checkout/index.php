<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Zkontrolovat, zda uživatel má položky v košíku
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['message'] = 'Váš košík je prázdný. Přidejte prosím položky do košíku před dokončením objednávky.';
    header('Location: /mprojekt/public/cart');
    exit();
}

// Načtení informací o uživateli, pokud je přihlášen
$user = null;
if (isset($_SESSION['user_id'])) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Tiché zpracování chyby
    }
}

// Spočítat celkovou cenu položek v košíku
$totalPrice = 0;
$cartItems = [];
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product = Product::getProductById($product_id);
    if ($product) {
        $itemTotal = $product['price'] * $quantity;
        $totalPrice += $itemTotal;
        $cartItems[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'total' => $itemTotal,
            'image' => $product['image'] ?? 'default.png'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Dokončení objednávky</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
    <style>
        /* Doplňující styly pro checkout */
        .checkout-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }
        
        .checkout-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .checkout-steps::after {
            content: '';
            position: absolute;
            top: 14px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 1;
        }
        
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 2;
        }
        
        .step.active {
            background-color: #3498db;
            color: white;
        }
        
        .step-label {
            position: absolute;
            top: 35px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            font-size: 0.85rem;
        }
        
        .cart-summary-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            padding-top: 1rem;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <header>
            <h1>Dokončení objednávky</h1>
            <div class="checkout-steps">
                <div class="step active">1<span class="step-label">Košík</span></div>
                <div class="step active">2<span class="step-label">Doručení</span></div>
                <div class="step">3<span class="step-label">Platba</span></div>
                <div class="step">4<span class="step-label">Dokončeno</span></div>
            </div>
        </header>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="checkout-container">
            <!-- Formulář pro objednávku -->
            <form action="/mprojekt/public/checkout/process" method="POST" class="checkout-form">
                <!-- Osobní údaje -->
                <div class="form-section">
                    <h2>Osobní údaje</h2>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="login-prompt">
                            <a href="/mprojekt/app/views/auth/login?redirect=/mprojekt/public/checkout">
                                Přihlásit se k existujícímu účtu
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="input-row">
                        <div class="input-group">
                            <label for="first_name">Jméno <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="<?= $user ? htmlspecialchars($user['first_name']) : '' ?>" required>
                        </div>
                        <div class="input-group">
                            <label for="last_name">Příjmení <span class="required">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="<?= $user ? htmlspecialchars($user['last_name']) : '' ?>" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="email">E-mail <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?= $user ? htmlspecialchars($user['email']) : '' ?>" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="phone">Telefon <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" pattern="[0-9]{9}" placeholder="123456789" required>
                        <small>Zadejte 9 číslic bez předvolby</small>
                    </div>
                </div>

                <!-- Fakturační adresa -->
                <div class="form-section">
                    <h2>Doručovací adresa</h2>
                    <div class="input-group">
                        <label for="shipping_address">Ulice a č. domu <span class="required">*</span></label>
                        <input type="text" id="shipping_address" name="shipping_address" required>
                    </div>
                    <div class="input-row">
                        <div class="input-group">
                            <label for="city">Město <span class="required">*</span></label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="input-group">
                            <label for="zip">PSČ <span class="required">*</span></label>
                            <input type="text" id="zip" name="zip" pattern="[0-9]{5}" placeholder="12345" required>
                        </div>
                    </div>
                </div>

                <!-- Výběr platby -->
                <div class="form-section">
                    <h2>Způsob platby</h2>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="payment_method" value="dobírka" checked> Dobírka (+49 Kč)
                        </label>
                        <label>
                            <input type="radio" name="payment_method" value="kartou"> Online platba kartou
                        </label>
                        <label>
                            <input type="radio" name="payment_method" value="bankovní převod"> Bankovní převod
                        </label>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Poznámka k objednávce</h2>
                    <div class="input-group">
                        <textarea name="note" rows="4" placeholder="Máte speciální požadavky k objednávce? Napište nám je zde."></textarea>
                    </div>
                </div>

                <!-- Tlačítko pro odeslání -->
                <div class="cart-actions">
                    <a href="/mprojekt/public/cart" class="btn btn-secondary">Zpět do košíku</a>
                    <button type="submit" class="btn btn-primary">Dokončit objednávku</button>
                </div>
            </form>

            <!-- Souhrn objednávky -->
            <div class="cart-summary-box">
                <h2>Souhrn objednávky</h2>
                
                <?php foreach ($cartItems as $item): ?>
                <div class="summary-item">
                    <span><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?>×)</span>
                    <span><?= number_format($item['total'], 0, ',', ' ') ?> Kč</span>
                </div>
                <?php endforeach; ?>
                
                <div class="summary-item">
                    <span>Mezisoučet</span>
                    <span><?= number_format($totalPrice, 0, ',', ' ') ?> Kč</span>
                </div>
                
                <div class="summary-item">
                    <span>Doprava a platba</span>
                    <span id="shipping-cost">Vypočítává se...</span>
                </div>
                
                <div class="summary-total">
                    <span>Celková cena</span>
                    <span id="total-price"><?= number_format($totalPrice, 0, ',', ' ') ?> Kč</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript pro aktualizaci ceny dopravy a celkové ceny
        document.addEventListener("DOMContentLoaded", function(){
            const paymentInputs = document.querySelectorAll('input[name="payment_method"]');
            const shippingCostElement = document.getElementById('shipping-cost');
            const totalPriceElement = document.getElementById('total-price');
            const basePrice = <?= $totalPrice ?>;
            
            function updatePrice() {
                let shippingCost = 0;
                const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;
                
                if (selectedPayment === 'dobírka') {
                    shippingCost = 49;
                }
                
                shippingCostElement.textContent = shippingCost + ' Kč';
                totalPriceElement.textContent = new Intl.NumberFormat('cs-CZ', { 
                    style: 'decimal',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(basePrice + shippingCost) + ' Kč';
            }
            
            // Nastavení počátečních hodnot
            updatePrice();
            
            // Přidání event listenerů pro změny
            paymentInputs.forEach(input => {
                input.addEventListener('change', updatePrice);
            });
        });
    </script>
</body>
</html>