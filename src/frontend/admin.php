<?php
require '../utils/gestionConnexion.php';
require '../utils/admin.php';
require '../utils/materiel.php';
require '../utils/emprunt.php';
$pdo = Connexion::getConnexion();
$matDAO = new MaterielDAO();
$empDAO = new EmpruntDAO();
$admDAO = new AdminDAO();
function quit() {
    header("Location: ConnexionAdmin.php");
    exit();
}
try {
    if (isset($_REQUEST["mdp"]) && isset($_REQUEST["idA"])) {
        $hashmdp = $_REQUEST["mdp"];
        $admin = $admDAO->findById($_REQUEST["idA"]);
        if ($admin->hashMdp != $hashmdp)
            quit();
    }else 
        quit();
}
catch(e) {quit();}
$emprunts = $empDAO->getAllEmprunts();
$materiels = $matDAO->getAll();

if (isset($_REQUEST["submitAjouter"])) {
    $nom = $_REQUEST["nom"];
    $desc = $_REQUEST["description"];
    $type = $_REQUEST["type"];
    $stockTotal = $_REQUEST["stockTotal"];
    $stockDispo = $_REQUEST["stockDispo"];
    $empruntable = $_REQUEST["empruntable"]=="empruntable" ? true : false;
    $nvemprunt = new Materiel(null,$nom,$desc,$type,$stockTotal,$stockDispo,$empruntable);
    $matDAO->create($nvemprunt);
}

if (isset($_REQUEST["submitModifier"])) {
    $id = $_REQUEST["id"];
    $nom = $_REQUEST["nom"];
    $desc = $_REQUEST["description"];
    $type = $_REQUEST["type"];
    $stockTotal = $_REQUEST["stockTotal"];
    $stockDispo = $_REQUEST["stockDispo"];
    $empruntable = $_REQUEST["empruntable"]=="empruntable" ? true : false;
    $emprunt = new Materiel($id,$nom,$desc,$type,$stockTotal,$stockDispo,$empruntable);
    $matDAO->update($emprunt);
}

if (isset($_REQUEST["submitDelete"])) {
    $id = $_REQUEST["id"];
    $matDAO->delete($id);
}

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
            <button class="tab-btn active" onclick="showTab('emprunts')">Demandes</button>
            <button class="tab-btn" onclick="showTab('materials')">Inventaire</button>
            <button class="tab-btn" onclick="showTab('modify')">Modifier</button>
            <button class="tab-btn" onclick="showTab('add')">Ajouter</button>
            <button class="tab-btn" onclick="showTab('del')">Supprimer</button>
        </div>

        <div id="emprunts-tab" class="tab-content active">
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
                        <?php foreach ($emprunts as $r): ?>
                        <tr>
                            <td>#<?php echo $r->id; ?></td>
                            <td><?php echo htmlspecialchars($r->email); ?></td>
                            <td><?php echo htmlspecialchars($r->motif); ?></td>
                            <td><?php echo $r->date_emprunt; ?> au <?php echo $r->date_retour_prevue; ?></td>
                            <td>
                                <span class="badge <?php echo ($r->statut_emprunt=='validé'?'badge-success':($r->statut_emprunt=='refusé'?'badge-danger':'badge-warning')); ?>">
                                    <?php echo htmlspecialchars($r->statut_emprunt); ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <?php if($r->statut_emprunt == 'en cours'): ?>
                                <form action="traitement_admin.php?<?php echo "mdp=".$admin->hashMdp."&idA=".$admin->id ?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="idEmprunt" value="<?php echo $r->id; ?>">
                                    <input type="hidden" name="action" value="valider">
                                    <button type="submit" class="btn btn-success">✓</button>
                                </form>
                                <form action="traitement_admin.php?<?php echo "mdp=".$admin->hashMdp."&idA=".$admin->id ?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="idEmprunt" value="<?php echo $r->id; ?>">
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
        <div id="modify-tab" class="tab-content">
            <form action="?<?php echo "mdp=".$admin->hashMdp."&idA=".$admin->id ?>" method="POST">
            <div class="form-section">
                <div class="form-group">
                    <label>materiel :</label>
                    <select name="id" class="form-select">
                        <?php
                            foreach($materiels as $m) {
                                echo "<option value=\"".$m->id."\">".$m->nom." : ".$m->id."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nom :</label>
                    <input type="text" name="nom" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Description :</label>
                    <textarea name="description" required class="form-input" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Type :</label>
                    <input type="text" name="type" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Stock Total :</label>
                    <input type="text" name="stockTotal" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Stock Dispo :</label>
                    <input type="text" name="stockDispo" required class="form-input">
                </div>
                <div class="form-group">
                    <label>empruntable :</label>
                    oui 
                    <input type="radio" name="empruntable">
                    non 
                    <input type="radio" name="empruntable">
                </div>
                <button type="submit" name="submitModifier" class="submit-btn">Modifier le materiel</button>
            </div>
            </form>
        </div>
        <div id="del-tab" class="tab-content">
            <form action="?<?php echo "mdp=".$admin->hashMdp."&idA=".$admin->id ?>" method="POST">
            <div class="form-section">
                <div class="form-group">
                    <label>materiel :</label>
                    <select name="id" class="form-select">
                        <?php
                            foreach($materiels as $m) {
                                echo "<option value=\"".$m->id."\">".$m->nom." : ".$m->id."</option>";
                            }
                        ?>
                    </select>
                </div>
                <button type="submit" name="submitDelete" class="submit-btn">Supprimer le materiel</button>
            </div>
            </form>
        </div>
        <div id="add-tab" class="tab-content">
            <div class="form-section">
                <form action="?<?php echo "mdp=".$admin->hashMdp."&idA=".$admin->id ?>" method="POST">
                <div class="form-group">
                    <label>Nom :</label>
                    <input type="text" name="nom" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Description :</label>
                    <textarea name="description" required class="form-input" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Type :</label>
                    <input type="text" name="type" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Stock Total :</label>
                    <input type="text" name="stockTotal" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Stock Dispo :</label>
                    <input type="text" name="stockDispo" required class="form-input">
                </div>
                <div class="form-group">
                    <label>empruntable :</label>
                    oui 
                    <input type="radio" name="empruntable" value="empruntable">
                    non 
                    <input type="radio" name="empruntable">
                </div>
                <button type="submit" name="submitAjouter" class="submit-btn">Ajouter le materiel</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>