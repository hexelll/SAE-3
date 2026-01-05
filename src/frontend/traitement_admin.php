<?php
require '../utils/gestionConnexion.php';
require '../utils/admin.php';
require '../utils/emprunt.php';
require '../utils/materiel.php';
require '../utils/emprunter.php';
$pdo = Connexion::getConnexion();

$admDAO = new AdminDAO();
$empDAO = new EmpruntDAO();
$emprDAO = new EmprunterDAO();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idEmprunt = $_POST['idEmprunt'];
    $action = $_POST['action'];
    $emprunt = $empDAO->findById($idEmprunt);
    switch ($action) {
        case "valider" : {
            $emprunt->statut_emprunt = "validé";
            $empDAO->update($emprunt);
            $items = $emprDAO->findByEmpruntId($idEmprunt);
            foreach ($items as $item)
                $item->materiel->stock_disponible -= $item->quantité;
            break;
        } case 'refuser': {
            $emprunt->statut_emprunt = "refusé";
            break;
        } case 'supprimer': {
            $items = $emprDAO->findByEmpruntId($idEmprunt);
            $emprDAO->deleteList($items);
            break;
        }
    }

    //header("Location: admin.php?mdp=".$admin->hashMdp."&idA=".$admin->id);
    //exit();
}
?>