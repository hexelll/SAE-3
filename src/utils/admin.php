<?php
    class Admin {
        public $id;
        public $email;
        public $hashMdp;
        public $demandesAValider = null;
        public function __construct($id,$email,$hashMdp) {
            $this->id = $id;
            $this->email = $email;
            $this->hashMdp = $hashMdp;
        }
        public static function fromTuple($tuple) {
            return new Admin($tuple["id_admin"],$tuple["email_admin"],$tuple["hash_admin"]);
        }
        public static function fromTuples($tuples) {
            $admins = array();
            foreach($tuples as $tuple) {
                $admins[] = Admin::fromTuple($tuple);
            }
            return $admins;
        }
    }
    class AdminDAO {
        private $connexion = null;
        public function __construct() {
            $this->connexion = Connexion::getConnexion();
        }
        public function findById($id) {
            $prepared = $this->connexion->prepare("select * from Admin where id_admin = :id");
            $prepared->bindValue(":id",$id,PDO::PARAM_INT);
            $prepared->execute();
            $tuple = $prepared->fetch(PDO::FETCH_ASSOC);
            return Admin::fromTuple($tuple);
        }
        public function findByEmail($email) {
            $prepared = $this->connexion->prepare("select * from Admin where email_admin = :email");
            $prepared->bindValue(":email",$email,PDO::PARAM_STR);
            $prepared->execute();
            $tuple = $prepared->fetch(PDO::FETCH_ASSOC);
            return Admin::fromTuple($tuple);
        }
        public function getAll() {
            $prepared = $this->connexion->prepare("select * from Admin");
            $tuples = $prepared->fetchAll();
            return Admin::fromTuples($tuples);
        }
    }
?>