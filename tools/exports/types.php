<?php
declare(strict_types=1);
include '../../prog/start.php';
startPage(false);

$date = date("Y-m-d_H-i");
$filename = "types-$date.csv";
$file = fopen('php://output', 'w');
fputs($file, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
$delimiter = ';';

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

$query = Filter::getQuery("filters_types", [], true, $wheresCount);
$pdo = connectPdo()->query((string) $query);
$list = $pdo->fetchAll(PDO::FETCH_ASSOC);
$pdo->closeCursor();

function getColumnsNames(): array
{
	return [
		"ID du type",
		"Intitulé du type",
	];
}

$fields = getColumnsNames();
$types = [];
$totalCost = 0;

foreach ($list as $type) {

	$types[] = [
		$type["id"],
		$type["nom"]
	];
}

fputcsv($file, $fields, $delimiter);

foreach ($types as $type) {
	fputcsv($file, $type, $delimiter);
}

exit();
