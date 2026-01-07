<?php
session_start();
if (!isset($_SESSION['user_id'])) exit();
require_once 'connexion.php';

$id = $_GET['id'] ?? null;
$sql = "SELECT m.*, p.nom as p_nom, p.prix_unitaire, c.nom as c_nom, c.adresse, c.telephone 
        FROM mouvements m 
        JOIN produits p ON m.produit_id = p.id 
        LEFT JOIN clients c ON m.client_id = c.id 
        WHERE m.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$m = $stmt->fetch();

if (!$m) exit("Mouvement non trouvé.");
$total = $m['quantite'] * $m['prix_unitaire'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #<?php echo $m['id']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
</head>
<body class="bg-white p-10" onload="window.print()">
    <div class="max-w-2xl mx-auto border p-8">
        <div class="flex justify-between mb-8">
            <div>
                <h1 class="text-4xl font-black text-blue-600">StockMaster</h1>
                <p class="text-sm text-gray-500">Votre Entreprise S.A.R.L</p>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-bold uppercase text-gray-400">Facture</h2>
                <p>N° #<?php echo str_pad($m['id'], 5, "0", STR_PAD_LEFT); ?></p>
                <p>Date : <?php echo date('d/m/Y', strtotime($m['date_mouvement'])); ?></p>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="font-bold border-b mb-2">FACTURÉ À :</h3>
            <p class="font-bold"><?php echo htmlspecialchars($m['c_nom']); ?></p>
            <p><?php echo htmlspecialchars($m['adresse']); ?></p>
            <p><?php echo htmlspecialchars($m['telephone']); ?></p>
        </div>

        <table class="w-full mb-8">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">Désignation</th>
                    <th class="p-2 text-right">Prix Unitaire</th>
                    <th class="p-2 text-center">Quantité</th>
                    <th class="p-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <tr>
                    <td class="p-2"><?php echo htmlspecialchars($m['p_nom']); ?></td>
                    <td class="p-2 text-right"><?php echo number_format($m['prix_unitaire'], 2); ?> €</td>
                    <td class="p-2 text-center"><?php echo $m['quantite']; ?></td>
                    <td class="p-2 text-right font-bold"><?php echo number_format($total, 2); ?> €</td>
                </tr>
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-1/2">
                <div class="flex justify-between p-2 bg-gray-100 font-bold text-xl">
                    <span>TOTAL TTC :</span>
                    <span><?php echo number_format($total, 2); ?> €</span>
                </div>
            </div>
        </div>

        <div class="mt-20 text-center text-xs text-gray-400 border-t pt-4">
            Merci de votre confiance. Facture générée automatiquement par StockMaster.
        </div>
    </div>
</body>
</html>