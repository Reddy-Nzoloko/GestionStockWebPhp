<?php
session_start();
require_once 'connexion.php';
if(isset($_GET['id'])) { $pdo->prepare("DELETE FROM fournisseurs WHERE id=?")->execute([$_GET['id']]); }
header('Location: fournisseurs.php');