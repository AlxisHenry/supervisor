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
		$query = "INSERT INTO utilisateurs (nom, prenom) VALUES (?, ?)";
		$statement = connectPdo()->prepare($query);
		$name = strtoupper($_POST['nom']);
		$firstname = strtoupper($_POST['prenom']);
		$statement->execute([$name, $firstname]);
		break;
	case 'edit':
		$query = "UPDATE utilisateurs SET nom = ?, prenom = ? WHERE id = ?";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$_POST['nom'], $_POST['prenom'], $id]);
		break;
	case 'delete':
		// On vérifie si l'utilisateur n'est pas lié à un asset
		$assets = DB::count('p_matos', 'user', $id);
		if ($assets > 0) {
			echo false;
			exit();
		}

		// On vérifie si l'utilisateur n'est pas lié à un mouvement
		$movements = DB::count('p_mouvements', 'user', $id);
		if ($movements > 0) {
			echo false;
			exit();
		}
		
		// On vérifie si l'utilisateur n'est pas lié à un mouvement de stock
		$stockMovements = DB::count('mouvements', 'users', $id);
		if ($stockMovements > 0) {
			echo false;
			exit();
		}

		$query = "DELETE FROM utilisateurs WHERE id = ?";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$id]);
		break;
	case 'get':
		$query = "SELECT * FROM utilisateurs WHERE id = ?";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$id]);
		$user = $statement->fetch(PDO::FETCH_ASSOC);
		echo json_encode($user);
		exit();
}

$statement->closeCursor();
echo Table::users();