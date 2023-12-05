<?php

declare(strict_types=1);

/**
 * Ce script permet d'alimenter l'autocomplétion des assets
 */

include_once '../../prog/start.php';
startPage(false);

$rech = $_GET["term"];
$query = "SELECT `id`, 
			CONCAT(nom, ' ', prenom) as `label` 
		  FROM utilisateurs 
		  WHERE 
		  	CONCAT(nom, ' ', prenom) LIKE ? 
			OR CONCAT(prenom, ' ', nom) LIKE ? 
		  ORDER by nom, prenom 
		  LIMIT 15;";
$pdo = connectPdo()->prepare($query);
$pdo->bindValue(1, "%$rech%", PDO::PARAM_STR);
$pdo->bindValue(2, "%$rech%", PDO::PARAM_STR);
$pdo->setFetchMode(PDO::FETCH_ASSOC);
$pdo->execute();
$assets = $pdo->fetchAll();

echo json_encode($assets);
