<?php
declare(strict_types=1);
include '../../prog/start.php';
startPage(false);

$from = $_GET["from"] ?? "COLWD6011L";
$to = $_GET["to"] ?? "";
$archive = (int) $_GET["archived"] ?? false;

if (!$from) exit("Missing 'from' parameter");
if ($to === "") $to = $from;

$date = date("Y-m-d_H-i");
$filename = "assets-$date.csv";
$file = fopen('php://output', 'w');
fputs($file, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
$delimiter = ';';

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

$query = "SELECT id FROM p_matos WHERE asset BETWEEN ? AND ? ";
if ($archive) $query .= " AND statut IN (6,7)";
$query .= " ORDER BY asset ASC;";
$pdo = connectPdo()->prepare($query);
$pdo->execute([$from, $to]);
$list = $pdo->fetchAll(PDO::FETCH_ASSOC);
$pdo->closeCursor();

const DEFAULT_LOCATION = "Colmar";
const SPARE = "SPARE IT";

$assets = [];

foreach ($list as $asset) {
	$computer = P_matos::create($asset["id"]);
	if (!$computer) continue;
	$isAffected = DB::findValueInTable("utilisateurs", "prenom", "id", $computer->getUser()) !== "IT";
	$isSpare = $isAffected === false;
	if ($isAffected) {
		$name = DB::findValueInTable("utilisateurs", "nom", "id", $computer->getUser());
		$firstname = DB::findValueInTable("utilisateurs", "prenom", "id", $computer->getUser());
	}
	$assets[] = [
		$computer->getAsset(),
		DB::findValueInTable("p_os", "nom", "id", $computer->getOs()) 
		. " - " . 
		DB::findValueInTable("p_os_version", "nom", "id", $computer->getOs_version()), // Sytème d'exploitation
		DEFAULT_LOCATION, // Localisation
		$computer->getStatut_ad() ? "Active" : "Not AD", // Statut AD
		$isAffected ? "Affected" : "Not affected", // Statut d'affectation
		$computer->getType_achat() === "Leasing" ? "LEASING" : $computer->getNum_immo(), // Numéro d'immobilisation
		"", // Centre de coût
		DB::findValueInTable('p_type', "nom", "id", $computer->getType()), // Type de matériel
		$computer->getModele(), // Modèle 
		$isSpare ? SPARE : "",
		$isSpare ? SPARE : "", // User ID
		$isSpare ? SPARE : ($name ?? ""), // Nom de l'utilisateur
		$isSpare ? SPARE : ($firstname ?? ""), // Prénom de l'utilisateur
		$computer->getDate_achat(), // Date d'acquisition
		"", // Age du matériel en jours (calculé dynamiquement)
		$computer->getSn(), // Numéro de série
		"model {$computer->getModele()}", // Désignation de l'immobilisation
		$computer->getComment(), // Commentaire hors AD,
		DB::findValueInTable('p_localisation', 'nom', 'id', $computer->getLocalisation()), // Localisation
	];
}

foreach ($assets as $asset) {
	fputcsv($file, $asset, $delimiter);
}

die();