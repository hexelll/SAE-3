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
    if (isset($_REQUEST["mdp"]) && $_REQUEST["mdp"] != "" && isset($_REQUEST["idA"]) && $_REQUEST["idA"] != "") {
        $hashmdp = $_REQUEST["mdp"];
        $admin = $admDAO->findById($_REQUEST["idA"]);
        if (gettype($admin)=="object" && $admin->hashMdp != $hashmdp)
            quit();
    }else 
        quit();
}
catch(e) {quit();}
$emprunts = $empDAO->getAllEmprunts();
$materiels = $matDAO->getAll();

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
        window.document.onload = function(e){ 
        const materiels = {
            <?php
                foreach($materiels as $k=>$m) {
                    echo "\"".$m->id."\":{".
                    "\"nom\":\"".$m->nom."\",".
                    "\"desc\":\"".$m->description."\",".
                    "\"type\":\"".$m->type."\",".
                    "\"total\":".$m->stock_total.",".
                    "\"dispo\":".$m->stock_disponible.",".
                    "\"empruntable\":\"".$m->empruntable."\"}";
                    if ($k!=count($materiels)-1)
                        echo ",";
                }
            ?>
        }
        const idselect = document.getElementById("mod_id")
        const nomtf = document.getElementById("mod_nom")
        const descta =document.getElementById("mod_desc")
        const typetf = document.getElementById("mod_type")
        const totalnf = document.getElementById("mod_total")
        const disponf = document.getElementById("dispo")
        const ouicb = document.getElementById("mod_empruntable")
        const noncb = document.getElementById("mod_nonempruntable")
        idselect.addEventListener("onchange",()=>{
            const mat = materiels[idselect.getAttribute("value")]
            nomtf.setAttribute("value",mat["nom"])
            descta.setAttribute("value",mat["desc"])
            typetf.setAttribute("value",mat["type"])
            totalnf.setAttribute("value",mat["total"])
            disponf.setAttribute("value",mat["dispo"])
            ouicb.setAttribute("value",mat["empruntable"])
            noncb.setAttribute("value",!mat["empruntable"])
        })
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
                            <th>Motif</th>
                            <th>Dates</th>
                            <th>Statut</th>
                            <th>Actions</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($emprunts as $r): ?>
                        <tr>
                            <td>#<?php echo $r->id; ?></td>
                            <td><?php echo htmlspecialchars($r->email); ?></td>
                            <td></td>
                            <td><?php echo htmlspecialchars($r->motif); ?></td>
                            <td><?php echo $r->date_emprunt; ?> au <?php echo $r->date_retour_prevue; ?></td>
                            <td>
                                <span class="badge <?php echo ($r->statut_emprunt=='validé'?'badge-success':($r->statut_emprunt=='refusé'?'badge-danger':'badge-warning')); ?>">
                                    <?php echo htmlspecialchars($r->statut_emprunt); ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <?php if($r->statut_emprunt == 'en cours'): ?>
                                <form action="traitement_admin.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="mdp" value="<?php echo $_REQUEST["mdp"] ?>">
                                    <input type="hidden" name="idA" value="<?php echo $_REQUEST["idA"] ?>">
                                    <input type="hidden" name="idEmprunt" value="<?php echo $r->id; ?>">
                                    <input type="hidden" name="action" value="valider">
                                    <button type="submit" class="btn btn-success">✓</button>
                                </form>
                                <form action="traitement_admin.php?<?php echo "mdp=".$admin->hashMdp."&idA=".$admin->id ?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="idEmprunt" value="<?php echo $r->id; ?>">
                                    <input type="hidden" name="action" value="refuser">
                                    <input type="hidden" name="mdp" value="<?php echo $_REQUEST["mdp"] ?>">
                                    <input type="hidden" name="idA" value="<?php echo $_REQUEST["idA"] ?>">
                                    <button type="submit" class="btn btn-danger">✗</button>
                                </form>
                                <?php else: ?>
                                    <small>Traité</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="traitement_admin.php?<?php echo "mdp=".$admin->hashMdp."&idA=".$admin->id ?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="idEmprunt" value="<?php echo $r->id; ?>">
                                    <input type="hidden" name="action" value="supprimer">
                                    <input type="hidden" name="mdp" value="<?php echo $_REQUEST["mdp"] ?>">
                                    <input type="hidden" name="idA" value="<?php echo $_REQUEST["idA"] ?>">
                                    <button type="submit" class="btn btn-danger">🗑</button>
                                </form>
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
            <h2>Modifier du materiel</h2>
            <form action="traitement_admin.php" method="POST">
                <input type="hidden" name="action" value="submitModifier">
                <input type="hidden" name="mdp" value="<?php echo $_REQUEST["mdp"] ?>">
                <input type="hidden" name="idA" value="<?php echo $_REQUEST["idA"] ?>">
            <div class="form-section">
                <div class="form-group">
                    <label>materiel :</label>
                    <select id="mod_id" name="id" class="form-select">
                        <?php
                            foreach($materiels as $m) {
                                echo "<option value=\"".$m->id."\">".$m->nom." : ".$m->id."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nom :</label>
                    <input id="mod_nom" type="text" name="nom" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Description :</label>
                    <textarea id="mod_desc" name="description" required class="form-input" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Type :</label>
                    <input id="mod_type" type="text" name="type" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Stock Total :</label>
                    <input id="mod_total" type="text" name="stockTotal" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Stock Dispo :</label>
                    <input id="mod_dispo" type="text" name="stockDispo" required class="form-input">
                </div>
                <div class="form-group">
                    <label>empruntable :</label>
                    oui 
                    <input id="mod_empruntable" type="radio" checked name="empruntable">
                    non 
                    <input id="mod_nonempruntable" type="radio" name="empruntable">
                </div>
                <button type="submit" name="submitModifier" class="submit-btn">Modifier le materiel</button>
            </div>
            </form>
        </div>
        <div id="del-tab" class="tab-content">
            <h2>Supprimer du materiel</h2>
            <form action="traitement_admin.php" method="POST">
                <input type="hidden" name="action" value="submitDelete">
                <input type="hidden" name="mdp" value="<?php echo $_REQUEST["mdp"] ?>">
                <input type="hidden" name="idA" value="<?php echo $_REQUEST["idA"] ?>">
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
            <h2>Ajouter du materiel</h2>
            <div class="form-section">
                <form action="traitement_admin.php" method="POST">
                    <input type="hidden" name="action" value="submitAjouter">
                    <input type="hidden" name="mdp" value="<?php echo $_REQUEST["mdp"] ?>">
                    <input type="hidden" name="idA" value="<?php echo $_REQUEST["idA"] ?>">
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
                    <input type="number" name="stockTotal" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Stock Dispo :</label>
                    <input type="number" name="stockDispo" required class="form-input">
                </div>
                <div class="form-group">
                    <label>empruntable :</label>
                    oui 
                    <input type="radio" checked name="empruntable" value="empruntable">
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