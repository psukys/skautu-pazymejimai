<?php

class BukleModel {
    public function __construct() {
        // Prisijungiama prie db
        require_once 'database.php';
        $db = Database::getInstance();
        $this->_dbh = $db->getConnection();
    }

    public function __destruct() {
        // Sunaikinama jungtis su db
        $this->_dbh = null;
    }

    public function get_bukle($id) {
        try {
            $query = "SELECT `bukle`.`id`, `bukle`.`pagamintas`,
                `bukle`.`nuotrauka`, `bukle`.`mokestis`, `bukle`.`data`
                FROM `bukle`
                WHERE `bukle`.`id`=?";
            $prep = $this->_dbh->prepare($query);
            $prep->execute(array($id));
            return $prep->fetch();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function add_bukle($data) {
        $laukai = array("id");
        $visi_laukai = true;
        foreach ($laukai as $laukas) {
            if (!isset($data[$laukas])) {
                $visi_laukai = false;
                echo $laukas . "<br />";
            }
        }
        if (!$visi_laukai) {
            die("Ne visi reikalingi laukai užpildyti");
        }
        $query = "INSERT INTO `bukle` (`id`, `pagamintas`, `nuotrauka`, `mokestis`, `data`)
            VALUES (:id, :pagamintas, :nuotrauka, :mokestis, :data)";
        try {
            $prep = $this->_dbh->prepare($query);
            $prep->execute(array(
                ":id" => $data["id"],
                ":pagamintas" => 0,
                ":nuotrauka" => "Netikrinta",
                ":mokestis" => "Netikrinta",
                ":data" => "-"
                ));
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function get_ids() {
        try {
            $query = "SELECT `bukle`.`id` FROM `bukle`";
            $prep = $this->_dbh->prepare($query);
            $prep->execute();
            $results = $prep->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
            return array_map("reset", $results);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }
}

?>