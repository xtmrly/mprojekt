<?php
require_once __DIR__ . '/../models/Product.php';

class CartController {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Inicializace košíku, pokud neexistuje
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    // Přidání produktu do košíku
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            // Validace
            if ($product_id <= 0 || $quantity <= 0) {
                $_SESSION['message'] = 'Neplatný produkt nebo množství.';
                header('Location: /mprojekt/public/products');
                exit();
            }
            
            // Ověření existence produktu
            $product = Product::getProductById($product_id);
            if (!$product) {
                $_SESSION['message'] = 'Produkt nebyl nalezen.';
                header('Location: /mprojekt/public/products');
                exit();
            }
            
            // Přidání do košíku
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            
            $_SESSION['message'] = 'Produkt "' . $product['name'] . '" byl přidán do košíku.';
            header('Location: /mprojekt/public/products/show?id=' . $product_id);
            exit();
        }
    }
    
    // Zobrazení košíku
    public function show() {
        $cartItems = [];
        $totalPrice = 0;
        
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $product = Product::getProductById($product_id);
                if ($product) {
                    $product['quantity'] = $quantity;
                    $product['total'] = $product['price'] * $quantity;
                    $cartItems[] = $product;
                    $totalPrice += $product['total'];
                }
            }
        }
        
        require_once __DIR__ . '/../views/cart/cart.php';
    }
    
    // Smazání položky z košíku
    public function remove() {
        if (isset($_GET['id'])) {
            $product_id = (int)$_GET['id'];
            
            if (isset($_SESSION['cart'][$product_id])) {
                $product = Product::getProductById($product_id);
                $productName = $product ? $product['name'] : 'Produkt';
                
                unset($_SESSION['cart'][$product_id]);
                $_SESSION['message'] = $productName . ' byl odstraněn z košíku.';
            }
        }
        
        header('Location: /mprojekt/public/cart');
        exit();
    }
    
    // Aktualizace množství
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $product_id => $quantity) {
                $quantity = (int)$quantity;
                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id] = $quantity;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
            
            // Zkontroluj, jestli je to AJAX request (nemá hlavičku Accept: text/html)
            $isAjax = !isset($_SERVER['HTTP_ACCEPT']) || strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false;
            
            if ($isAjax) {
                // Pro AJAX požadavky vrať JSON odpověď
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit();
            } else {
                // Pro běžné požadavky přesměruj zpět na košík
                $_SESSION['message'] = 'Košík byl aktualizován.';
                header('Location: /mprojekt/public/cart');
                exit();
            }
        }
    }
}