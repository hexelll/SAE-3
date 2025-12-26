<?php
$host = 'localhost';
$dbname = 'bdfablab';
$username = 'adminer'; 
$password = 'Isanum64!';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>