<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail objednávky #<?= $order['id'] ?></title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
    <style>
        .order-detail-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            display: inline-block;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .order-info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .order-info-box h3 {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .info-label {
            font-weight: 500;
            color: #555;
        }
        
        .order-items {
            margin-top: 2rem;
        }
        
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        
        .item-table th, 
        .item-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .item-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        
        .item-product {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .item-details {
            display: flex;
            flex-direction: column;
        }
        
        .item-name {
            font-weight: 500;
        }
        
        .order-summary {
            margin-top: 2rem;
            text-align: right;
        }
        
        .order-total {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 0.5rem;
        }
        
        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        @media (max-width: 768px) {
            .order-info {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
                gap: 1rem;
            }
            
            .actions .btn {
                width: 100%;
                text-align: center;
            }
        }
        
        @media print {
            .actions, .navbar {
                display: none;
            }
            
            .order-detail-container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container order-detail-container">
        <header>
            <h1>Objednávka #<?= $order['id'] ?></h1>
            
            <?php 
            $statusClass = '';
            $statusText = '';
            
            switch ($order['status']) {
                case 'pending':
                    $statusClass = 'status-pending';
                    $statusText = 'Čeká na zpracování';
                    break;
                case 'completed':
                    $statusClass = 'status-completed';
                    $statusText = 'Dokončeno';
                    break;
                case 'cancelled':
                    $statusClass = 'status-cancelled';
                    $statusText = 'Zrušeno';
                    break;
                default:
                    $statusText = 'Neznámý stav';
            }
            ?>
            
            <div class="order-status <?= $statusClass ?>"><?= $statusText ?></div>
        </header>
        
        <div class="order-info">
            <div class="order-info-box">
                <h3>Informace o objednávce</h3>
                <div class="info-row">
                    <span class="info-label">Datum objednávky:</span>
                    <span><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Způsob platby:</span>
                    <span>
                        <?php 
                        switch ($order['payment_method']) {
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
                                echo htmlspecialchars($order['payment_method']);
                        }
                        ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Celková cena:</span>
                    <span><?= number_format($order['total_price'], 0, ',', ' ') ?> Kč</span>
                </div>
            </div>
            
            <div class="order-info-box">
                <h3>Doručovací údaje</h3>
                <div class="info-row">
                    <span class="info-label">Jméno:</span>
                    <span><?= htmlspecialchars($order['first_name']) ?> <?= htmlspecialchars($order['last_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span><?= htmlspecialchars($order['email']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Adresa:</span>
                    <span><?= htmlspecialchars($order['shipping_address']) ?></span>
                </div>
            </div>
        </div>
        
        <div class="order-items">
            <h2>Objednané položky</h2>
            
            <table class="item-table">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Kategorie</th> <!-- Přidaný sloupec pro kategorie -->
                        <th>Cena za kus</th>
                        <th>Množství</th>
                        <th>Celkem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td>
                            <div class="item-product">
                                <img src="/mprojekt/public/assets/images/<?= htmlspecialchars($item['image'] ?? 'default.png') ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>" class="item-image">
                                <div class="item-details">
                                    <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
                                    <span class="item-id">ID: <?= $item['product_id'] ?></span>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($item['category'] ?? 'Bez kategorie') ?></td> <!-- Zobrazení kategorie -->
                        <td><?= number_format($item['price'], 0, ',', ' ') ?> Kč</td>
                        <td><?= $item['quantity'] ?> ks</td>
                        <td><?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> Kč</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="order-summary">
                <div>Celková částka:</div>
                <div class="order-total"><?= number_format($order['total_price'], 0, ',', ' ') ?> Kč</div>
            </div>
        </div>
        
        <div class="actions">
            <a href="/mprojekt/public/user/orders" class="btn btn-secondary">Zpět na přehled objednávek</a>
            <button class="btn btn-primary" onclick="window.print()">Vytisknout objednávku</button>
        </div>
    </div>
</body>
</html>