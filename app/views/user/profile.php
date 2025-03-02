<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uživatelský profil</title>
    <link rel="stylesheet" href="/mprojekt/public/assets/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    
    <div class="container">
        <header>
            <h1>Uživatelský profil</h1>
        </header>
        
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-container">
            <div class="profile-card">
                <h2>Osobní údaje</h2>
                <div class="profile-info">
                    <p><strong>Jméno:</strong> <?= htmlspecialchars($user['first_name']) ?> <?= htmlspecialchars($user['last_name']) ?></p>
                    <p><strong>E-mail:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Účet vytvořen:</strong> <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                </div>
                
                <a href="/mprojekt/public/user/edit" class="btn btn-primary">Upravit profil</a>
            </div>
            
            <div class="orders-section">
                <h2>Moje objednávky</h2>
                
                <?php if (empty($orders)): ?>
                    <p>Zatím nemáte žádné objednávky.</p>
                <?php else: ?>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Číslo objednávky</th>
                                <th>Datum</th>
                                <th>Celková cena</th>
                                <th>Stav</th>
                                <th>Akce</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                                    <td><?= number_format($order['total_price'], 0, ',', ' ') ?> Kč</td>
                                    <td>
                                        <?php
                                        switch ($order['status']) {
                                            case 'pending':
                                                echo '<span class="status pending">Čeká na zpracování</span>';
                                                break;
                                            case 'completed':
                                                echo '<span class="status completed">Dokončeno</span>';
                                                break;
                                            case 'cancelled':
                                                echo '<span class="status cancelled">Zrušeno</span>';
                                                break;
                                            default:
                                                echo '<span class="status">Neznámý stav</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="/mprojekt/public/orders/detail?id=<?= $order['id'] ?>" class="btn btn-sm">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>