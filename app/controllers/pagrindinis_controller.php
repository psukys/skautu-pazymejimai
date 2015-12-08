<?php

class PagrindinisController extends BaseController {

	protected function _pagrindinis() {
		$this->page_title = 'Pagrindinis';

		$this->render("pagrindinis/index");
	}
}

?>