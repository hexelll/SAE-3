<?php
require '../utils/gestionConnexion.php';
require '../utils/admin.php';
require '../utils/emprunt.php';
require '../utils/materiel.php';
require '../utils/emprunter.php';
$pdo = Connexion::getConnexion();

$matDAO = new MaterielDAO();
$admDAO = new AdminDAO();
$empDAO = new EmpruntDAO();
$emprDAO = new EmprunterDAO();
function quit()
{
    header("Location: ConnexionAdmin.php");
    exit();
}
try {
    if (isset($_REQUEST["mdp"]) && isset($_REQUEST["idA"])) {
        $hashmdp = $_REQUEST["mdp"];
        $admin = $admDAO->findById($_REQUEST["idA"]);
        if ($admin->hashMdp != $hashmdp)
            quit();
    } else
        quit();
} catch (e) {
    quit();
}

if (isset($_REQUEST['idEmprunt']))
    $idEmprunt = $_REQUEST['idEmprunt'];
$action = $_REQUEST['action'];
$emprunt = $empDAO->findById($idEmprunt);
switch ($action) {
    case "valider": {
        $emprunt->statut_emprunt = "validé";
        $empDAO->update($emprunt);
        $items = $emprDAO->findByEmpruntId($idEmprunt);
        foreach ($items as $item)
            $item->materiel->stock_disponible -= $item->quantité;
        break;
    }
    case 'refuser': {
        $emprunt->statut_emprunt = "refusé";
        break;
    }
    case 'supprimer': {
        $items = $emprDAO->findByEmpruntId($idEmprunt);
        $emprDAO->deleteList($items);
        $empDAO->delete($idEmprunt);
        break;
    }
    case 'submitDelete': {
        $id = $_REQUEST["id"];
        $matDAO->delete($id);
        break;
    }
    case 'submitAjouter': {
        $nom = $_REQUEST["nom"];
        $desc = $_REQUEST["description"];
        $type = $_REQUEST["type"];
        $stockTotal = $_REQUEST["stockTotal"];
        $stockDispo = $_REQUEST["stockDispo"];
        $empruntable = $_REQUEST["empruntable"] == "empruntable" ? true : false;
        $nvemprunt = new Materiel(null, $nom, $desc, $type, $stockTotal, $stockDispo, $empruntable);
        $matDAO->create($nvemprunt);
        break;
    }
    case 'submitModifier': {
        $id = $_REQUEST["id"];
        $nom = $_REQUEST["nom"];
        $desc = $_REQUEST["description"];
        $type = $_REQUEST["type"];
        $stockTotal = $_REQUEST["stockTotal"];
        $stockDispo = $_REQUEST["stockDispo"];
        $empruntable = $_REQUEST["empruntable"] == "empruntable" ? true : false;
        $emprunt = new Materiel($id, $nom, $desc, $type, $stockTotal, $stockDispo, $empruntable);
        $matDAO->update($emprunt);
    }
}
echo "<script>alert('action effectuée avec succès !'); window.location.href='admin.php?mdp=" . $_REQUEST["mdp"] . "&idA=" . $_REQUEST["idA"] . "';</script>";
echo 'admin.php?mdp=" . $_REQUEST["mdp"] . "&idA=" . $_REQUEST["idA"] . "';
?>