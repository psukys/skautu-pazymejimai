<?php

class AdministravimasController extends BaseController {

	protected function _administravimas() {
		session_start();
		if (isset($_SESSION["autentifikuotas"]) &&
			$_SESSION["autentifikuotas"] == true) {
			$this->page_title = 'Administravimas';
			include 'app/models/prasymas_model.php';
			$prasymas = new PrasymasModel();
			
			//perduodami prašymai, kuriems pažymėjimai nepagaminti
			$prasymai = $prasymas->_get_all_prasymai();
			$nepagaminti_prasymai = sizeof($prasymai);
			
			$this->view_assign(array(
				"prasymai" => $nepagaminti_prasymai
			));
			$this->render("administravimas/index");
		} else {
			$url = "http" . ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "/../prisijungimas";
			header("Location: " . $url);
			die();
		}
	}

	protected function _prisijungimas() {
		$this->page_title = 'Administravimo prisijungimas';
		session_start();
		// Žiūrima ar yra POST duomenų ir konkrečiai ar tai prisijungimas
		if (isset($_POST, $_POST["prisijungimas"], $_POST["slaptazodis"])) {
			include 'app/models/paskyra_model.php';
			$paskyra = new PaskyraModel();
			//tikriname įvestį
			if ($paskyra->check_login($_POST["prisijungimas"], $_POST["slaptazodis"])) {
				$_SESSION["autentifikuotas"] = true;
				$url = "http" . ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "/../administravimas";
				header("Location: " . $url);
				die();
			die();
			} else {
				$this->view_assign(array(
					"nepavyko_prisijungti" => "Neteisingas prisijungimo vardas arba slaptažodis"
				));
				$this->render("administravimas/prisijungimas");
			}
		} else {
			$this->render("administravimas/prisijungimas");
		}
	}

	protected function _atsijungimas() {
		//pašalinamas sesijos tokenas 
		if (!isset($_SESSION)) session_start();
		$_SESSION["autentifikuotas"] = false;
		session_unset();
		session_destroy();

		$url = "http" . ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "/../prisijungimas";
		header("Location: " . $url);
		die();
	}
}

?>