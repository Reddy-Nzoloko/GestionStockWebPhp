<?php
session_start();
// Sécurité : Seul un admin connecté peut ajouter un autre admin
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
require_once 'connexion.php';

$erreur = "";
$succes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom_complet']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation simple
    if ($password !== $confirm_password) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email existe déjà
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            $erreur = "Cet email est déjà utilisé par un autre administrateur.";
        } else {
            // Hachage du mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_complet, email, mot_de_passe) VALUES (?, ?, ?)");
                $stmt->execute([$nom, $email, $hash]);
                $succes = "Nouvel administrateur créé avec succès !";
            } catch (PDOException $e) {
                $erreur = "Erreur lors de l'inscription : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Admin - StockMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">
        
        <div class="p-4 md:p-8 flex-1 flex items-center justify-center">
            <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                
                <div class="bg-blue-600 p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4">
                        <i class="fas fa-user-shield text-white text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white uppercase tracking-wide">Nouvel Admin</h2>
                    <p class="text-blue-100 text-sm italic">Enregistrez un collaborateur RedDev</p>
                </div>

                <div class="p-8">
                    <?php if($erreur): ?>
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 mb-6 text-sm flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $erreur; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($succes): ?>
                        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 mb-6 text-sm flex items-center">
                            <i class="fas fa-check-circle mr-2"></i> <?php echo $succes; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Nom Complet</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="nom_complet" required 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                    placeholder="Ex: John Doe">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Email professionnel</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email" required 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                    placeholder="email@domaine.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Mot de passe</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="password" required 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Confirmer le mot de passe</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-check-double"></i>
                                </span>
                                <input type="password" name="confirm_password" required 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transform transition active:scale-95 flex items-center justify-center">
                            <i class="fas fa-user-plus mr-2"></i> Créer le compte Admin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>