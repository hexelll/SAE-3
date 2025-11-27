<?php
    class Fabriquer {
        public $fabriqué;
        public $composant;
        public $quantité;
        public function __construct($fabriqué,$composant,$quantité) {
            $this->fabriqué = $fabriqué;
            $this->composant = $composant;
            $this->quantité = $quantité;
        }
        public static function fromTuple($tuple) {
            $materielDAO = new MaterielDAO();
            return new Fabriquer($materielDAO->findById($tuple["idFabriqué"]),$materielDAO->findById($tuple["idComposant"]),$tuple["quantité"]);
        }
        public static function fromTuples($tuples) {
            $fabriqués = array();
            foreach($tuples as $tuple) {
                $fabriqués[] = Fabriquer::fromTuple($tuple);
            }
            return $fabriqués;
        }
    }
    class FabriquerDAO {
        private $connexion = null;
        public function __construct() {
            $this->connexion = Connexion::getConnexion();
        }
        public function findByFabriquéId($id) {
            $prepared = $this->connexion->prepare("select * from Fabriquer where idFabriqué = :id");
            $prepared->bindValue(":id",$id);
            $prepared->execute();
            $tuples = $prepared->fetchAll(PDO::FETCH_ASSOC);
            return Fabriquer::fromTuples($tuples);
        }
        public function findByComposantId($id) {
            $prepared = $this->connexion->prepare("select * from Fabriquer where idComposant = :id");
            $prepared->bindValue(":id",$id);
            $prepared->execute();
            $tuples = $prepared->fetchAll(PDO::FETCH_ASSOC);
            return Fabriquer::fromTuples($tuples);
        }
    }
?>