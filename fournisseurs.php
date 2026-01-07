<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

$message = "";

// Traitement de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_f'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $contact = htmlspecialchars($_POST['contact']);
    $tel = htmlspecialchars($_POST['telephone']);
    $email = htmlspecialchars($_POST['email']);
    
    $stmt = $pdo->prepare("INSERT INTO fournisseurs (nom, contact, telephone, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $contact, $tel, $email]);
    $message = "Fournisseur ajouté avec succès !";
}

$fournisseurs = $pdo->query("SELECT * FROM fournisseurs ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fournisseurs - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-8">Gestion des Fournisseurs</h1>

        <?php if($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-md h-fit">
                <h2 class="text-xl font-bold mb-4 text-blue-600 italic">Ajouter un Fournisseur</h2>
                <form method="POST" class="space-y-4">
                    <input type="text" name="nom" placeholder="Nom de l'entreprise" required class="w-full px-4 py-2 border rounded-lg">
                    <input type="text" name="contact" placeholder="Nom du contact" class="w-full px-4 py-2 border rounded-lg">
                    <input type="text" name="telephone" placeholder="Téléphone" class="w-full px-4 py-2 border rounded-lg">
                    <input type="email" name="email" placeholder="Email" class="w-full px-4 py-2 border rounded-lg">
                    <button type="submit" name="ajouter_f" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold">Enregistrer</button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-800 text-white text-xs">
                        <tr>
                            <th class="px-6 py-3 uppercase">Entreprise</th>
                            <th class="px-6 py-3 uppercase">Contact</th>
                            <th class="px-6 py-3 uppercase">Téléphone</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm text-gray-700">
                        <?php foreach($fournisseurs as $f): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($f['nom']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($f['contact']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($f['telephone']); ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="supprimer_fournisseur.php?id=<?= $f['id'] ?>" class="text-red-500 hover:underline" onclick="return confirm('Supprimer ce fournisseur ?')"><i class="fas fa-trash"></i></a>
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