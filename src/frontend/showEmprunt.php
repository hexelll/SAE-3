<?php
    require_once "../utils/gestionConnexion.php";
    require_once "../utils/materiel.php";
    require_once "../utils/emprunt.php";
    require_once "../utils/admin.php";
    require_once "../utils/emprunter.php";

    $items = (new EmprunterDAO())->findByEmpruntId($_REQUEST["idEmprunt"]);
    $materiels = [];
    foreach($items as $item) {
        $materiels[] = $item->materiel;
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
            <a href="index.php" class="back-link">← Retour</a>
            <h1>Tableau de bord Administrateur</h1>
        </div>
        <div>
            <h2>Composants</h2>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Total</th>
                            <th>Dispo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materiels as $m): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($m->nom); ?></td>
                            <td><?php echo htmlspecialchars($m->type); ?></td>
                            <td><?php echo $m->stock_total; ?></td>
                            <td><?php echo $m->stock_disponible; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
</body>
</html>