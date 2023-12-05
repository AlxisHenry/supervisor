<?php

declare(strict_types=1);

/**
 * Ce script permet de modifier ou supprimer un mouvement
 */

include_once "../../prog/start.php";
startPage(false);

$action = $_POST['action'] ?? "";
$movementId = $_POST['movement_id'] ?? "";
$assetId = $_POST['asset_id'] ?? "";

switch ($action) {
	case 'edit':
		$remarque = $_POST['remarque'];
		P_mouvement::update($movementId, $remarque);
		break;
	case 'delete':
		try {
			P_mouvement::delete($movementId);
		} catch (Exception $e) {
			echo false;
		}
		break;
	case 'get':
		$query = "SELECT 
			date,
			p_localisation.nom as localisation, 
			CONCAT(utilisateurs.nom, ' ', utilisateurs.prenom) AS user,
			remarque
			FROM p_mouvements
		INNER JOIN p_localisation ON p_mouvements.localisation = p_localisation.id
		INNER JOIN utilisateurs ON p_mouvements.user = utilisateurs.id
		WHERE p_mouvements.id = ? LIMIT 1;";
		$sql = connectPdo()->prepare($query);
		$sql->execute([$movementId]);
		$data = $sql->fetch(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		echo json_encode($data);
		exit();
}

echo Table::movements(P_matos::create((int) $assetId));
