<?php

declare(strict_types=1);

/**
 * Ce script permet de supprimer un asset 
 */

include_once "../../prog/start.php";
startPage(false);

$id = $_POST['id'];
$query = "DELETE FROM p_matos WHERE id = ?; 
		  DELETE FROM p_reparations WHERE matos_id = ?; 
		  DELETE FROM p_mouvements WHERE matos = ?;"; 
$statement = connectPdo()->prepare($query);
$statement->execute([$id, $id, $id]);

echo Table::assets();