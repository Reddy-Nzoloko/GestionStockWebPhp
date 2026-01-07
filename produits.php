<?php
require_once 'connexion.php';

// Récupération des produits avec le nom de leur catégorie
$sql = "SELECT p.*, c.nom as categorie_nom 
        FROM produits p 
        LEFT JOIN categories c ON p.categorie_id = c.id 
        ORDER BY p.created_at DESC";
$stmt = $pdo->query($sql);
$produits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Produits</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 p-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Liste des Produits</h1>
                <a href="ajouter_produit.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i> Ajouter un produit
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700 uppercase text-sm">
                            <th class="px-6 py-4">Nom</th>
                            <th class="px-6 py-4">Catégorie</th>
                            <th class="px-6 py-4">Prix</th>
                            <th class="px-6 py-4">Stock</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($produits)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    Aucun produit trouvé. <a href="ajouter_produit.php" class="text-blue-500 underline">Ajoutez-en un !</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($produits as $p): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($p['nom']); ?></td>
                                    <td class="px-6 py-4 text-gray-600">
                                        <span class="px-2 py-1 bg-gray-100 rounded-md text-xs">
                                            <?php echo htmlspecialchars($p['categorie_nom'] ?? 'Sans catégorie'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600"><?php echo number_format($p['prix_unitaire'], 2); ?> €</td>
                                    <td class="px-6 py-4">
                                        <?php if ($p['quantite_stock'] < 5): ?>
                                            <span class="text-red-600 font-bold bg-red-100 px-2 py-1 rounded">
                                                <i class="fas fa-exclamation-circle mr-1"></i><?php echo $p['quantite_stock']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-green-600"><?php echo $p['quantite_stock']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="modifier_produit.php?id=<?php echo $p['id']; ?>" class="text-blue-500 hover:text-blue-700 mx-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="supprimer_produit.php?id=<?php echo $p['id']; ?>" class="text-red-500 hover:text-red-700 mx-2" onclick="return confirm('Supprimer ce produit ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>