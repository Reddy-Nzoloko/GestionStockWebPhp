<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

// Requête avec JOIN pour avoir le nom de la catégorie au lieu de l'ID
$sql = "SELECT p.*, c.nom as cat_nom 
        FROM produits p 
        LEFT JOIN categories c ON p.categorie_id = c.id 
        ORDER BY p.id DESC";
$produits = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 p-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Inventaire des Produits</h1>
                <a href="ajouter_produit.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg">
                    <i class="fas fa-plus mr-2"></i> Ajouter un Produit
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-800 text-white uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Produit</th>
                            <th class="px-6 py-4">Catégorie</th>
                            <th class="px-6 py-4">Prix</th>
                            <th class="px-6 py-4">Stock</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach($produits as $p): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800"><?php echo htmlspecialchars($p['nom']); ?></div>
                                <div class="text-xs text-gray-400"><?php echo substr($p['description'], 0, 50); ?>...</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-semibold">
                                    <?php echo htmlspecialchars($p['cat_nom'] ?? 'Aucune'); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-700"><?php echo number_format($p['prix_unitaire'], 2); ?> €</td>
                            <td class="px-6 py-4">
                                <?php if($p['quantite_stock'] <= 5): ?>
                                    <span class="text-red-600 font-bold"><i class="fas fa-arrow-down mr-1"></i><?php echo $p['quantite_stock']; ?> (Bas)</span>
                                <?php else: ?>
                                    <span class="text-green-600 font-bold"><?php echo $p['quantite_stock']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <a href="modifier_produit.php?id=<?php echo $p['id']; ?>" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                <a href="supprimer_produit.php?id=<?php echo $p['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Supprimer ce produit ?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>