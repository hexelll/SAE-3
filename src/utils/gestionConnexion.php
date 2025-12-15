<?php
    class Connexion {
        private static  $_instance = null;
        private static $laConnexion;
        private  function __construct() {
            try {
                self::$laConnexion= new PDO('mysql:host=10.3.17.219;port=9100;dbname=bdfablab','adminer','Isanum64!');
            }catch(PDOException $erreur){
                echo "erreur de connexion : ".$erreur->getMessage();
                die();
            }
        }
        public static function getConnexion()
        {    
            if (is_null(self::$_instance))
                self::$_instance=new Connexion();           
            return (self::$laConnexion);
        }
        public static function liberer() {
            self::$_instance= null;
            self::$laConnexion= null;
        }
    }
?>