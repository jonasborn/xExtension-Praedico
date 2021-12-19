<?php


namespace dde;

require_once __DIR__ . "/../database/PraedicoDatabase.php";

use DateTime;
use FreshRSS_Entry;
use FreshRSS_Factory;
use TeamTNT\TNTSearch\Classifier\TNTClassifier;

class PraedicoUser {

	public $name;


	/**
	 * @param $item FreshRSS_Entry
	 */
	public function predict(FreshRSS_Entry $item) {
		$dataset = PraedicoFiles::userDataset($this->name);
		if (!file_exists($dataset)) return null;
		$classifier = new TNTClassifier();
		$classifier->load($dataset);
		return $classifier->predict($item->content());
	}

	public function train() {
		$db = PraedicoDatabase::user($this->name);
		$classifier = new TNTClassifier();
		$evaluations = $db->query("SELECT * FROM evaluations ORDER BY RANDOM()");
		while($row = $evaluations->fetchArray()) {
			if (!empty($row)) {
				$entryDAO = FreshRSS_Factory::createEntryDao();
				$entries = $entryDAO->listByIds([$row["id"]]);
				/** @var FreshRSS_Entry $entry */
				foreach ($entries as $entry) {
					$classifier->learn($entry->content(), $row["class"]);
				}
			}
		}
		$classifier->save(PraedicoFiles::userDataset($this->name));
	}

	/**
	 * @param $id string
	 * @param $cls int
	 */
	public function add($id, $cls) {
		$db = PraedicoDatabase::user($this->name);
		$q = $db->prepare("REPLACE INTO evaluations VALUES(:id, :cls, :creation)");
		$q->bindValue(":id", $id);
		$q->bindValue(":cls", $cls);
		$q->bindValue(":creation", time());
		$q->execute();
	}

	public function remove($id) {
		$db = PraedicoDatabase::user($this->name);
		$q = $db->prepare("DELETE FROM evaluations WHERE id = :id");
		$q->bindValue(":id", $id);
		$q->execute();
	}




}
