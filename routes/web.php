<?php
require_once __DIR__ . '/../app/controllers/CheckoutController.php'; // DŮLEŽITÉ
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/CartController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
// Další kontrolery...

// Get the request URI without query parameters
$requestUri = strtok($_SERVER['REQUEST_URI'], '?');

switch ($requestUri) {
    // Main page
    case '/mprojekt/public/':
        $controller = new ProductController();
        $controller->index();
        break;

    // Products
    case '/mprojekt/public/products':
        $controller = new ProductController();
        $controller->index();
        break;

    case '/mprojekt/public/products/show':
        if(isset($_GET['id']) && is_numeric($_GET['id'])) {
            $controller = new ProductController();
            $controller->show($_GET['id']);
        } else {
            header('Location: /mprojekt/public/products');
        }
        break;


        case '/mprojekt/public/search':
            $controller = new ProductController();
            $controller->search();
            break;

        
        
    // Cart routes
    case '/mprojekt/public/cart':
        $controller = new CartController();
        $controller->show();
        break;
        
    case '/mprojekt/public/cart/add':
        $controller = new CartController();
        $controller->add();
        break;
        
    case '/mprojekt/public/cart/update':
        $controller = new CartController();
        $controller->update();
        break;
        
    case '/mprojekt/public/cart/remove':
        $controller = new CartController();
        $controller->remove();
        break;


    // checkout
    case '/mprojekt/public/checkout':
        $controller = new CheckoutController();
        $controller->index();
        break;
        
    case '/mprojekt/public/checkout/process':
        $controller = new CheckoutController();
        $controller->process();
        break;
        
    case '/mprojekt/public/checkout/success':
        $controller = new CheckoutController();
        $controller->success();
        break;

    case '/mprojekt/public/user/profile':
        $controller = new UserController();
        $controller->profile();
        break;
        
    case '/mprojekt/public/user/orders':
        $controller = new UserController();
        $controller->orders();
        break;

    // Orders routes
    case '/mprojekt/public/orders/detail':
        $controller = new UserController();
        $controller->orderDetail();
        break;

    // Ostatní routy...

    default:
        header("HTTP/1.0 404 Not Found");
        require __DIR__ . '/../app/views/404.php';
        break;
}