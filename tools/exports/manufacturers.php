<?php
declare(strict_types=1);
include '../../prog/start.php';
startPage(false);

$date = date("Y-m-d_H-i");
$filename = "manufacturers-$date.csv";
$file = fopen('php://output', 'w');
fputs($file, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
$delimiter = ';';

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

$query = Filter::getQuery("filters_manufacturers", [], true, $wheresCount);
$pdo = connectPdo()->query((string) $query);
$list = $pdo->fetchAll(PDO::FETCH_ASSOC);
$pdo->closeCursor();

function getColumnsNames(): array
{
	return [
		"ID de la marque",
		"Intitulé de la marque",
	];
}

$fields = getColumnsNames();
$manufacturers = [];
$totalCost = 0;

foreach ($list as $manufacturer) {

	$manufacturers[] = [
		$manufacturer["id"],
		$manufacturer["nom"]
	];
}

fputcsv($file, $fields, $delimiter);

foreach ($manufacturers as $manufacturer) {
	fputcsv($file, $manufacturer, $delimiter);
}

exit();
