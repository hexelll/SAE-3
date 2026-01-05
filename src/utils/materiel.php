<?php
    class Materiel {
        public $id;
        public $nom;
        public $description;
        public $type;
        public $stock_total;
        public $stock_disponible;
        public $empruntable;
        public function __construct($id,$nom,$description,$type,$stock_total,$stock_disponible,$empruntable) {
            $this->id = $id;
            $this->nom = $nom;
            $this->description = $description;
            $this->type = $type;
            $this->stock_total = $stock_total;
            $this->stock_disponible = $stock_disponible;
            $this->empruntable = $empruntable;
        }
        public static function fromTuple($tuple) {
            return new Materiel($tuple["idMateriel"],$tuple["nomMateriel"],$tuple["descriptionMateriel"],$tuple["typeMateriel"],$tuple["stockTotal"],$tuple["stockDisponible"],$tuple["empruntable"]);
        }
        public static function fromTuples($tuples) {
            $materiaux = array();
            foreach($tuples as $tuple) {
                $materiaux[] = Materiel::fromTuple($tuple);
            }
            return $materiaux;
        }
    }
    class MaterielDAO {
        private $connexion = null;
        public function __construct() {
            $this->connexion = Connexion::getConnexion();
        }
        public function findById($id) {
            $prepared = $this->connexion->prepare("select * from Materiel where idMateriel = :id");
            $prepared->bindParam(":id",$id);
            $prepared->execute();
            return Materiel::fromTuple($prepared->fetch(PDO::FETCH_ASSOC));
        }
        public function getAll() {
            $prepared = $this->connexion->prepare("select * from Materiel");
            $prepared->execute();
            return Materiel::fromTuples($prepared->fetchAll(PDO::FETCH_ASSOC));
        }
        public function delete($id) {
            $prepared = $this->connexion->prepare("delete from Materiel where idMateriel = :id");
            $prepared->bindValue(":id",$id);
            $prepared->execute();
        }
        public function create($materiel) {
            $prepared = $this->connexion->prepare("insert into Materiel(nomMateriel,descriptionMateriel,typeMateriel,stockTotal,stockDisponible,empruntable) values(:nom,:desc,:type,:stockTot,:stockDisp,:empruntable)");
            $prepared->bindValue(":nom",$materiel->nom);
            $prepared->bindValue(":desc",$materiel->description);
            $prepared->bindValue(":type",$materiel->type);
            $prepared->bindValue(":stockTot",$materiel->stock_total);
            $prepared->bindValue(":stockDisp",$materiel->stock_disponible);
            $prepared->bindValue(":empruntable",$materiel->empruntable);
            $prepared->execute();
        }
        public function update($materiel) {
            $prepared = $this->connexion->prepare(
                "update Materiel set ".
                "nomMateriel=:nom,".
                "descriptionMateriel=:desc,".
                "typeMateriel=:type,".
                "stockTotal=:stockTot,".
                "stockDisponible=:stockDisp,".
                "empruntable=:empruntable ".
                "where idMateriel = :id"
            );
            $prepared->bindValue(":id",$materiel->id);
            $prepared->bindValue(":nom",$materiel->nom);
            $prepared->bindValue(":desc",$materiel->description);
            $prepared->bindValue(":type",$materiel->type);
            $prepared->bindValue(":stockTot",$materiel->stock_total);
            $prepared->bindValue(":stockDisp",$materiel->stock_disponible);
            $prepared->bindValue(":empruntable",$materiel->empruntable);
            $prepared->execute();
        }
    }
?>