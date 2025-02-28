<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Dokončení objednávky</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <section class="cart-container">
        <header>
            <h1>Dokončení objednávky</h1>
        </header>

        <!-- Formulář pro objednávku -->
        <div class="checkout-form">
            <!-- Osobní údaje -->
            <div class="checkout-section">
                <h2>Osobní údaje</h2>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="login-prompt">
                        <a href="/mprojekt/public/login?redirect=/mprojekt/public/checkout">
                            Přihlásit se k existujícímu účtu
                        </a>
                    </div>
                <?php endif; ?>

                <div class="input-row">
                    <div class="input-group">
                        <label for="first_name">Jméno <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="input-group">
                        <label for="last_name">Příjmení <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="email">E-mail <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="input-group">
                    <label for="phone">Telefon <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
            </div>

            <!-- Fakturační adresa -->
            <div class="checkout-section">
                <h2>Fakturační adresa</h2>
                <div class="input-group">
                    <label for="street">Ulice a č. domu <span class="required">*</span></label>
                    <input type="text" id="street" name="street" required>
                </div>
                <div class="input-row">
                    <div class="input-group">
                        <label for="city">Město <span class="required">*</span></label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="input-group">
                        <label for="zip">PSČ <span class="required">*</span></label>
                        <input type="text" id="zip" name="zip" required>
                    </div>
                </div>
                <div class="input-group">
                    <label for="country">Země <span class="required">*</span></label>
                    <div class="input-group">
                    <input type="text" id="shipping_country" name="shipping_country">
                    </input>
                </div>
                </div>

                <div class="inline-checkbox">
                    <input type="checkbox" id="different_shipping" name="different_shipping">
                    <label for="different_shipping">Doručit na jinou adresu</label>
                </div>
            </div>

            <!-- Dodací adresa (skrytá dokud není zaškrtnut checkbox) -->
            <div class="checkout-section" id="shipping-section" style="display: none;">
                <h2>Dodací adresa</h2>
                <div class="input-group">
                    <label for="shipping_street">Ulice a č. domu</label>
                    <input type="text" id="shipping_street" name="shipping_street">
                </div>
                <div class="input-row">
                    <div class="input-group">
                        <label for="shipping_city">Město</label>
                        <input type="text" id="shipping_city" name="shipping_city">
                    </div>
                    <div class="input-group">
                        <label for="shipping_zip">PSČ</label>
                        <input type="text" id="shipping_zip" name="shipping_zip">
                    </div>
                </div>
                <div class="input-group">
                    <label for="shipping_country">Země</label>
                    <input type="text" id="shipping_country" name="shipping_country">
                    </input>
                </div>
            </div>

            <!-- Výběr platby -->
            <div class="checkout-section">
                <h2>Způsob platby</h2>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="payment_method" value="Dobírka" checked> Dobírka
                    </label>
                    <label>
                        <input type="radio" name="payment_method" value="Kreditní karta"> Kreditní karta
                    </label>
                    <label>
                        <input type="radio" name="payment_method" value="PayPal"> PayPal
                    </label>
                </div>
            </div>

            <!-- Tlačítko pro odeslání -->
            <div class="cart-actions">
                <a href="/mprojekt/public/cart" class="btn btn-secondary">Zpět do košíku</a>
                <button type="submit" class="btn btn-primary">Dokončit objednávku</button>
            </div>
        </div>
    </section>

    <script>
        // Zobrazení sekce dodací adresy
        document.addEventListener("DOMContentLoaded", function(){
            var checkbox = document.getElementById("different_shipping");
            var shippingSection = document.getElementById("shipping-section");

            checkbox.addEventListener("change", function(){
                shippingSection.style.display = checkbox.checked ? "block" : "none";
            });
        });
    </script>
</body>
</html>
