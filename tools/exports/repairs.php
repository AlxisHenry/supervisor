<?php
declare(strict_types=1);
include '../../prog/start.php';
startPage(false);

$date = date("Y-m-d_H-i");
$filename = "repairs-$date.csv";
$file = fopen('php://output', 'w');
fputs($file, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
$delimiter = ';';

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

$query = Filter::getQuery("filters_repairs", [], true, $wheresCount);
$pdo = connectPdo()->query((string) $query);
$list = $pdo->fetchAll(PDO::FETCH_ASSOC);
$pdo->closeCursor();

function getColumnsNames(): array
{
	return [
		"N° de réparation",
		"N° de série",
		"Asset",
		"Type d'intervention",
		"Intervenant",
		"Coût",
		"Date d'intervention",
		"Remarque",
		"Statut",
		"Date de création",
		"Coût total"
	];
}

$fields = getColumnsNames();
$repairs = [];
$totalCost = 0;

foreach ($list as $repair) {

	if (gettype($repair["id"]) === "integer") $computer = P_matos::create($repair["id"]);
	$type = $repair["type"] === "E" ? "Externe" : "Interne";
	$statut = $repair["is_finished"] === 1 ? "Terminé" : "En attente";
	$totalCost += $repair["cout"];

	$repairs[] = [
		$repair["num"],
		$repair["sn"] ?? $computer->getSn() === "" ? "N/A" : $computer->getSn(),
		$repair["asset"] ?? $computer->getAsset(),
		$type,
		$repair["intervenant"],
		Functions::displayNumberIntoMonetary($repair["cout"], false),
		Functions::formatDate($repair["date"], true),
		$repair["remarque"],
		$statut,
		date("d/m/Y", strtotime($repair["created_at"])),
		Functions::displayNumberIntoMonetary($totalCost, false)
	];
}

fputcsv($file, $fields, $delimiter);

foreach ($repairs as $repair) {
	fputcsv($file, $repair, $delimiter);
}

exit();
