<?php

declare(strict_types=1);

/**
 * Ce script permet de récupérer les informations d'un ordinateur dont l'id est envoyé en paramètre
 */

include_once "../../prog/start.php";
startPage(false);

$id = $_GET['id'];
$computer = P_matos::create((int) $id);

echo json_encode([
	"id" => $computer->getId(),
	"asset" => $computer->getAsset(),
	"modele" => $computer->getModele(),
	"marque" => $computer->getMarque(),
	"statut" => $computer->getStatut(),
	"loc" => $computer->getLocalisation()
]);
