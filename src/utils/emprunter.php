<?php
    class Emprunter {
        public $emprunt;
        public $materiel;
        public $quantité;
        public function __construct($emprunt,$materiel,$quantité) {
            $this->emprunt = $emprunt;
            $this->materiel = $materiel;
            $this->quantité = $quantité;
        }
        public static function fromTuple($tuple) {
            $empruntDAO = new EmpruntDAO();
            $materielDAO = new MaterielDAO();
            return new Emprunter(
                $empruntDAO->findById($tuple["idEmprunt"]),
                $materielDAO->findById($tuple["idMateriel"]),
                $tuple["quantité"]
            );
        }
        public static function fromTuples($tuples) {
            $emprunters = array();
            foreach($tuples as $tuple) {
                $emprunters[] = Emprunter::fromTuple($tuple);
            }
            return $emprunters;
        }
    }
    class EmprunterDAO {
        private $connexion = null;
        public function __construct() {
            $this->connexion = Connexion::getConnexion();
        }
        public function delete($emprunter) {
            $emprunter->materiel->quantité += $emprunter->quantité;
            $prepared = $this->connexion->prepare("delete from Emprunter where idMateriel=:idm and idEmprunt=:ide");
            $prepared->bindValue(":idm",$emprunter->materiel->id);
            $prepared->bindValue(":ide",$emprunter->emprunt->id);
            $prepared->execute();
        }
        public function deleteList($emprunters) {
            foreach ($emprunters as $emprunter) {
                (new EmpruntDAO())->delete($emprunter->materiel->id);
                $this->delete($emprunter);
            }
        }
        public function findByMaterielId($id) {
            $prepared = $this->connexion->prepare("select * from Emprunter where idMateriel = :id");
            $prepared->bindValue(":id",$id);
            $prepared->execute();
            $tuples = $prepared->fetchAll(PDO::FETCH_ASSOC);
            return Emprunter::fromTuples($tuples);
        }
        public function findByEmpruntId($id) {
            $prepared = $this->connexion->prepare("select * from Emprunter where idEmprunt = :id");
            $prepared->bindValue(":id",$id);
            $prepared->execute();
            $tuples = $prepared->fetchAll(PDO::FETCH_ASSOC);
            return Emprunter::fromTuples($tuples);
        }
        public function create(Emprunter $emprunter) {
            $prepared = $this->connexion->prepare("insert into Emprunter values(:idEmprunt,:idMateriel,:quantité)");
            $prepared->bindValue(":idEmprunt",$emprunter->emprunt->id);
            $prepared->bindValue(":idMateriel",$emprunter->materiel->id);
            $prepared->bindValue(":quantité",$emprunter->quantité);
            $prepared->execute();
        }
        public function update(Emprunter $emprunter) {
            $prepared = $this->connexion->prepare(
                "update Emprunter set ".
                "quantité=:quantité,".
                "where idEmprunt=:idEmprunt and idMateriel:idMateriel"
            );
            $prepared->bindValue(":idEmprunt",$emprunter->emprunt->id);
            $prepared->bindValue(":idMateriel",$emprunter->materiel->id);
            $prepared->bindValue(":quantité",$emprunter->quantité);
            $prepared->execute();
        }
    }
?>