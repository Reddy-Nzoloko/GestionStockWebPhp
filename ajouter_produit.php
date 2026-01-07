<?php
session_start();
// Sécurité : Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'connexion.php';

$message = "";
$erreur = "";

// 1. Récupérer les catégories pour remplir la liste déroulante
$stmt_cat = $pdo->query("SELECT * FROM categories ORDER BY nom ASC");
$categories = $stmt_cat->fetchAll();

// 2. Traitement du formulaire quand il est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $prix = $_POST['prix'];
    $quantite = $_POST['quantite'];
    $categorie_id = $_POST['categorie_id'];

    if (!empty($nom) && !empty($prix)) {
        try {
            // Insertion du produit
            $sql = "INSERT INTO produits (nom, description, prix_unitaire, quantite_stock, categorie_id) 
                    VALUES (:nom, :desc, :prix, :qty, :cat)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'  => $nom,
                ':desc' => $description,
                ':prix' => $prix,
                ':qty'  => $quantite,
                ':cat'  => $categorie_id
            ]);

            // Optionnel : Enregistrer le mouvement initial dans la table 'mouvements'
            $produit_id = $pdo->lastInsertId();
            $sql_mouv = "INSERT INTO mouvements (produit_id, type_mouvement, quantite, commentaire) 
                         VALUES (?, 'entree', ?, 'Stock initial lors de la création')";
            $pdo->prepare($sql_mouv)->execute([$produit_id, $quantite]);

            $message = "Produit ajouté avec succès au catalogue !";
        } catch (PDOException $e) {
            $erreur = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    } else {
        $erreur = "Veuillez remplir les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Produit - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen overflow-hidden">
        
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-3xl mx-auto">
                
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-3xl font-bold text-gray-800">Ajouter un nouveau produit</h1>
                    <a href="produits.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
                    </a>
                </div>

                <?php if($message): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                        <i class="fas fa-check-circle mr-2"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if($erreur): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $erreur; ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-xl shadow-lg p-8">
                    <form action="" method="POST" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nom du produit *</label>
                                <input type="text" name="nom" required placeholder="Ex: iPhone 15 Pro"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                            </div>

                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Catégorie</label>
                                <select name="categorie_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            <?php echo htmlspecialchars($cat['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3" placeholder="Détails du produit..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Prix Unitaire (€) *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-400">€</span>
                                    <input type="number" step="0.01" name="prix" required placeholder="0.00"
                                        class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Quantité initiale en stock *</label>
                                <input type="number" name="quantite" required min="0" placeholder="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                                <i class="fas fa-save mr-2"></i> Enregistrer le produit
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</body>
</html>