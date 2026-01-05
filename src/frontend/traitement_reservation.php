<?php
require '../utils/gestionConnexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $motif = $_POST['motif'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    
    
    $materiels_choisis = isset($_POST['materiel']) ? $_POST['materiel'] : [];
    $quantites = $_POST['quantite'];

    if (count($materiels_choisis) > 0) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO Emprunt (emailEmprunt, motifEmprunt, dateEmprunt, dateRetourPrevue, statutEmprunt) VALUES (?, ?, ?, ?, 'en cours')");
            $stmt->execute([$email, $motif, $date_debut, $date_fin]);
            
            $idEmprunt = $pdo->lastInsertId();

            
            $stmtInsert = $pdo->prepare("INSERT INTO Emprunter (idEmprunt, idMateriel, quantité) VALUES (?, ?, ?)");
            
            foreach ($materiels_choisis as $idMateriel => $on) {
                $qte = $quantites[$idMateriel];
                if ($qte > 0) {
                    $stmtInsert->execute([$idEmprunt, $idMateriel, $qte]);
                }
            }

            $pdo->commit();

            echo "<script>alert('Réservation enregistrée avec succès !'); window.location.href='index.php';</script>";

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erreur lors de la réservation : " . $e->getMessage());
        }
    } else {
        echo "Aucun matériel sélectionné.";
    }
}
?>