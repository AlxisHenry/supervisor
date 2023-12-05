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
		$query = "INSERT INTO p_marque (nom) VALUES (?)";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$_POST['nom']]);
		break;
}

$statement->closeCursor();
echo Table::manufacturers();