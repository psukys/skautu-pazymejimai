<?php

class FormaController extends BaseController {

	protected function _forma() {
		$this->page_title = 'Forma';

		$this->render("forma/index");
	}

	protected function _tikrinti_duomenis($duomenys) {
		$errors = array();
		$laukai = array("vardas", "pavarde", "data", "lytis", "kaklaraistis",
			"pareigos", "krastas", "tuntas", "draugove", "adresas");
		foreach ($laukai as $laukas) {
			if (!(isset($duomenys[$laukas]) && !empty($duomenys[$laukas])))
				$errors[] = "Nenurodyta: " . $laukas;
		}
		//Tikrinamas gimimo datos formatas
		if (preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $duomenys["data"], $matches) != 1) {
			$errors[] = "Gimimo datos formatas turi būti MMMM-mm-DD, M - metai; m -mėnuo; D - diena";
		}
	    //Nuotrauka
	    if (isset($duomenys["nuotrauka"]) && !empty($duomenys["nuotrauka"]) && 
	      strcmp($duomenys["nuotrauka"]["name"], "") != 0) {
	        if ($duomenys["nuotrauka"]["error"] == 0) {
	            //tikrinamas tipas
	            if (!empty($duomenys["nuotrauka"]["type"]) && strlen($duomenys["nuotrauka"]["type"]) >= 5 &&
	              strcmp(substr($duomenys["nuotrauka"]["type"],0 , 5), "image") == 0) {
	                //tikrinamas dydis, 41943040 -> 5 MB
	                if ($duomenys["nuotrauka"]["size"] > 41943040) {
	                    $errors[] = "Nuotrauka yra didesnė nei 5 MB";
	                }
	            } else {
	                $errors[] = "Nuotrauka netinkamo tipo. Turi būti JPG, TIF arba PNG tipo.";
	            }
	        } else {
	            $errors[] = "Failo įkėlimo klaida: ".$duomenys["nuotrauka"]["error"];
	        }
	    } else {
	        $errors[] = "Nenurodyta nuotrauka";
	    }
	    
	    //El. Paštas
	    if (!filter_var($duomenys["el_pastas"], FILTER_VALIDATE_EMAIL)) {
	    	$errors[] = "Neteisingai nurodytas el. paštas";
	    }
	    return $errors;
	}

	protected function _saugoti() {
		// Čia pagrindinai apdorojami duomenys
		$duomenys = $_POST;
		$duomenys["nuotrauka"] = $_FILES["nuotrauka"];
		
		$klaidos = $this->_tikrinti_duomenis($duomenys);
		if (!empty($klaidos)) {
			$this->view_assign(array(
				"klaidos" => $klaidos
				));
			$this->page_title = "Klaidos formoje";
			$this->render("forma/index");
		} else {
			// Įterpiama į prašymų lentelę
			require_once 'app/models/prasymas_model.php';
			$pras = new PrasymasModel();
			$id = $pras->_add_prasymas($duomenys);
			//sunaikinamas prisijungimas prie DB
			unset($pras);
			require_once 'app/models/bukle_model.php';
			$bukl = new BukleModel();
			$bukl->add_bukle(array("id" => $id));
			$bukle = $bukl->get_bukle($id);
			$this->view_assign(array(
				"bukle_id" => $bukle["id"],
				"bukle_pagamintas"	=> $bukle["pagamintas"],
				"bukle_vardas"		=> $duomenys["vardas"],
				"bukle_pavarde"		=> $duomenys["pavarde"],
				"bukle_nuotrauka"		=> $bukle["nuotrauka"],
				"bukle_nario_mokestis"=> $bukle["mokestis"],
				"bukle_gaminimo_data" => $bukle["data"]
				));
			//if sekmingai priduota peradresuoti i bukle
			$this->page_title = "Forma gauta";
			$this->render("forma/bukle");
		}
	}

	protected function _bukle() {
		if (!isset($_GET, $_GET["id"])) {
			header("Location: /");
			die();
		}
		require_once "app/models/bukle_model.php";
		$bukl = new BukleModel();
		$ids = $bukl->get_ids();
		if (!isset($ids[$_GET["id"]])) {
			header("Location: /");
			die();
		}
		$bukle = $bukl->get_bukle($_GET["id"]);
		unset($bukl);
		require_once "app/models/prasymas_model.php";
		$pras = new PrasymasModel();
		$prasymas = $pras->_get_prasymas($_GET["id"]);
		$this->page_title = 'Pažymėjimo būklė';
		$this->view_assign(array(
			"bukle_id" => $bukle["id"],
			"bukle_pagamintas"	=> $bukle["pagamintas"],
			"bukle_vardas"		=> $prasymas["vardas"],
			"bukle_pavarde"		=> $prasymas["pavarde"],
			"bukle_nuotrauka"		=> $bukle["nuotrauka"],
			"bukle_nario_mokestis"=> $bukle["mokestis"],
			"bukle_gaminimo_data" => $bukle["data"]
			));
		
		$this->render("forma/bukle");
	}
}

?>