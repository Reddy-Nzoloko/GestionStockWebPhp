<?php
session_start();
// Vérification de sécurité : si pas de session, retour au login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'connexion.php';

// Statistiques pour le Dashboard
$total_produits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$alerte_stock = $pdo->query("SELECT COUNT(*) FROM produits WHERE quantite_stock < 5")->fetchColumn();

// Bonus : Récupérer les 5 derniers produits ajoutés
$derniers_produits = $pdo->query("SELECT p.*, c.nom as cat_nom FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockMaster - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen overflow-hidden">
        
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-8 bg-white p-4 rounded-lg shadow-sm">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord</h1>
                    <p class="text-sm text-gray-500">Bienvenue, <span class="font-semibold text-blue-600"><?php echo htmlspecialchars($_SESSION['username']); ?></span> !</p>
                </div>
                <a href="logout.php" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg hover:bg-red-600 hover:text-white transition duration-300 flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-blue-500 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg text-blue-500 mr-4">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold">Produits</p>
                            <p class="text-3xl font-black text-gray-800"><?php echo $total_produits; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-green-500 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg text-green-500 mr-4">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold">Catégories</p>
                            <p class="text-3xl font-black text-gray-800"><?php echo $total_categories; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-red-500 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-lg text-red-500 mr-4">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold">Alertes Stock</p>
                            <p class="text-3xl font-black text-gray-800"><?php echo $alerte_stock; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gray-50 p-4 border-b">
                    <h2 class="text-lg font-bold text-gray-800">Dernières activités</h2>
                </div>
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3">Produit</th>
                            <th class="px-6 py-3">Catégorie</th>
                            <th class="px-6 py-3">Prix</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach($derniers_produits as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($p['nom']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['cat_nom'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 text-sm font-bold"><?php echo $p['prix_unitaire']; ?> €</td>
                            <td class="px-6 py-4 text-xs text-gray-400"><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>