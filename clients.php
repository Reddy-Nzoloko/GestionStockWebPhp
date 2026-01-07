<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

$message = "";

// Traitement de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_c'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $tel = htmlspecialchars($_POST['telephone']);
    $adresse = htmlspecialchars($_POST['adresse']);
    
    $stmt = $pdo->prepare("INSERT INTO clients (nom, telephone, adresse) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $tel, $adresse]);
    $message = "Client enregistré !";
}

$clients = $pdo->query("SELECT * FROM clients ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Clients - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-8">Portefeuille Clients</h1>

        <?php if($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-md h-fit">
                <h2 class="text-xl font-bold mb-4 text-purple-600 italic">Nouveau Client</h2>
                <form method="POST" class="space-y-4">
                    <input type="text" name="nom" placeholder="Nom complet" required class="w-full px-4 py-2 border rounded-lg">
                    <input type="text" name="telephone" placeholder="Téléphone" class="w-full px-4 py-2 border rounded-lg">
                    <textarea name="adresse" placeholder="Adresse complète" class="w-full px-4 py-2 border rounded-lg"></textarea>
                    <button type="submit" name="ajouter_c" class="w-full bg-purple-600 text-white py-2 rounded-lg font-bold shadow-md">Ajouter au fichier</button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-800 text-white text-xs">
                        <tr>
                            <th class="px-6 py-3 uppercase">Nom du Client</th>
                            <th class="px-6 py-3 uppercase">Téléphone</th>
                            <th class="px-6 py-3 uppercase">Adresse</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        <?php foreach($clients as $c): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($c['nom']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($c['telephone']); ?></td>
                            <td class="px-6 py-4 text-xs text-gray-500"><?php echo htmlspecialchars($c['adresse']); ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="supprimer_client.php?id=<?= $c['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Supprimer ce client ?')"><i class="fas fa-trash"></i></a>
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