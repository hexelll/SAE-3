<?php
    require_once "../utils/gestionConnexion.php";
    require_once "../utils/materiel.php";
    require_once "../utils/emprunt.php";
    require_once "../utils/admin.php";
    require_once "../utils/emprunter.php";

    $items = (new EmprunterDAO())->findByEmpruntId($_REQUEST["idEmprunt"]);
    $materiels = [];
    foreach($items as $item) {
        $materiels[] = [$item->materiel,$item->quantité];
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Tableau de bord</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="admin.php?mdp=<?php echo $_REQUEST["mdp"] ?>&idA=<?php echo $_REQUEST["idA"] ?>" class="back-link">← Retour</a>
            <h1>Tableau de bord Administrateur</h1>
        </div>
        <div class="tab-content active">
            <h2>Composants</h2>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materiels as $t): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t[0]->nom); ?></td>
                            <td><?php echo htmlspecialchars($t[0]->type); ?></td>
                            <td><?php echo $t[1]; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>