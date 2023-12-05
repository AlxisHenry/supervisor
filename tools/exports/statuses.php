<?php
declare(strict_types=1);
include '../../prog/start.php';
startPage(false);

$date = date("Y-m-d_H-i");
$filename = "statuses-$date.csv";
$file = fopen('php://output', 'w');
fputs($file, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
$delimiter = ';';

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

$query = Filter::getQuery("filters_statuses", [], true, $wheresCount);
$pdo = connectPdo()->query((string) $query);
$list = $pdo->fetchAll(PDO::FETCH_ASSOC);
$pdo->closeCursor();

function getColumnsNames(): array
{
	return [
		"ID du statut",
		"Intitulé du statut",
	];
}

$fields = getColumnsNames();
$statuses = [];
$totalCost = 0;

foreach ($list as $status) {

	$statuses[] = [
		$status["id"],
		$status["nom"]
	];
}

fputcsv($file, $fields, $delimiter);

usort($statuses, function ($a, $b) {
	return $b[1] <=> $a[1];
});

foreach ($statuses as $status) {
	fputcsv($file, $status, $delimiter);
}

exit();
