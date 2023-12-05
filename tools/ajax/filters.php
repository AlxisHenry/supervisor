<?php
declare(strict_types=1);

/**
 * Ce script gère les filtres des différents tableaux
 */

include_once '../../prog/start.php';
startPage(false);

$filter = $_POST['filter'] ?? "";

$arr = [
	"is_finished" => $_POST['is_finished'] ?? null,
	"search" => $_POST['search'] ?? null,
	"os" => $_POST['os'] ?? null,
	"vos" => $_POST['os_version'] ?? null,
	"type" => $_POST['type'] ?? null,
	"typeIntervention" => $_POST['typeIntervention'] ?? null,
	"loc" => $_POST['localisation'] ?? null,
	"modele" => $_POST['modele'] ?? null,
	"marque" => $_POST['marque'] ?? null,
	"statut" => $_POST['statut'] ?? null,
	"statut_ad" => $_POST['statut_ad'] ?? null
];

// On génère dynamiquement le nom de la méthode à appeler en fonction du filtre
$method = explode("_", $filter)[1];

if ($method === "archived") {
	echo Table::assets($arr, archived: true);
} else {
	echo Table::$method($arr);
}
