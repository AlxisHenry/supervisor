<?php
declare(strict_types=1);
include '../../prog/start.php';
startPage(false);

$date = date("Y-m-d_H-i");
$filename = "assets-$date.csv";
$file = fopen('php://output', 'w');
fputs($file, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));
$delimiter = ';';

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);

if ($_GET['archived'] ?? false) {
	$key = "filters_archived_assets";
} else {
	$key = "filters_assets";
}

$query = Filter::getQuery($key, [], true, $wheresCount);
$pdo = connectPdo()->query((string) $query);
$list = $pdo->fetchAll(PDO::FETCH_ASSOC);
$pdo->closeCursor();

function getColumnsNames(): array
{
	return [
		"Asset",
		"Statut",
		"Numéro de série",
		"Utilisateur",
		"Localisation",
		"Domaine",
		"Remarque",
		"Commentaire",
		"Type de matériel",
		"Marque",
		"Modèle",
		"Clavier",
		"Version du BIOS",
		"Processeur",
		"OS",
		"OS Version",
		"OS Bits",
		"Language",
		"Disque dur",
		"RAM Emplacements",
		"RAM Installée (Go)",
		"RAM Capacité (Go)",
		"Type d'acquisition",
		"Date d'acquisition",
		"Durée de location",
		"N° d'immobilisation",
		"Nombre de mouvements",
		"Nombre de réparations",
		"Dernière mise à jour depuis AD",
		"Derière mise à jour depuis WMI",
	];
}

$fields = getColumnsNames();
$assets = [];

foreach ($list as $asset) {
	$computer = P_matos::create($asset["id"]);
	if (!$computer) continue;
	$disk = Functions::formatDiskSize($computer);
	$assets[] = [
		$computer->getAsset(),
		DB::findValueInTable("p_statut", "nom", "id", $computer->getStatut()),
		$computer->getSn(),
		DB::findValueInTable('utilisateurs', 'nom,prenom', 'id', $computer->getUser()),
		DB::findValueInTable("p_localisation", "nom", "id", $computer->getLocalisation()),
		$computer->getStatut_ad() ? "Oui" : "Non",
		$computer->getRemarque(),
		$computer->getComment(),
		DB::findValueInTable("p_type", "nom", "id", $computer->getType()),
		DB::findValueInTable("p_marque", "nom", "id", $computer->getMarque()),
		$computer->getModele(),
		$computer->getClavier(),
		$computer->getBios(),
		$computer->getProcesseur(),
		DB::findValueInTable("p_os", "nom", "id", $computer->getOs()),
		DB::findValueInTable("p_os_version", "nom", "id", $computer->getOs_version()),
		$computer->getOs_bits(),
		$computer->getLangue(),
		$disk,
	    $computer->getSlot_ram(),
		$computer->getRam(),
		$computer->getRam_max(),
		$computer->getType_achat(),
		$computer->getDate_achat(),
		$computer->getDuree_loc(),
		$computer->getNum_immo(),
		Functions::count("p_mouvements", "matos = " . $computer->getId()),
		Functions::count("p_reparations", "matos_id = " . $computer->getId()),
		$computer->getUpdate_ad(),
		$computer->getUpdate_wmi(),
	];
}

fputcsv($file, $fields, $delimiter);

foreach ($assets as $asset) {
	fputcsv($file, $asset, $delimiter);
}

exit();