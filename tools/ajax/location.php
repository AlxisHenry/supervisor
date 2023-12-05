<?php

declare(strict_types=1);

/**
 * Ce script permet de créer, modifier ou supprimer un utilisateur
 */

include_once "../../prog/start.php";
startPage(false);

$action = $_POST['action'];
$id = $_POST['id'];

switch ($action) {
	case 'new':
		$query = "INSERT INTO p_localisation (nom) VALUES (?)";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$_POST['nom']]);
		break;
	case 'get':
		$query = "SELECT * FROM p_localisation WHERE id = ?";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$id]);
		$location = $statement->fetch(PDO::FETCH_ASSOC);
		$statement->closeCursor();
		echo json_encode($location);	
		exit();
	case 'edit':
		$query = "UPDATE p_localisation SET nom = ? WHERE id = ?";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$_POST['nom'], $id]);
		break;
}

$statement->closeCursor();
echo Table::locations();