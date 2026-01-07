<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

// 1. CHARGEMENT DES DONNÉES POUR LES SÉLECTEURS
$clients = $pdo->query("SELECT id, nom FROM clients ORDER BY nom ASC")->fetchAll();
$fournisseurs = $pdo->query("SELECT id, nom FROM fournisseurs ORDER BY nom ASC")->fetchAll();
$produits = $pdo->query("SELECT id, nom, quantite_stock FROM produits ORDER BY nom ASC")->fetchAll();

$erreur = "";

// 2. TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = $_POST['produit_id'];
    $type = $_POST['type'];
    $quantite = intval($_POST['quantite']);
    $commentaire = htmlspecialchars($_POST['commentaire']);
    
    // Récupération des tiers (Client ou Fournisseur)
    $client_id = (!empty($_POST['client_id']) && $type == 'sortie') ? $_POST['client_id'] : null;
    $fournisseur_id = (!empty($_POST['fournisseur_id']) && $type == 'entree') ? $_POST['fournisseur_id'] : null;

    if ($produit_id && $quantite > 0) {
        try {
            $pdo->beginTransaction();

            // A. Vérification du stock pour les sorties
            if ($type == 'sortie') {
                $check = $pdo->prepare("SELECT quantite_stock FROM produits WHERE id = ?");
                $check->execute([$produit_id]);
                $stock_actuel = $check->fetchColumn();

                if ($stock_actuel < $quantite) {
                    throw new Exception("Stock insuffisant ! Il ne reste que $stock_actuel unité(s).");
                }
                $updateSql = "UPDATE produits SET quantite_stock = quantite_stock - ? WHERE id = ?";
            } else {
                $updateSql = "UPDATE produits SET quantite_stock = quantite_stock + ? WHERE id = ?";
            }

            // B. Insertion du mouvement avec les IDs des tiers
            $stmt = $pdo->prepare("INSERT INTO mouvements (produit_id, type_mouvement, quantite, client_id, fournisseur_id, commentaire) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$produit_id, $type, $quantite, $client_id, $fournisseur_id, $commentaire]);

            // C. Mise à jour de la table produits
            $update = $pdo->prepare($updateSql);
            $update->execute([$quantite, $produit_id]);
            
            $pdo->commit();
            header('Location: mouvements.php?success=1');
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            $erreur = $e->getMessage();
        }
    } else {
        $erreur = "Veuillez saisir une quantité valide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau Mouvement - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-2xl mx-auto">
            
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">Enregistrer une opération</h2>
                <a href="mouvements.php" class="text-gray-500 hover:text-gray-700 text-sm italic">Retour</a>
            </div>
            
            <?php if($erreur): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <?php echo $erreur; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <form method="POST" class="space-y-5">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Produit concerné</label>
                        <select name="produit_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Sélectionner le produit --</option>
                            <?php foreach($produits as $p): ?>
                                <option value="<?php echo $p['id']; ?>">
                                    <?php echo htmlspecialchars($p['nom']); ?> (Dispo: <?php echo $p['quantite_stock']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Type d'opération</label>
                        <select name="type" id="type_mouvement" onchange="toggleTiers()" class="w-full px-4 py-2 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="entree">🟢 ENTRÉE (Réapprovisionnement / Achat)</option>
                            <option value="sortie">🔴 SORTIE (Vente / Livraison)</option>
                        </select>
                    </div>

                    <div id="div_fournisseur" class="p-4 bg-green-50 rounded-lg border border-green-100">
                        <label class="block text-sm font-bold text-green-700 mb-1">Source (Fournisseur)</label>
                        <select name="fournisseur_id" class="w-full px-4 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                            <option value="">-- Choisir le fournisseur --</option>
                            <?php foreach($fournisseurs as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="div_client" class="hidden p-4 bg-red-50 rounded-lg border border-red-100">
                        <label class="block text-sm font-bold text-red-700 mb-1">Destination (Client)</label>
                        <select name="client_id" class="w-full px-4 py-2 border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                            <option value="">-- Choisir le client --</option>
                            <?php foreach($clients as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Quantité</label>
                            <input type="number" name="quantite" min="1" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Ex: 10">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Note / Commentaire</label>
                            <input type="text" name="commentaire" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Numéro facture, motif...">
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit" class="flex-1 bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 shadow-lg transition">
                            <i class="fas fa-check-circle mr-2"></i> Confirmer l'opération
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function toggleTiers() {
        const type = document.getElementById('type_mouvement').value;
        const divF = document.getElementById('div_fournisseur');
        const divC = document.getElementById('div_client');
        
        if(type === 'entree') {
            divF.classList.remove('hidden');
            divC.classList.add('hidden');
        } else {
            divF.classList.add('hidden');
            divC.classList.remove('hidden');
        }
    }
    </script>
</body>
</html>