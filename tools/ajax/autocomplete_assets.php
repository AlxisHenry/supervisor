<?php

declare(strict_types=1);

/**
 * Ce script permet d'alimenter l'autocomplétion des assets
 */

include_once '../../prog/start.php';
startPage(false);

$rech = $_GET["term"];
$query = "SELECT `id`, `asset` as `label` FROM p_matos WHERE asset LIKE ? ORDER by asset LIMIT 15;";
$pdo = connectPdo()->prepare($query);
$pdo->bindValue(1, "%$rech%", PDO::PARAM_STR);
$pdo->setFetchMode(PDO::FETCH_ASSOC);
$pdo->execute();
$assets = $pdo->fetchAll();

echo json_encode($assets);
