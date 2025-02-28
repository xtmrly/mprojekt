<?php

require_once __DIR__ . '/../models/Product.php';

class CheckoutController
{
    public function index()
    {
        // Zde můžeme předvyplnit adresu, pokud ji má uživatel v profilu atd.
        // Prozatím zobrazíme prázdný formulář.
        require __DIR__ . '/../views/checkout/index.php';
    }

    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1) Získání dat z formuláře
            $shippingAddress = trim($_POST['shipping_address']);
            $paymentMethod = trim($_POST['payment_method']);

            // 2) Získání obsahu košíku ze session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

            if (empty($cart)) {
                // Košík je prázdný – nelze pokračovat
                $_SESSION['message'] = 'Košík je prázdný, nelze vytvořit objednávku.';
                header('Location: /mprojekt/public/cart');
                exit();
            }

            // 3) Spočítání celkové ceny
            $totalPrice = 0;
            $cartItems = [];
            foreach ($cart as $product_id => $quantity) {
                $product = Product::getProductById($product_id);
                if ($product) {
                    $itemTotal = $product['price'] * $quantity;
                    $totalPrice += $itemTotal;
                    // Uložíme si pro pozdější použití
                    $cartItems[] = [
                        'product_id' => $product_id,
                        'quantity'   => $quantity,
                        'price'      => $product['price']
                    ];
                }
            }

            // 4) Vytvoření objednávky v tabulce `orders`
            global $pdo;

            // user_id můžeme načíst ze session, pokud je uživatel přihlášen
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            try {
                // Začneme transakci pro jistotu, aby se buď uložilo vše, nebo nic
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, shipping_address, payment_method, created_at) 
                                       VALUES (:user_id, :total, :shipping_address, :payment_method, NOW())");
                $stmt->execute([
                    ':user_id'         => $userId,
                    ':total'           => $totalPrice,
                    ':shipping_address'=> $shippingAddress,
                    ':payment_method'  => $paymentMethod
                ]);

                $orderId = $pdo->lastInsertId();

                // 5) Vložení položek objednávky do `order_items`
                $stmtItems = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price)
                                            VALUES (:order_id, :product_id, :quantity, :price)");

                foreach ($cartItems as $item) {
                    $stmtItems->execute([
                        ':order_id'   => $orderId,
                        ':product_id' => $item['product_id'],
                        ':quantity'   => $item['quantity'],
                        ':price'      => $item['price']
                    ]);
                }

                // Dokončení transakce
                $pdo->commit();

                // 6) Vyprázdnění košíku
                unset($_SESSION['cart']);

                // 7) Přesměrování na stránku s potvrzením objednávky
                header("Location: /mprojekt/public/checkout/success?order_id=" . $orderId);
                exit();

            } catch (PDOException $e) {
                // V případě chyby zrušíme transakci
                $pdo->rollBack();
                $_SESSION['message'] = "Chyba při vytváření objednávky: " . $e->getMessage();
                header('Location: /mprojekt/public/cart');
                exit();
            }
        } else {
            // Pokud není POST, vrať se na formulář
            header('Location: /mprojekt/public/checkout');
            exit();
        }
    }

    public function success()
    {
        // Tady můžeš zobrazit „Děkujeme za objednávku“, případně číslo objednávky
        require __DIR__ . '/../views/checkout/success.php';
    }
}
