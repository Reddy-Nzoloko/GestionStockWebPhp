<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

// --- 1. RÉCUPÉRATION DES STATISTIQUES GLOBALES ---
$total_produits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$valeur_stock = $pdo->query("SELECT SUM(prix_unitaire * quantite_stock) FROM produits")->fetchColumn();
$alertes = $pdo->query("SELECT COUNT(*) FROM produits WHERE quantite_stock <= 5")->fetchColumn();

// --- 2. TOP 5 DES VENTES (Le plus vendu) ---
$top_vendus = $pdo->query("SELECT p.nom, SUM(m.quantite) as total 
    FROM mouvements m JOIN produits p ON m.produit_id = p.id 
    WHERE m.type_mouvement = 'sortie' GROUP BY p.id ORDER BY total DESC LIMIT 5")->fetchAll();

// --- 3. FLOP 5 DES VENTES (Le moins vendu ou pas vendu) ---
$flop_vendus = $pdo->query("SELECT p.nom, IFNULL(SUM(m.quantite), 0) as total 
    FROM produits p LEFT JOIN mouvements m ON p.id = m.produit_id AND m.type_mouvement = 'sortie' 
    GROUP BY p.id ORDER BY total ASC LIMIT 5")->fetchAll();

// --- 4. LISTE DES PRODUITS AVEC ÉTAT DES STOCKS ---
$inventaire = $pdo->query("SELECT p.*, c.nom as cat_nom FROM produits p 
                           LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.quantite_stock ASC")->fetchAll();

// --- 5. DONNÉES POUR LE GRAPHIQUE DES MOUVEMENTS (7 derniers jours) ---
$dates = []; $entrees = []; $sorties = [];
for($i=6; $i>=0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $dates[] = date('d M', strtotime($d));
    $entrees[] = $pdo->query("SELECT SUM(quantite) FROM mouvements WHERE type_mouvement='entree' AND DATE(date_mouvement)='$d'")->fetchColumn() ?: 0;
    $sorties[] = $pdo->query("SELECT SUM(quantite) FROM mouvements WHERE type_mouvement='sortie' AND DATE(date_mouvement)='$d'")->fetchColumn() ?: 0;
}
?>
<!-- Html pour cette page d'index -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans flex flex-col lg:flex-row h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 overflow-y-auto p-4 md:p-8">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 pt-12 lg:pt-0">
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800">Tableau de Bord</h1>
            <div class="flex space-x-2 w-full sm:w-auto">
                <a href="mouvements.php?type=entree" class="flex-1 text-center bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow hover:bg-green-700 transition">
                   <i class="fas fa-arrow-down mr-1"></i> Entrées
                </a>
                <a href="mouvements.php?type=sortie" class="flex-1 text-center bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow hover:bg-red-700 transition">
                   <i class="fas fa-arrow-up mr-1"></i> Sorties
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                <p class="text-gray-500 text-xs font-bold uppercase">Total Produits</p>
                <p class="text-3xl font-black text-gray-800"><?php echo $total_produits; ?></p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500">
                <p class="text-gray-500 text-xs font-bold uppercase">Valeur du Stock</p>
                <p class="text-3xl font-black text-gray-800"><?php echo number_format($valeur_stock, 2); ?> $</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500 sm:col-span-2 lg:col-span-1">
                <p class="text-gray-500 text-xs font-bold uppercase">Alertes Rupture</p>
                <p class="text-3xl font-black text-red-600"><?php echo $alertes; ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm overflow-hidden">
                <h3 class="font-bold text-gray-700 mb-4 text-sm md:text-base">Activité des 7 derniers jours</h3>
                <div class="h-[250px] md:h-auto">
                    <canvas id="mouvementChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="font-bold text-gray-700 mb-4 text-center">Performance Produits</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 h-full">
                    <div class="border-b sm:border-b-0 sm:border-r pb-4 sm:pb-0 sm:pr-4">
                        <p class="text-xs font-bold text-green-600 mb-3 uppercase flex items-center">
                            <i class="fas fa-trophy mr-2"></i> Plus vendus
                        </p>
                        <?php foreach($top_vendus as $t): ?>
                            <div class="flex justify-between mb-2 text-sm border-b border-gray-50 pb-1">
                                <span class="text-gray-600 truncate mr-2"><?php echo $t['nom']; ?></span>
                                <span class="font-bold text-gray-800"><?php echo $t['total']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="sm:pl-2">
                        <p class="text-xs font-bold text-red-600 mb-3 uppercase flex items-center">
                            <i class="fas fa-chart-line fa-flip-vertical mr-2"></i> Moins vendus
                        </p>
                        <?php foreach($flop_vendus as $f): ?>
                            <div class="flex justify-between mb-2 text-sm border-b border-gray-50 pb-1">
                                <span class="text-gray-600 truncate mr-2"><?php echo $f['nom']; ?></span>
                                <span class="font-bold text-gray-400"><?php echo $f['total']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 italic">État des stocks</h3>
                <span class="text-[10px] bg-gray-200 px-2 py-1 rounded lg:hidden">Faites glisser →</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[600px]">
                    <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-sm">Produit</th>
                            <th class="px-6 py-3">Catégorie</th>
                            <th class="px-6 py-3">Prix</th>
                            <th class="px-6 py-3">Stock</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        <?php foreach($inventaire as $item): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($item['nom']); ?></td>
                            <td class="px-6 py-4 text-gray-500"><?php echo htmlspecialchars($item['cat_nom']); ?></td>
                            <td class="px-6 py-4"><?php echo number_format($item['prix_unitaire'], 2); ?>$</td>
                            <td class="px-6 py-4 font-black text-blue-600"><?php echo $item['quantite_stock']; ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php if($item['quantite_stock'] <= 0): ?>
                                    <span class="bg-red-500 text-white px-2 py-1 rounded-full text-[10px] uppercase font-bold">Rupture</span>
                                <?php elseif($item['quantite_stock'] <= 5): ?>
                                    <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-[10px] uppercase font-bold">Critique</span>
                                <?php else: ?>
                                    <span class="bg-green-500 text-white px-2 py-1 rounded-full text-[10px] uppercase font-bold">Ok</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('mouvementChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Entrées',
                    data: <?php echo json_encode($entrees); ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Sorties',
                    data: <?php echo json_encode($sorties); ?>,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>