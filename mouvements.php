<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

// Récupération de l'historique des mouvements avec le nom du produit
$sql = "SELECT m.*, p.nom as produit_nom 
        FROM mouvements m 
        JOIN produits p ON m.produit_id = p.id 
        ORDER BY m.date_mouvement DESC";
$mouvements = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mouvements de Stock - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    <?php include 'sidebar.php'; ?>

    <div class="flex-1 p-8 overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Mouvements de Stock</h1>
            <a href="ajouter_mouvement.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg">
                <i class="fas fa-exchange-alt mr-2"></i> Enregistrer un mouvement
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-800 text-white uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Produit</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Quantité</th>
                        <th class="px-6 py-4">Commentaire</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach($mouvements as $m): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php echo date('d/m/Y H:i', strtotime($m['date_mouvement'])); ?>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($m['produit_nom']); ?></td>
                        <td class="px-6 py-4">
                            <?php if($m['type_mouvement'] == 'entree'): ?>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold uppercase">Entrée</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold uppercase">Sortie</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 font-bold">
                            <?php echo ($m['type_mouvement'] == 'entree' ? '+' : '-') . $m['quantite']; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 italic"><?php echo htmlspecialchars($m['commentaire']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>