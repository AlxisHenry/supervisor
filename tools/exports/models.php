<?php
declare(strict_types=1);
include '../../prog/start.php';
startPage(false);

$date = date("Y-m-d_H-i");
$filename = "models-$date.csv";
$file = fopen('php://output', 'w');
fputs($file, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
$delimiter = ';';

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

$query = Filter::getQuery("filters_models", [], true, $wheresCount);
$pdo = connectPdo()->query((string) $query);
$list = $pdo->fetchAll(PDO::FETCH_ASSOC);
$pdo->closeCursor();

function getColumnsNames(): array
{
	return [
		"Nom du modèle",
		"Nombre d'ordinateurs",
	];
}

$fields = getColumnsNames();
$models = [];
$totalCost = 0;

foreach ($list as $model) {

	$models[] = [
		$model["nom"],
		$model["nb"]
	];
}

fputcsv($file, $fields, $delimiter);

usort($models, function ($a, $b) {
	return $b[1] <=> $a[1];
});

foreach ($models as $model) {
	fputcsv($file, $model, $delimiter);
}

exit();
