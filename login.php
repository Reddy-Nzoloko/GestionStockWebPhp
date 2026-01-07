<?php
session_start();
require_once 'connexion.php';

// Si l'utilisateur est déjà connecté, on le redirige vers l'index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE nom_utilisateur = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // password_verify compare le mot de passe saisi avec le hachage en BDD
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nom_utilisateur'];
        $_SESSION['role'] = $user['role'];
        
        header('Location: index.php');
        exit();
    } else {
        $erreur = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - StockMaster</title>
    <link rel="icon" href="logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">StockMaster</h1>
            <p class="text-gray-500">Connectez-vous pour gérer votre stock</p>
        </div>
        
        <?php if($erreur): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-6 rounded shadow-sm">
                <?php echo $erreur; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nom d'utilisateur</label>
                <input type="text" name="username" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mot de passe</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transform active:scale-95 transition duration-150">
                Se connecter
            </button>
            <a href="creer_admin.php">Ajout admin</a>
        </form>
    </div>
</body>
</html>