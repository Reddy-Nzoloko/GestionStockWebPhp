<?php
// 1. Inclusion de votre fichier de connexion
require_once 'connexion.php';

// 2. Définition des identifiants
$nom_utilisateur = "admin";
$mot_de_passe_en_clair = "admin123"; // C'est ce que vous taperez dans le formulaire

// 3. Hachage du mot de passe (Méthode sécurisée PHP)
$mot_de_passe_hache = password_hash($mot_de_passe_en_clair, PASSWORD_DEFAULT);

try {
    // 4. Préparation de la requête d'insertion
    $sql = "INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, role) VALUES (:nom, :pass, :role)";
    $stmt = $pdo->prepare($sql);
    
    // 5. Exécution avec les données
    $stmt->execute([
        ':nom'  => $nom_utilisateur,
        ':pass' => $mot_de_passe_hache,
        ':role' => 'admin'
    ]);

    echo "<div style='font-family: sans-serif; padding: 20px; background: #dcfce7; color: #166534; border-radius: 8px;'>";
    echo "<strong>Succès !</strong> L'administrateur a été créé.<br><br>";
    echo "Utilisateur : <b>" . $nom_utilisateur . "</b><br>";
    echo "Mot de passe : <b>" . $mot_de_passe_en_clair . "</b><br><br>";
    echo "<i>Note : Le mot de passe stocké en base ressemble à ceci : " . substr($mot_de_passe_hache, 0, 20) . "...</i><br><br>";
    echo "<a href='login.php' style='color: #15803d; font-weight: bold;'>Aller vers la page de connexion</a>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='color: red; padding: 20px;'>Erreur : " . $e->getMessage() . "</div>";
}
?>