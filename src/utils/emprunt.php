<?php
    class Emprunt {
        public $id;
        public $email;
        public $motif;
        public $admin;
        public $date_emprunt;
        public $date_retour_prevue;
        public $date_retour_reelle;
        public $statut_emprunt;
        public $composants = null;
        public function __construct($id,$email,$motif,$admin,$date_emprunt,$date_retour_prevue,$date_retour_reelle,$statut_emprunt) {
            $this->id = $id;
            $this->email = $email;
            $this->motif = $motif;
            $this->admin = $admin;
            $this->date_emprunt = $date_emprunt;
            $this->date_retour_prevue = $date_retour_prevue;
            $this->date_retour_reelle = $date_retour_reelle;
            $this->statut_emprunt = $statut_emprunt;
        }
        public static function fromTuple($tuple) {
            $adminDAO = new AdminDAO();
            return new Emprunt(
                $tuple["idEmprunt"],
                $tuple["emailEmprunt"],
                $tuple["motifEmprunt"],
                $tuple["idAdmin"]?$adminDAO->findById($tuple["idAdmin"]):null,
                $tuple["dateEmprunt"],
                $tuple["dateRetourPrevue"],
                $tuple["dateRetourReelle"],
                $tuple["statutEmprunt"]
            );
        }
        public static function fromTuples($tuples) {
            $empruntsValidés = array();
            foreach($tuples as $tuple) {
                $empruntsValidés[] = Emprunt::fromTuple($tuple);
            }
            return $empruntsValidés;
        }
    }
    class EmpruntDAO {
        private $connexion = null;
        public function __construct() {
            $this->connexion = Connexion::getConnexion();
        }
        public function delete($id) {
            $prepared = $this->connexion->prepare("delete from Emprunt where idEmprunt=:id");
            $prepared->bindValue(":id",$id);
            $prepared->execute();
        }
        public function findById($id) {
            $prepared = $this->connexion->prepare("select * from Emprunt where idEmprunt = :id");
            $prepared->bindValue(":id",$id,PDO::PARAM_INT);
            $prepared->execute();
            $tuple = $prepared->fetch(PDO::FETCH_ASSOC);
            return Emprunt::fromTuple($tuple);
        }
        public function getAllEmpruntsAValidés($idAdmin) {
            $prepared = $this->connexion->prepare("select * from Emprunt,Emprunter where statutEmprunt = \"en cours\" and idAdmin=:id and Emprunt.idEmprunt = Emprunter.idEmprunt");
            $prepared->bindValue(":id",$idAdmin);
            $prepared->execute();
            $tuples = $prepared->fetchAll();
            return Emprunt::fromTuples($tuples);
        }
        public function getAllEmprunts() {
            $prepared = $this->connexion->prepare("select * from Emprunt");
            $prepared->execute();
            $tuples = $prepared->fetchAll();
            return Emprunt::fromTuples($tuples);
        }
        public function create(Emprunt $emprunt) {
            $prepared = $this->connexion->prepare("insert into Emprunt values(:motif,:email,:dateEmprunt,:datePrevue,:dateReelle,:statut,:idAdmin)");
            $prepared->bindValue(":email",$emprunt->email);
            $prepared->bindValue(":motif",$emprunt->motif);
            $prepared->bindValue(":idAdmin",$emprunt->admin->id);
            $prepared->bindValue(":dateEmprunt",$emprunt->date_emprunt);
            $prepared->bindValue(":datePrevue",$emprunt->date_retour_prevue);
            $prepared->bindValue(":dateReelle",$emprunt->date_retour_reelle);
            $prepared->bindValue(":statut",$emprunt->statut_emprunt);
            $prepared->execute();
        }
        public function update($emprunt) {
            $prepared = $this->connexion->prepare(
                "update Emprunt set ".
                "emailEmprunt=:email,".
                "motifEmprunt=:motif,".
                "dateEmprunt=:dateEmprunt,".
                "dateRetourPrevue=:datePrevue,".
                "dateRetourReelle=:dateReelle,".
                "statutEmprunt=:statut ".
                "where idEmprunt = :id"
            );
            $prepared->bindValue(":id",$emprunt->id);
            $prepared->bindValue(":email",$emprunt->email);
            $prepared->bindValue(":motif",$emprunt->motif);
            $prepared->bindValue(":idAdmin",$emprunt->admin->id);
            $prepared->bindValue(":dateEmprunt",$emprunt->date_emprunt);
            $prepared->bindValue(":datePrevue",$emprunt->date_retour_prevue);
            $prepared->bindValue(":dateReelle",$emprunt->date_retour_reelle);
            $prepared->bindValue(":statut",$emprunt->statut_emprunt);
            $prepared->execute();
        }
    }
?>