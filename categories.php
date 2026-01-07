<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'connexion.php';

$message = "";
$erreur = "";

// 1. TRAITEMENT DE L'AJOUT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_cat'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);

    if (!empty($nom)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (nom, description) VALUES (?, ?)");
            $stmt->execute([$nom, $description]);
            $message = "La catégorie '$nom' a été ajoutée avec succès !";
        } catch (PDOException $e) {
            $erreur = "Erreur : " . $e->getMessage();
        }
    } else {
        $erreur = "Le nom de la catégorie est obligatoire.";
    }
}

// 2. RÉCUPÉRATION DE LA LISTE
$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen overflow-hidden">
        
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 overflow-y-auto p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Gestion des Catégories</h1>

            <?php if($message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="bg-white p-6 rounded-xl shadow-md h-fit">
                    <h2 class="text-xl font-bold mb-6 text-blue-600 flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i> Nouvelle catégorie
                    </h2>
                    
                    <form action="" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nom de la catégorie *</label>
                            <input type="text" name="nom" required placeholder="Ex: Électronique, Boisson..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="4" placeholder="Brève description..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"></textarea>
                        </div>

                        <button type="submit" name="ajouter_cat" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg shadow transition duration-200">
                            Enregistrer
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gray-50 p-4 border-b">
                        <h2 class="font-bold text-gray-700">Catégories existantes</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 text-xs uppercase">
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Nom</th>
                                    <th class="px-6 py-3">Description</th>
                                    <th class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php if(empty($categories)): ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">
                                            Aucune catégorie pour le moment.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($categories as $cat): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-gray-400">#<?php echo $cat['id']; ?></td>
                                        <td class="px-6 py-4 font-bold text-gray-800"><?php echo htmlspecialchars($cat['nom']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($cat['description']); ?></td>
                                        <td class="px-6 py-4 text-center space-x-2">
                                            <a href="modifier_categorie.php?id=<?php echo $cat['id']; ?>" class="text-blue-500 hover:text-blue-700">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="supprimer_categorie.php?id=<?php echo $cat['id']; ?>" 
                                               class="text-red-500 hover:text-red-700" 
                                               onclick="return confirm('Attention : Supprimer cette catégorie pourrait affecter les produits liés. Continuer ?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
</body>
</html>