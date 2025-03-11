<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartItemCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>

<nav class="main-nav">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="/mprojekt/public/">
                <strong>SportNutrition</strong>
            </a>
        </div>
        
        <div class="nav-search">
            <form action="/mprojekt/public/search" method="GET">
                <input type="text" name="q" placeholder="Hledat produkty...">
                <button type="submit"><i class="fas fa-search">🔍</i></button>
            </form>
        </div>
        
        <ul class="navbar">
            <li><a href="/mprojekt/public/">Domů</a></li>
            <li><a href="/mprojekt/public/products">Produkty</a></li>
            
            <li class="dropdown">
                <a href="#" class="dropbtn">Kategorie <i class="fas fa-angle-down">▼</i></a>
                <div class="dropdown-content">
                    <a href="/mprojekt/public/products?category=Doplňky stravy">Doplňky stravy</a>
                    <a href="/mprojekt/public/products?category=Fitness vybavení">Fitness vybavení</a>
                    <a href="/mprojekt/public/products?category=Oblečení">Oblečení</a>
                    <a href="/mprojekt/public/products?category=Příslušenství">Příslušenství</a>
                </div>
            </li>

            <!-- Odkaz na admin panel (viditelný pouze pro admina) -->
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li><a href="/mprojekt/public/admin">Admin Panel</a></li>
            <?php endif; ?>
            
            <!-- Shopping Cart -->
            <li class="cart-icon">
                <a href="/mprojekt/public/cart">
                    <i class="fas fa-shopping-cart">🛒</i>
                    <?php if($cartItemCount > 0): ?>
                        <span class="cart-count"><?= $cartItemCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <?php if (!isset($_SESSION['user_name'])): ?>
                <li><a href="/mprojekt/app/views/auth/login">Přihlášení</a></li>
                <li><a href="/mprojekt/app/views/auth/register">Registrace</a></li>
            <?php else: ?>
                <li class="dropdown user-dropdown">
                    <a href="#" class="dropbtn user-section">
                        <i class="fas fa-user">👤</i>
                        <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']); ?></span>
                    </a>
                    <div class="dropdown-content">
                        <a href="/mprojekt/public/user/profile">Můj profil</a>
                        <a href="/mprojekt/public/user/orders">Moje objednávky</a>
                        <a href="/mprojekt/app/controllers/LogoutController.php" class="logout-link">Odhlásit se</a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>