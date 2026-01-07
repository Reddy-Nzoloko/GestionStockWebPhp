<?php
session_start();
if (!isset($_SESSION['user_id'])) { exit(); }
require_once 'connexion.php';

$id = $_GET['id'] ?? null;
if ($id) {
    // Note : Si des produits sont liés, ils deviendront 'Sans catégorie' 
    // grâce au 'ON DELETE SET NULL' que nous avons mis dans le SQL au début.
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
}
header('Location: categories.php');
exit();