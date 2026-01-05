<?php
require '../utils/gestionConnexion.php';
require '../utils/admin.php';
$pdo = Connexion::getConnexion();

$admDAO = new AdminDAO();
function quit() {
    header("Location: ConnexionAdmin.php");
    exit();
}
try {
    if (isset($_REQUEST["mdp"]) && isset($_REQUEST["id"])) {
        $hashmdp = $_REQUEST["mdp"];
        $admin = $admDAO->findById($_REQUEST["id"]);
        if ($admin->hashMdp != $hashmdp)
            quit();
    }else 
        quit();
}
catch(e) {quit();}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idEmprunt = $_POST['idEmprunt'];
    $action = $_POST['action'];

    if ($action == 'valider') {
        $stmt = $pdo->prepare("UPDATE Emprunt SET statutEmprunt = 'validé' WHERE idEmprunt = ?");
        $stmt->execute([$idEmprunt]);
        $stmtGet = $pdo->prepare("SELECT idMateriel, quantité FROM Emprunter WHERE idEmprunt = ?");
        $stmtGet->execute([$idEmprunt]);
        $items = $stmtGet->fetchAll();

        foreach ($items as $item) {
            $updateStock = $pdo->prepare("UPDATE Materiel SET stockDisponible = stockDisponible - ? WHERE idMateriel = ?");
            $updateStock->execute([$item['quantité'], $item['idMateriel']]);
        }

    } elseif ($action == 'refuser') {
        $stmt = $pdo->prepare("UPDATE Emprunt SET statutEmprunt = 'refusé' WHERE idEmprunt = ?");
        $stmt->execute([$idEmprunt]);
    }

    header("Location: admin.php?".$admin->hashMdp."&id=".$admin->id);
    exit;
}
?>