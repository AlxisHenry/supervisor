<?php
declare(strict_types=1);
include '../../prog/start.php';
startPage(false);

$date = date("Y-m-d_H-i");
$filename = "locations-$date.csv";
$file = fopen('php://output', 'w');
fputs($file, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
$delimiter = ';';

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

$query = Filter::getQuery("filters_locations", [], true, $wheresCount);
$pdo = connectPdo()->query((string) $query);
$list = $pdo->fetchAll(PDO::FETCH_ASSOC);
$pdo->closeCursor();

function getColumnsNames(): array
{
	return [
		"ID de la localisation",
		"Intitulé de la localisation",
	];
}

$fields = getColumnsNames();
$locations = [];
$totalCost = 0;

foreach ($list as $location) {

	$locations[] = [
		$location["id"],
		$location["nom"]
	];
}

fputcsv($file, $fields, $delimiter);

foreach ($locations as $location) {
	fputcsv($file, $location, $delimiter);
}

exit();
