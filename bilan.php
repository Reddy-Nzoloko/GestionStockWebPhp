<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

// 1. ANALYSE PAR CATÉGORIE (Valeur du stock actuel)
$sql_cat = "SELECT c.nom as categorie, 
            COUNT(p.id) as nb_produits,
            SUM(p.quantite_stock) as total_unites,
            SUM(p.quantite_stock * p.prix_unitaire) as valeur_achat
            FROM categories c
            LEFT JOIN produits p ON c.id = p.categorie_id
            GROUP BY c.id";
$bilan_categories = $pdo->query($sql_cat)->fetchAll();

// 2. ANALYSE DES VENTES (Rentabilité réelle sur les sorties)
$sql_ventes = "SELECT c.nom as categorie,
               SUM(m.quantite) as unites_vendues,
               SUM(m.quantite * p.prix_unitaire) as ca_genere
               FROM mouvements m
               JOIN produits p ON m.produit_id = p.id
               JOIN categories c ON p.categorie_id = c.id
               WHERE m.type_mouvement = 'sortie'
               GROUP BY c.id";
$bilan_ventes = $pdo->query($sql_ventes)->fetchAll();

// 3. TOTAUX GÉNÉRAUX
$global_valeur = $pdo->query("SELECT SUM(quantite_stock * prix_unitaire) FROM produits")->fetchColumn();
$global_ventes = $pdo->query("SELECT SUM(m.quantite * p.prix_unitaire) 
                             FROM mouvements m JOIN produits p ON m.produit_id = p.id 
                             WHERE m.type_mouvement = 'sortie'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bilan & Rentabilité - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 p-8 overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800">Rapport de Rentabilité</h1>
            <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">
                <i class="fas fa-print mr-2"></i> Imprimer le Bilan
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Valeur Actuelle du Stock (Actif)</h3>
                <p class="text-4xl font-black text-blue-600"><?php echo number_format($global_valeur, 2); ?> $</p>
                <p class="text-xs text-gray-400 mt-2 italic">Basé sur le prix unitaire × stock en magasin</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Chiffre d'Affaires Réalisé (Ventes)</h3>
                <p class="text-4xl font-black text-green-600"><?php echo number_format($global_ventes, 2); ?> $</p>
                <p class="text-xs text-gray-400 mt-2 italic">Cumul total des sorties de stock enregistrées</p>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-4 bg-gray-800 text-white font-bold">Analyse du Stock par Catégorie</div>
                <table class="w-full text-left">
                    <thead class="bg-gray-100 text-xs uppercase font-bold text-gray-600">
                        <tr>
                            <th class="px-6 py-4">Catégorie</th>
                            <th class="px-6 py-4">Réf. Différentes</th>
                            <th class="px-6 py-4">Unités totales</th>
                            <th class="px-6 py-4 text-right">Valeur Financière</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach($bilan_categories as $b): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($b['categorie']); ?></td>
                            <td class="px-6 py-4"><?php echo $b['nb_produits']; ?></td>
                            <td class="px-6 py-4 text-blue-600 font-medium"><?php echo (int)$b['total_unites']; ?></td>
                            <td class="px-6 py-4 text-right font-black"><?php echo number_format($b['valeur_achat'], 2); ?> $</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-4 bg-green-700 text-white font-bold">Performance des Ventes (Sorties)</div>
                <table class="w-full text-left">
                    <thead class="bg-gray-100 text-xs uppercase font-bold text-gray-600">
                        <tr>
                            <th class="px-6 py-4">Catégorie</th>
                            <th class="px-6 py-4">Volume Vendu</th>
                            <th class="px-6 py-4 text-right">CA Généré</th>
                            <th class="px-6 py-4 text-right">Poids (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach($bilan_ventes as $v): 
                            $poids = ($global_ventes > 0) ? ($v['ca_genere'] / $global_ventes) * 100 : 0;
                        ?>
                        <tr class="hover:bg-green-50 transition">
                            <td class="px-6 py-4 font-bold text-green-800"><?php echo htmlspecialchars($v['categorie']); ?></td>
                            <td class="px-6 py-4 italic"><?php echo (int)$v['unites_vendues']; ?> unités</td>
                            <td class="px-6 py-4 text-right font-bold"><?php echo number_format($v['ca_genere'], 2); ?> $</td>
                            <td class="px-6 py-4 text-right">
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                                    <?php echo round($poids, 1); ?> %
                                </span>
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