<?php

declare(strict_types=1);

/**
 * Ce script alimente l'autocomplétion des modèles d'ordinateurs
 */

include_once "../../prog/start.php";
startPage(false);

$rech = $_GET["term"];
$query = "SELECT DISTINCT modele as label FROM p_matos WHERE modele LIKE ? ORDER BY modele LIMIT 20;";
$sql = connectPdo()->prepare($query);
$sql->bindValue(1, "%$rech%", PDO::PARAM_STR);
$sql->execute();
$data = $sql->fetchAll(PDO::FETCH_COLUMN,0);
$sql->closeCursor();
echo json_encode($data);