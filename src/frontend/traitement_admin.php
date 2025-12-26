<?php
require 'config.php';

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

    header('Location: admin.php');
    exit;
}
?>