<?php
$host = 'localhost'; // L'adresse de la maison de la base de données
$dbname = 'ijs_biblio_db'; // Le nom du placard qu'on veut ouvrir
$username = 'root'; // Le nom du chef qui a le droit d'entrer
$password = ''; // Le code secret du chef (souvent vide sur ton ordi)

try { // On essaye de faire quelque chose de difficile
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password); // On branche le téléphone
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Si le téléphone sonne mal, on veut que ça nous le dise fort
} catch (PDOException $e) { // Si on n'arrive pas à brancher le téléphone...
    die("Zut ! On n'arrive pas à se connecter : " . $e->getMessage()); // On affiche un message et on s'arrête là
} // Fin de la tentative
?>