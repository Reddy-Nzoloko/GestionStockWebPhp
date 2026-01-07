<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

// Récupération avec jointure Clients et Fournisseurs (si vous avez mis à jour vos tables)
$sql = "SELECT m.*, p.nom as produit_nom, p.prix_unitaire, 
               IFNULL(c.nom, 'N/A') as client_nom, 
               IFNULL(f.nom, 'N/A') as fournisseur_nom
        FROM mouvements m 
        JOIN produits p ON m.produit_id = p.id 
        LEFT JOIN clients c ON m.client_id = c.id
        LEFT JOIN fournisseurs f ON m.fournisseur_id = f.id
        ORDER BY m.date_mouvement DESC";
$mouvements = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mouvements - StockMaster</title>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Style spécifique pour l'impression */
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white; }
            .flex { display: block; }
        }
        .print-only { display: none; }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    
    <div class="no-print w-64">
        <?php include 'sidebar.php'; ?>
    </div>

    <div class="flex-1 p-8 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 no-print">
            <h1 class="text-3xl font-bold text-gray-800">Mouvements de Stock</h1>
            <div class="space-x-2">
                <button onclick="window.print()" class="bg-gray-700 text-white px-4 py-2 rounded-lg font-bold hover:bg-gray-800 transition shadow">
                    <i class="fas fa-file-pdf mr-2"></i> Rapport d'état
                </button>
                <a href="ajouter_mouvement.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg">
                    <i class="fas fa-exchange-alt mr-2"></i> Nouveau mouvement
                </a>
            </div>
        </div>

        <div class="print-only mb-8 text-center">
            <h1 class="text-2xl font-bold">RAPPORT D'ACTIVITÉ - StockMaster</h1>
            <p>Date du rapport : <?php echo date('d/m/Y H:i'); ?></p>
            <hr class="my-4">
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-800 text-white uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Produit</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Tiers (Client/Fourn.)</th>
                        <th class="px-6 py-4">Quantité</th>
                        <th class="px-6 py-4 no-print text-center">Facture</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach($mouvements as $m): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm"><?php echo date('d/m/Y H:i', strtotime($m['date_mouvement'])); ?></td>
                        <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($m['produit_nom']); ?></td>
                        <td class="px-6 py-4 text-xs font-bold uppercase">
                            <?php echo ($m['type_mouvement'] == 'entree') ? '<span class="text-green-600">Entrée</span>' : '<span class="text-red-600">Sortie</span>'; ?>
                        </td>
                        <td class="px-6 py-4 text-sm italic">
                            <?php echo ($m['type_mouvement'] == 'entree') ? htmlspecialchars($m['fournisseur_nom']) : htmlspecialchars($m['client_nom']); ?>
                        </td>
                        <td class="px-6 py-4 font-bold"><?php echo ($m['type_mouvement'] == 'entree' ? '+' : '-') . $m['quantite']; ?></td>
                        <td class="px-6 py-4 no-print text-center">
                            <?php if($m['type_mouvement'] == 'sortie'): ?>
                                <a href="facture.php?id=<?php echo $m['id']; ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-print"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>