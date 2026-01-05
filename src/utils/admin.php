<?php
    class Admin {
        public $id;
        public $email;
        public $hashMdp;
        public function __construct($id,$email,$hashMdp) {
            $this->id = $id;
            $this->email = $email;
            $this->hashMdp = $hashMdp;
        }
        public static function fromTuple($tuple) {
            return new Admin($tuple["idAdmin"],$tuple["emailAdmin"],$tuple["hashAdmin"]);
        }
        public static function fromTuples($tuples) {
            $admins = array();
            foreach($tuples as $tuple) {
                $admins[] = Admin::fromTuple($tuple);
            }
            return $admins;
        }
        public static function hashMdp($mdp) {
            return hash('sha256', $mdp);
        }
    }
    class AdminDAO {
        private $connexion = null;
        public function __construct() {
            $this->connexion = Connexion::getConnexion();
        }
        public function findById($id) {
            $prepared = $this->connexion->prepare("select * from Admin where idAdmin = :id");
            $prepared->bindValue(":id",$id,PDO::PARAM_INT);
            $prepared->execute();
            $tuple = $prepared->fetch(PDO::FETCH_ASSOC);
            return Admin::fromTuple($tuple);
        }
        public function findByEmail($email) {
            $prepared = $this->connexion->prepare("select * from Admin where emailAdmin = :email");
            $prepared->bindValue(":email",$email,PDO::PARAM_STR);
            $prepared->execute();
            $tuple = $prepared->fetch(PDO::FETCH_ASSOC);
            return Admin::fromTuple($tuple);
        }
        public function getAll() {
            $prepared = $this->connexion->prepare("select * from Admin");
            $prepared->execute();
            $tuples = $prepared->fetchAll();
            return Admin::fromTuples($tuples);
        }
        public function update($admin) {
            $prepared = $this->connexion->prepare(
                "update Admin set ".
                "emailAdmin=:emailAdmin,".
                "hashAdmin=:hashAdmin".
                "where idAdmin=:idAdmin"
            );
            $prepared->bindValue(":idAdmin",$admin->id);
            $prepared->bindValue(":emailAdmin",$admin->email);
            $prepared->bindValue(":hashAdmin",$admin->hashMdp);
            $prepared->execute();
        }
    }
?>