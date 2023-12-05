<?php

declare(strict_types=1);

/**
 * Ce script permet d'alimenter l'autocomplétion des assets
 */

include_once '../../prog/start.php';
startPage(false);

$rech = $_GET["term"];
$query = "SELECT DISTINCT processeur as label FROM p_matos WHERE processeur LIKE ? ORDER BY processeur LIMIT 15;";
$pdo = connectPdo()->prepare($query);
$pdo->bindValue(1, "%$rech%", PDO::PARAM_STR);
$pdo->setFetchMode(PDO::FETCH_ASSOC);
$pdo->execute();
$processors = $pdo->fetchAll();

echo json_encode($processors);
