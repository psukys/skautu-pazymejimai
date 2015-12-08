<?php

class PrasymasModel {

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

    /**
     * Gaunama prašymo informacija pagal nurodyta prašymo ID
     * $id - prašymo ID
     * return - masyvas su prašymo informacija, įskaitant ID.
     */
    public function _get_prasymas($id) {
        try {
            $query = "SELECT `prasymas`.`id`, `prasymas`.`vardas`,
                `prasymas`.`pavarde`, `prasymas`.`data`, `prasymas`.`nuotrauka`,
                `prasymas`.`lytis`, `prasymas`.`kaklaraistis`, `prasymas`.`pareigos`,
                `prasymas`.`krastas`, `prasymas`.`tuntas`, `prasymas`.`draugove`,
                `prasymas`.`adresas`, `prasymas`.`el_pastas`, `prasymas`.`telefono_numeris` 
                FROM `prasymas`
                WHERE `prasymas`.`id`=?";
            $prep = $this->_dbh->prepare($query);
            $prep->execute(array($id));
            return $prep->fetch();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function _get_prasymai($ids) {
        $prasymai = array();
        foreach ($ids as $id) {
            array_push($prasymai, $this->_get_prasymas($id));
        }
        return $prasymai;
    }

    public function _get_ids() {
        try {
            $query = "SELECT `prasymas`.`id` FROM `prasymas`";
            $prep = $this->_dbh->prepare($query);
            $prep->execute();
            $ids = array();
            $results = $prep->fetchAll();
            foreach ($results as $result)
                $ids[] = $result[0];
            return $ids;
        } catch(PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function _get_all_prasymai() {
        $ids = $this->_get_ids();
        return $this->_get_prasymai($ids);
    }

    /**
     * Sugeneruoja unikalų ID. Generavimo forma: pirma vardo raidė, pirma
     * pavardės raidė ir 8 simboliai datai. Jei toks ID jau egzistuoja, prie
     * datos yra pridedamas vienetas ir vėl ieškoma ar tai unikalu
     * $vardas - vardas
     * $pavarde - pavardė
     * $data - YYYY-mm-DD formato data
     */
    private function _generate_id($vardas, $pavarde, $data) {
        // tikrinama data
        $regex = "/[0-9]{4}-[0-9]{2}-[0-9]{2}/";
        if (preg_match($regex, $data)) {
            $data = (int)str_replace("-", "", $data);
        } else {
            $data = 0; // jei neatitinka formato, tiesiog nulis
        }
        // ieškojimas palengvinamas vos radus: http://stackoverflow.com/a/18114500/552214
        $query = "SELECT `prasymas`.`id` FROM `prasymas` WHERE `prasymas`.`id` = :id LIMIT 1";
        try {
            $prep = $this->_dbh->prepare($query);
            $result = array("1");//kvailys aš
            do {
                $id = substr($vardas, 0, 1) . substr($pavarde, 0, 1) . str_pad($data, 8, "0", STR_PAD_LEFT);
                $prep->execute(array(
                    ":id" => $id
                ));
                $result = $prep->fetchAll();
                $data += 1;
                //kažkada tai atsitiks ir teks kažkaip visa tai taisyti, tad dabar - YOLO
                if ($data == 100000000)
                    die("NEBĖRA SKAIČIŲ");
            } while (!empty($result));

            return $id;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
        
    }

    /**
 * This code is an improvement over Alex's code that can be found here -> http://stackoverflow.com/a/11376379
 * 
 * This funtion creates a thumbnail with size $thumbnail_width x $thumbnail_height.
 * It supports JPG, PNG and GIF formats. The final thumbnail tries to keep the image proportion.
 * 
 * Warnings and/or notices will also be thrown if anything fails.
 * 
 * Example of usage:
 * 
 * <code>
 * require_once 'create_thumbnail.php';
 * 
 * $success = createThumbnail(__DIR__.DIRECTORY_SEPARATOR.'image.jpg', __DIR__.DIRECTORY_SEPARATOR.'image_thumb.jpg', 60, 60);
 * 
 * echo $success ? 'thumbnail was created' : 'something went wrong';
 * </code>
 * 
 * @author Pedro Pinheiro (https://github.com/PedroVPP).
 * @param string $filepath The image complete path with name. Example: C:\xampp\htdocs\project\image.jpg
 * @param string $thumbpath The path with name of the final thumbnail. Example: C:\xampp\htdocs\project\image_thumbnail.jpg
 * @param int $thumbnail_width Width of the thumbnail. Only integers allowed.
 * @param int $thumbnail_height Height of the thumbnail. Only integers allowed.
 * @return boolean Returns true if the thumbnail was created successfully, false otherwise.
 */
private function _create_thumbnail($filepath, $thumbpath, $thumbnail_width, $thumbnail_height) {
    list($original_width, $original_height, $original_type) = getimagesize($filepath);
    if ($original_width > $original_height) {
        $new_width = $thumbnail_width;
        $new_height = intval($original_height * $new_width / $original_width);
    } else {
        $new_height = $thumbnail_height;
        $new_width = intval($original_width * $new_height / $original_height);
    }
    $dest_x = intval(($thumbnail_width - $new_width) / 2);
    $dest_y = intval(($thumbnail_height - $new_height) / 2);
    
    if ($original_type === 1) {
        $imgt = "ImageGIF";
        $imgcreatefrom = "ImageCreateFromGIF";
    } else if ($original_type === 2) {
        $imgt = "ImageJPEG";
        $imgcreatefrom = "ImageCreateFromJPEG";
    } else if ($original_type === 3) {
        $imgt = "ImagePNG";
        $imgcreatefrom = "ImageCreateFromPNG";
    } else {
        return false;
    }
    
    $old_image = $imgcreatefrom($filepath);
    $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
    imagecopyresampled($new_image, $old_image, $dest_x, $dest_y, 150, 150, $new_width, $new_height, $original_width, $original_height);
    $imgt($new_image, $thumbpath);
    
    return file_exists($thumbpath);
}

    /** Nuotraukos apdorojimas:
     * 1. Išsaugojama lokaliam folderyje (gaunamas kelas iki jos)
     * 2. Sukuriamas thumbnailas tokiu pat vardu tik thumbs folderyje
     * $nuotrauka - serverio įkelto failo alternatyva (kaip $_FILES["nuotrauka"])
     * return - true, jei sėkmingai išsaugota, false- jei ne
     */
    private function _save_nuotrauka($nuotrauka, $nuotrauku_kelias, $thumbs_kelias, $id) {
        if (!is_dir($nuotrauku_kelias)) {
            mkdir($nuotrauku_kelias, 0777, true); 
        }

        if (!is_dir($thumbs_kelias)) {
            mkdir($thumbs_kelias, 0777, true);
        }
        $ext = '.' . pathinfo($nuotrauka["name"], PATHINFO_EXTENSION);//gaunamas extensionas
        if (!copy($nuotrauka['tmp_name'], $nuotrauku_kelias . $id . $ext)) {
            return false;
        }
        // Kuriamas thumbnail
        if (!$this->_create_thumbnail($nuotrauku_kelias . $id . $ext, $thumbs_kelias . $id . $ext, 100, 100)) {
            return false;
        }
        return true;
    }

    /**
     * Išsaugomas prašymas, išsaugant turi būti visi duomenys (žiūrėti $laukai)
     * $data - prašymo duomenys
     * return - prašymo ID
     */
    public function _add_prasymas($data) {
        // Patikrinama ar visi reikalingi duomenys egzistuoja
        $laukai = ["vardas", "pavarde", "data", "nuotrauka", "lytis", "kaklaraistis",
            "pareigos", "krastas", "tuntas", "draugove", "adresas", "el_pastas", "telefono_numeris"];
        $visi_laukai = true;
        foreach ($laukai as $laukas) {
            if (!isset($data[$laukas])) {
                $visi_laukai = false;
                echo $laukas .  "<br />";
            }
        }
        if (!$visi_laukai) {
            die("Ne visi reikalingi laukai užpildyti");
        }

        // Sugeneruojame id
        $id = $this->_generate_id($data["vardas"], $data["pavarde"], $data["data"]);
        $nuotraukos_kelias = "nuotraukos/";
        $thumbs_kelias = $nuotraukos_kelias . "thumbs/";
        if (!$this->_save_nuotrauka($data["nuotrauka"], $nuotraukos_kelias, $thumbs_kelias, $id)) {
            echo "Nepavyko išsaugoti nuotraukos";
            return null;
        }

        $query = "INSERT INTO `prasymas` (`id`, `vardas`, `pavarde`, `data`, `nuotrauka`, `lytis`, `kaklaraistis`,
            `pareigos`, `krastas`, `tuntas`, `draugove`, `adresas`, `el_pastas`, `telefono_numeris`) VALUES (:id,
            :vardas, :pavarde, :data, :nuotrauka, :lytis, :kaklaraistis, :pareigos, :krastas, :tuntas, :draugove, :adresas,
            :el_pastas, :telefono_numeris);";

        try {
            $prep = $this->_dbh->prepare($query);
            $ext = '.' . pathinfo($data["nuotrauka"]["name"], PATHINFO_EXTENSION);
            $err = $prep->execute(array(
                ":id" => $id,
                ":vardas" => $data["vardas"],
                ":pavarde" => $data["pavarde"],
                ":data" => $data["data"],
                ":nuotrauka" => $nuotraukos_kelias . $id . $ext,
                ":lytis" => $data["lytis"],
                ":kaklaraistis" => $data["kaklaraistis"],
                ":pareigos" => implode(", ", $data["pareigos"]),
                ":krastas" => $data["krastas"],
                ":tuntas" => $data["tuntas"],
                ":draugove" => $data["draugove"],
                ":adresas" => $data["adresas"],
                ":el_pastas" => $data["el_pastas"],
                ":telefono_numeris" => $data["telefono_numeris"]
            ));
            return $id;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Redaguojami prašymo duomenys
     * $id - prašymo ID
     * $data - duomenys, kurie turi būti pakeičiami
     * return - ar sėkmingai paKeisti duomenys
     */
    public function _set_prasymas($id, $data) {

    }

    /**
     * Pašalinamas prašymas
     * $id - prašymo ID
     * return - ar sėkmingai pašalintas ID
     */
    public function _del_prasymas($id) {
        //pasalinamas tik prasymas
        try {
            $query = "DELETE FROM `prasymai` WHERE `prasymai`.`id` = ?";
            $prep = $this->_dbh->prepare($query);
            $prep->execute($id);    
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        
    }
}

?>