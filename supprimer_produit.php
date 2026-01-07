<?php
session_start();
if (!isset($_SESSION['user_id'])) { exit(); }
require_once 'connexion.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: produits.php');
exit();