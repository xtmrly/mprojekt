<?php

session_start(); // Spuštění session
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Kontrola, zda jsou všechna pole vyplněná
    if (empty($email) || empty($password)) {
        die('Vyplňte všechny požadované údaje.');
    }

    try {
        // Načtení uživatele z databáze podle e-mailu
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kontrola, zda uživatel existuje a heslo je správné
        if (!$user || !password_verify($password, $user['password'])) {
            die('Neplatný e-mail nebo heslo.');
        }

        // Nastavení údajů do session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name']; // Předpokládá, že v databázi je sloupec `first_name`
        $_SESSION['user_email'] = $user['email'];

        // Přesměrování na hlavní stránku po úspěšném přihlášení
        header('Location: /mprojekt/public/');
        exit();
    } catch (PDOException $e) {
        // Zpracování chyby při připojení k databázi nebo dotazu
        die('Chyba při přihlášení: ' . $e->getMessage());
    }
} else {
    // Pokud metoda není POST, přesměruj zpět na přihlašovací formulář
    header('Location: /mprojekt/public/auth/login');
    exit();
}
