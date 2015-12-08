<?php
class PaskyraModel {
    public function __construct() {
        // Prisijungiama prie db
        include 'database.php';
        $db = Database::getInstance();
        $this->_dbh = $db->getConnection();
    }

    public function __destruct() {
        // Sunaikinama jungtis su db
        $this->_dbh = null;
    }

    public function check_login($prisijungimas, $slaptazodis) {
        $prisijungimai = $this->get_prisijungimai()[0];
        //vengiame injekcijų, tiesiog tikriname kas yra
        if (in_array($prisijungimas, $prisijungimai)) {
            return $this->patikrinti_slaptazodi($prisijungimas, $slaptazodis);
        } else {
            return false;
        }
    }

    private function get_prisijungimai() {
        try {
            $query = "SELECT `paskyra`.`prisijungimas` FROM `paskyra`";
            $prep = $this->_dbh->prepare($query); 
            $prep->execute();
            return $prep->fetchAll();  
        } catch (PDOException $e) {
            echo $e->getMessage();
            return array();//tiesiog tuščias masyvas
        }
    }

    private function patikrinti_slaptazodi($prisijungimas, $slaptazodis) {
        try {
            $query = "SELECT `paskyra`.`slaptazodis` FROM `paskyra` WHERE `paskyra`.`prisijungimas` = ?";
            $prep = $this->_dbh->prepare($query);
            $prep->execute(array($prisijungimas));
            $tikras_hashas = $prep->fetch();
            // Gaunama slaptažodžio reprezentacija
            $druska = "pazymejimai";
            $hashas = hash("sha256", $druska . $slaptazodis . $druska);
            return $hashas == $tikras_hashas["slaptazodis"];
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;//tiesiog tuščias masyvas
        }
    }
}
?>