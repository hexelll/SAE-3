<?php
require '../utils/gestionConnexion.php';
require '../utils/admin.php';
$pdo = Connexion::getConnexion();
function quit() {
    header("Location: ConnexionAdmin.php");
    exit();
}
try {
    if (isset($_REQUEST["mdp"]) && isset($_REQUEST["id"])) {
        $hashmdp = $_REQUEST["mdp"];
        $admin = (new AdminDAO())->findById($_REQUEST["id"]);
        if ($admin->hashMdp != $hashmdp)
            quit();
    }else 
        quit();
}
catch(e) {quit();}
$sql_resa = "
    SELECT e.idEmprunt, e.emailEmprunt, e.dateEmprunt, e.dateRetourPrevue, e.statutEmprunt, 
           GROUP_CONCAT(CONCAT(m.nomMateriel, ' (x', emp.quantité, ')') SEPARATOR ', ') as details
    FROM Emprunt e
    JOIN Emprunter emp ON e.idEmprunt = emp.idEmprunt
    JOIN Materiel m ON emp.idMateriel = m.idMateriel
    GROUP BY e.idEmprunt
    ORDER BY e.dateEmprunt DESC";
$stmt_resa = $pdo->query($sql_resa);
$reservations = $stmt_resa->fetchAll(PDO::FETCH_ASSOC);

$sql_mat = "SELECT * FROM Materiel";
$stmt_mat = $pdo->query($sql_mat);
$materiels = $stmt_mat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Tableau de bord</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            
            document.getElementById(tabId + '-tab').style.display = 'block';
            event.target.classList.add('active');
        }
    </script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="index.php" class="back-link">← Retour</a>
            <h1>Tableau de bord Administrateur</h1>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="showTab('reservations')">Demandes</button>
            <button class="tab-btn" onclick="showTab('materials')">Inventaire</button>
            <button class="tab-btn" onclick="showTab('modify')">Modifier</button>
        </div>

        <div id="reservations-tab" class="tab-content active">
            <h2>Suivi des emprunts</h2>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Matériel</th>
                            <th>Dates</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $r): ?>
                        <tr>
                            <td>#<?php echo $r['idEmprunt']; ?></td>
                            <td><?php echo htmlspecialchars($r['emailEmprunt']); ?></td>
                            <td><?php echo htmlspecialchars($r['details']); ?></td>
                            <td><?php echo $r['dateEmprunt']; ?> au <?php echo $r['dateRetourPrevue']; ?></td>
                            <td>
                                <span class="badge <?php echo ($r['statutEmprunt']=='validé'?'badge-success':($r['statutEmprunt']=='refusé'?'badge-danger':'badge-warning')); ?>">
                                    <?php echo htmlspecialchars($r['statutEmprunt']); ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <?php if($r['statutEmprunt'] == 'en cours'): ?>
                                <form action="traitement_admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="idEmprunt" value="<?php echo $r['idEmprunt']; ?>">
                                    <input type="hidden" name="action" value="valider">
                                    <button type="submit" class="btn btn-success">✓</button>
                                </form>
                                <form action="traitement_admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="idEmprunt" value="<?php echo $r['idEmprunt']; ?>">
                                    <input type="hidden" name="action" value="refuser">
                                    <button type="submit" class="btn btn-danger">✗</button>
                                </form>
                                <?php else: ?>
                                    <small>Traité</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="materials-tab" class="tab-content">
            <h2>Inventaire</h2>
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
                            <td><?php echo htmlspecialchars($m['nomMateriel']); ?></td>
                            <td><?php echo htmlspecialchars($m['typeMateriel']); ?></td>
                            <td><?php echo $m['stockTotal']; ?></td>
                            <td><?php echo $m['stockDisponible']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="modify-tab" class="tab-content">
            <h2>Inventaire</h2>
            <div class="form-section">
                <h2>Vos informations</h2>
                <div class="form-group">
                    <label>Email :</label>
                    <input type="email" name="email" required class="form-input" placeholder="votre@email.com">
                </div>
                <div class="form-group">
                    <label>Motif de l'emprunt :</label>
                    <textarea name="motif" required class="form-input" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Date début :</label>
                    <input type="date" name="date_debut" id="date_from" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Date fin (prévue) :</label>
                    <input type="date" name="date_fin" id="date_to" required class="form-input">
                </div>
                

                <button type="button" class="btn btn-primary" id="liveToastBtn">Show live toast</button>

                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                    <img src="..." class="rounded me-2" alt="...">
                    <strong class="me-auto">Bootstrap</strong>
                    <small>11 mins ago</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                    Hello, world! This is a toast message.
                    </div>
                </div>
                </div>


            </div>
        </div>
    </div>
</body>
</html>