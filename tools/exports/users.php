<?php
declare(strict_types=1);
include '../../prog/start.php';
startPage(false);

$date = date("Y-m-d_H-i");
$filename = "users-$date.csv";
$file = fopen('php://output', 'w');
fputs($file, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
$delimiter = ';';

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

$query = Filter::getQuery("filters_users", [], true, $wheresCount);
$pdo = connectPdo()->query((string) $query);
$list = $pdo->fetchAll(PDO::FETCH_ASSOC);
$pdo->closeCursor();

function getColumnsNames(): array
{
	return [
		"ID",
		"Nom",
		"Prénom"
	];
}

$fields = getColumnsNames();
$users = [];
$totalCost = 0;

foreach ($list as $user) {
	$users[] = [
		$user["id"],
		$user["nom"],
		$user["prenom"]
	];
}

fputcsv($file, $fields, $delimiter);

foreach ($users as $user) {
	fputcsv($file, $user, $delimiter);
}

exit();
