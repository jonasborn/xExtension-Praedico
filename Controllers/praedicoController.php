<?php

require_once __DIR__ . "/../src/user/PraedicoUser.php";
require_once __DIR__ . "/../src/user/PraedicoUsers.php";

class FreshExtension_praedico_Controller extends Minz_ActionController {

	/**
	 * @var Minz_Extension
	 */
	private $extension;

	public function init() {
		$this->extension = Minz_ExtensionManager::findExtension('Praedico');
	}

	public function evaluateAction() {

		$id = Minz_Request::param('id', null);
		if (null === $id) {
			Minz_Error::error(400);
		}

		$evaluation = Minz_Request::param('evaluation', null);
		if (null === $evaluation) {
			Minz_Error::error(400);
		}

		$username = Minz_Session::param('currentUser', '_');

		$user = \dde\PraedicoUsers::get($username);

		if ($evaluation == "pos") {
			$user->addPositive($id);
			echo "Pos";
		} else if ($evaluation == "neg") {
			$user->addNegative($id);
			echo "Neg";
		} else {
			Minz_Error::error(400);
		}

		$user->train();

		Minz_View::appendScript($this->extension->getFileUrl('praedicoEvaluation.js', 'js'));
	}
}
