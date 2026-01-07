<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$cat = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $desc = htmlspecialchars($_POST['description']);
    $pdo->prepare("UPDATE categories SET nom = ?, description = ? WHERE id = ?")->execute([$nom, $desc, $id]);
    header('Location: categories.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Catégorie</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 p-8">
        <div class="max-w-md bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-bold mb-4">Modifier la Catégorie</h2>
            <form method="POST" class="space-y-4">
                <input type="text" name="nom" value="<?php echo htmlspecialchars($cat['nom']); ?>" class="w-full px-3 py-2 border rounded-lg">
                <textarea name="description" class="w-full px-3 py-2 border rounded-lg"><?php echo htmlspecialchars($cat['description']); ?></textarea>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-bold">Sauvegarder</button>
            </form>
        </div>
    </div>
</body>
</html>