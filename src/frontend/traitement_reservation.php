<?php
require '../utils/gestionConnexion.php';
require '../utils/emprunt.php';
require '../utils/materiel.php';
require '../utils/emprunter.php';
$pdo = Connexion::getConnexion();
$empDAO = new EmpruntDAO();
$matDAO = new MaterielDAO();
$emprDAO = new EmprunterDAO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_REQUEST['email'];
    $motif = $_REQUEST['motif'];
    $date_debut = $_REQUEST['date_debut'];
    $date_fin = $_REQUEST['date_fin'];
    
    
    $materiels_choisis = isset($_REQUEST['materiel']) ? $_REQUEST['materiel'] : [];
    $quantites = $_REQUEST['quantite'];

    if (count($materiels_choisis) > 0) {
        $nvemprunt = new Emprunt(null,$email, $motif, null,$date_debut, $date_fin,null,"en cours");
        $empDAO->create($nvemprunt);
        foreach ($materiels_choisis as $idMateriel => $on) {
            $qte = $quantites[$idMateriel];
            $mat = $matDAO->findById($idMateriel);
            $mat->stock_disponible -= $qte;
            $matDAO->update($mat);
            if ($qte > 0) {
                $emprunter = new Emprunter($empDAO->findById($idEmprunt),$mat,$qte);
                $emprDAO->create($emprunter);
            }
        }
        echo "<script>alert('Réservation enregistrée avec succès !'); window.location.href='index.php';</script>";
    } else {
        echo "Aucun matériel sélectionné.";
    }
}
?>