<?php

session_start();
session_destroy();

// Přesměrování na domovskou stránku po odhlášení
header('Location: /mprojekt/public/');
exit();
