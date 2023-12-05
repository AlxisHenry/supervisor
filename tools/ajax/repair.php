<?php

declare(strict_types=1);

/**
 * Ce script permet de récupérer, créer, modifier ou supprimer une réparation
 */

include_once "../../prog/start.php";
startPage(false);

$fromAssetPage = $_POST['fromAssetPage'] ?? null;
$assetId = $_POST['asset_id'] ?? null;
$repairId = $_POST['repair_id'] ?? null;
$action = $_POST['action'] ?? $_GET['action'] ?? null;

switch ($action) {
	case "new":
		// Création d'une nouvelle réparation
		$query = "INSERT INTO p_reparations (type, num, intervenant, cout, date, remarque, matos_id, is_finished, created_at) VALUES (:type, :num, :intervenant, :cout, :date, :remarque, :matos_id, :is_finished, :created_at)";
		$pdo = connectPdo()->prepare($query);
		if (!$assetId) {
			echo false;
			exit;
		}
		$cost = str_replace(",", ".", $_POST['cost'] ?? "0.0");
		$pdo->execute([
			"type" => $_POST['type'] ?? "Externe",
			"num" => $_POST['num'] ?? "",
			"intervenant" => $_POST['intervenant'] ?? "Technicien",
			"cout" =>  (float) $cost,
			"date" => $_POST['date'] === "" ? null : $_POST['date'],
			"remarque" => $_POST['remarque'] ?? "",
			"matos_id" => (int) $assetId,
			"is_finished" => isset($_POST['is_finished']) ? 1 : 0,
			"created_at" => date("Y-m-d H:i:s")
		]);
		$pdo->closeCursor();
		break;
	case "delete":
		$query = "DELETE FROM p_reparations WHERE id = :id";
		$pdo = connectPdo()->prepare($query);
		$pdo->execute([
			"id" => $repairId
		]);
		$pdo->closeCursor();
		break;
	case "edit":
		// Modification de la réparation
		$query = "UPDATE p_reparations SET type = :type, num = :num, intervenant = :intervenant, cout = :cout, date = :date, remarque = :remarque, is_finished = :is_finished WHERE id = :id";
		$pdo = connectPdo()->prepare($query);
		$cost = str_replace(",", ".", $_POST['cost'] ?? "0.0");
		$pdo->execute([
			"type" => $_POST['type'] ?? "Externe",
			"num" => $_POST['num'] ?? "",
			"intervenant" => $_POST['intervenant'] ?? "Technicien",
			"cout" => (float) $cost,
			"date" => $_POST['date'] === "" ? null : $_POST['date'],
			"remarque" => $_POST['remarque'] ?? "",
			"is_finished" => isset($_POST['is_finished']) ? 1 : 0,
			"id" => (int) $repairId
		]);
		$pdo->closeCursor();
		break;
	case 'get':
		$repairId = $_GET['repair_id'] ?? null;
		$query = "SELECT * FROM p_reparations WHERE id = :id";
		$sql = connectPdo()->prepare($query);
		$sql->execute([
			"id" => $repairId
		]);
		$data = $sql->fetch(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		echo json_encode($data);
		exit();
}

// Si on est en train de créer ou modifier une réparation, on met à jour l'asset
if (in_array($action, ["new", "edit"])) {
	// On supprime les clés liées à la réparation
	unset($_POST['type'], $_POST['num'], $_POST['intervenant'], $_POST['cost'], $_POST['date'], $_POST['remarque'], $_POST['is_finished']);
	$asset = P_matos::create((int) $assetId);
	$_POST['statut_ad'] = $asset->getStatut_ad();
	P_matos::update(P_matos::createFromArray($_POST, $asset));
}

$computer = null;
$tables = [];

if ($fromAssetPage === "true") {
	$computer = P_matos::create((int) $assetId);
};

echo json_encode(Table::repairs(asset: $computer));