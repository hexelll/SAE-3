<?php
session_start();
require_once '../utils/gestionConnexion.php';
require_once '../utils/admin.php';

$message = "";

$email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
$mdp = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';

if (!empty($email) && !empty($mdp)) {
    try {
        $adminDAO = new AdminDAO();
        $admin = $adminDAO->findByEmail($email);
        $hashSaisi = Admin::hashMdp($mdp);
        if ($admin && $admin->hashMdp === $hashSaisi) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin->id;
            $_SESSION['admin_email'] = $admin->email;
            header("Location: index.html");
            exit();
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    } catch (Exception $e) {
        $message = "Identifiants incorrects.";
    }
} else {
    $message = "Veuillez remplir tous les champs.";
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur - FabLab</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="container">

        <header class="header">
            <h1>Espace Administrateur</h1>
            <p>Veuillez vous identifier pour accéder à la gestion</p>
        </header>

        <a href="index.html" class="back-link">← Retour à l'accueil</a>

        <div class="reservation-form" style="display: block; max-width: 500px; margin: 0 auto;">
            <div class="form-section">
                <h2>Connexion</h2>

                <?php if (!empty($message)): ?>
                    <div class="badge badge-danger"
                        style="display:block; text-align:center; margin-bottom:20px; padding:10px;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="get" action="">
                    <div class="form-group">
                        <label for="email">Adresse Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="admin@fablab.fr"
                            required
                            value="<?php echo isset($_REQUEST['email']) ? htmlspecialchars($_REQUEST['email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-input"
                            placeholder="Votre mot de passe" required>
                    </div>

                    <button type="submit" class="submit-btn">Se connecter</button>
                </form>
            </div>
        </div>

    </div>

</body>

</html>