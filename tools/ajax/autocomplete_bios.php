<?php

declare(strict_types=1);

/**
 * Ce script permet d'alimenter l'autocomplétion des assets
 */

include_once '../../prog/start.php';
startPage(false);

$rech = $_GET["term"];
$query = "SELECT DISTINCT bios as label FROM p_matos WHERE bios LIKE ? ORDER BY bios LIMIT 15;";
$pdo = connectPdo()->prepare($query);
$pdo->bindValue(1, "%$rech%", PDO::PARAM_STR);
$pdo->setFetchMode(PDO::FETCH_ASSOC);
$pdo->execute();
$bios = $pdo->fetchAll();

echo json_encode($bios);
