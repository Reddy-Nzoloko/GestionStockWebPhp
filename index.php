<?php
// Inclusion du fichier de connexion que vous avez créé
require_once 'connexion.php';

// 1. Récupérer le nombre total de produits
$stmt = $pdo->query("SELECT COUNT(*) FROM produits");
$total_produits = $stmt->fetchColumn();

// 2. Récupérer le nombre de catégories
$stmt = $pdo->query("SELECT COUNT(*) FROM categories");
$total_categories = $stmt->fetchColumn();

// 3. Alerte de stock (produits dont la quantité est < 5)
$stmt = $pdo->query("SELECT COUNT(*) FROM produits WHERE quantite_stock < 5");
$alerte_stock = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen overflow-hidden">
        
        <div class="w-64 bg-gray-800 text-white flex-shrink-0">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">StockMaster</div>
            <nav class="mt-6">
                <a href="index.php" class="block py-3 px-6 bg-blue-600 text-white">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                <a href="produits.php" class="block py-3 px-6 hover:bg-gray-700">
                    <i class="fas fa-box mr-3"></i> Produits
                </a>
                <a href="categories.php" class="block py-3 px-6 hover:bg-gray-700">
                    <i class="fas fa-tags mr-3"></i> Catégories
                </a>
                <a href="mouvements.php" class="block py-3 px-6 hover:bg-gray-700">
                    <i class="fas fa-exchange-alt mr-3"></i> Mouvements
                </a>
            </nav>
        </div>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Tableau de Bord</h1>
                <div class="text-gray-600">Bienvenue dans votre gestionnaire de stock</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full text-blue-500 mr-4">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase">Total Produits</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $total_produits; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full text-green-500 mr-4">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase">Catégories</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $total_categories; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-full text-red-500 mr-4">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase">Rupture / Alerte</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $alerte_stock; ?></p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-12 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Derniers Produits Ajoutés</h2>
                <p class="text-gray-500 italic">Nous allons coder cette liste à l'étape suivante...</p>
            </div>
        </div>
    </div>

</body>
</html>