<?php
// filepath: /c:/wamp64/www/mprojekt/app/views/checkout/success.php
require_once __DIR__ . '/../../../app/models/Order.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Získat ID objednávky z URL
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

// Získat informace o objednávce
$orderDetails = null;
$orderItems = [];

if ($orderId) {
    try {
        global $pdo;
        
        // Získat detaily objednávky
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute([':id' => $orderId]);
        $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Získat položky objednávky
        $stmt = $pdo->prepare("
            SELECT oi.*, p.name, p.image 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute([':order_id' => $orderId]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $_SESSION['message'] = "Chyba při načítání informací o objednávce: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Objednávka úspěšně dokončena</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
    <style>
        .success-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .success-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: #4CAF50;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        
        .success-title {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .success-message {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .order-summary h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.75rem;
        }
        
        .order-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-detail:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .order-detail strong {
            color: #333;
        }
        
        .order-items {
            margin-top: 2rem;
        }
        
        .order-items h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
        }
        
        .order-item {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .order-item img {
            width: 100%;
            height: 120px;
            object-fit: contain;
            margin-bottom: 0.5rem;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
            font-size: 0.95rem;
        }
        
        .item-details {
            font-size: 0.9rem;
            color: #666;
            display: flex;
            justify-content: space-between;
        }
        
        .next-steps {
            text-align: center;
            margin-top: 3rem;
        }
        
        .next-steps h3 {
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn-success {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #3e8e41;
        }
        
        @media (max-width: 768px) {
            .item-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
        
        @media print {
            .action-buttons, .next-steps h3 {
                display: none;
            }
            
            .success-container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">✓</div>
            <h1 class="success-title">Děkujeme za Vaši objednávku!</h1>
            <p class="success-message">
                Vaše objednávka #<?= $orderDetails ? $orderDetails['id'] : '---' ?> byla úspěšně přijata.
                O průběhu zpracování Vás budeme informovat e-mailem.
            </p>
        </div>
        
        <?php if ($orderDetails): ?>
        <div class="order-summary">
            <h2>Souhrn objednávky #<?= $orderDetails['id'] ?></h2>
            
            <div class="order-detail">
                <span>Datum objednávky:</span>
                <strong><?= date('d.m.Y H:i', strtotime($orderDetails['created_at'])) ?></strong>
            </div>
            
            <div class="order-detail">
                <span>Způsob platby:</span>
                <strong>
                    <?php 
                    switch ($orderDetails['payment_method']) {
                        case 'dobírka':
                            echo 'Dobírka';
                            break;
                        case 'kartou':
                            echo 'Online platba kartou';
                            break;
                        case 'bankovní převod':
                            echo 'Bankovní převod';
                            break;
                        default:
                            echo htmlspecialchars($orderDetails['payment_method']);
                    }
                    ?>
                </strong>
            </div>
            
            <div class="order-detail">
                <span>Doručovací adresa:</span>
                <strong><?= htmlspecialchars($orderDetails['shipping_address']) ?></strong>
            </div>
            
            <div class="order-detail">
                <span>Celková cena:</span>
                <strong><?= number_format($orderDetails['total_price'], 0, ',', ' ') ?> Kč</strong>
            </div>
            
            <?php if (!empty($orderItems)): ?>
            <div class="order-items">
                <h3>Objednané položky:</h3>
                <div class="item-grid">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="order-item">
                        <img src="/mprojekt/public/assets/images/<?= htmlspecialchars($item['image'] ?? 'default.png') ?>" 
                             alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="item-details">
                            <span><?= $item['quantity'] ?> ks</span>
                            <span><?= number_format($item['price'], 0, ',', ' ') ?> Kč/ks</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="next-steps">
            <h3>Co dále?</h3>
            <p>Na váš e-mail jsme zaslali potvrzení objednávky s detaily.</p>
            <p>V případě jakýchkoliv dotazů nás neváhejte kontaktovat.</p>
            
            <div class="action-buttons">
                <button class="btn btn-secondary" onclick="window.print()">Vytisknout objednávku</button>
                <a href="/mprojekt/public/products" class="btn btn-primary">Pokračovat v nákupu</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/mprojekt/public/user/orders" class="btn btn-success">Moje objednávky</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
