<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: produits.php'); exit(); }

// Récupérer le produit actuel
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) { header('Location: produits.php'); exit(); }

// Récupérer les catégories pour le select
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prix = $_POST['prix'];
    $cat_id = $_POST['categorie_id'];
    $desc = htmlspecialchars($_POST['description']);

    $sql = "UPDATE produits SET nom = ?, prix_unitaire = ?, categorie_id = ?, description = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$nom, $prix, $cat_id, $desc, $id]);
    header('Location: produits.php?msg=Produit modifié');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Produit</title>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 p-8">
        <div class="max-w-2xl bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Modifier : <?php echo htmlspecialchars($produit['nom']); ?></h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Nom du produit</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">Prix (€)</label>
                        <input type="number" step="0.01" name="prix" value="<?php echo $produit['prix_unitaire']; ?>" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Catégorie</label>
                        <select name="categorie_id" class="w-full px-3 py-2 border rounded-lg">
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $produit['categorie_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Description</label>
                    <textarea name="description" class="w-full px-3 py-2 border rounded-lg"><?php echo htmlspecialchars($produit['description']); ?></textarea>
                </div>
                <div class="flex justify-between items-center pt-4">
                    <a href="produits.php" class="text-gray-500 hover:underline">Annuler</a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>