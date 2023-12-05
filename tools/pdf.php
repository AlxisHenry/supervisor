<?php

declare(strict_types=1);

include_once "../prog/start.php";
startPage(false);

const COMPANY_NAME = "";

/**
 * Traduit une chaîne de caractères en ISO-8859-1
 *
 * @param string $str
 */
function __($str): string
{
	// Equivalent utf8_decode
	return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
}

function exists(string $file): bool
{
    return file_exists(ROOT . "/css/images/". COMPANY_NAME . "-$file.png");
}

if (!isset($_GET["id"])) {
	die("Paramètre manquant");
}

$asset = P_matos::create($_GET["id"]);

if (!$asset) {
	die("Asset introuvable");
}

require ROOT . "/vendor/fpdf/fpdf.php";

// Instantiate and use the FPDF class
$pdf = new FPDF();

//Add a new page
$pdf->AddPage();
$pdf->SetAuthor("<author>");
$pdf->SetTitle("{$asset->getAsset()} - Fiche d'identification");
$pdf->SetSubject("Fiche d'identification de l'asset {$asset->getAsset()}");

$pdf->AddFont('RobotoMono', '', 'RobotoMono-Regular.php');
// put image in cell with scaling
if (exists("-pdf-header")) {
    $pdf->Image(ROOT . "/css/images/". COMPANY_NAME ."-pdf-header.png", $pdf->GetX() - 12, $pdf->GetY() - 10, 0, 0, 'PNG');
}
// put the date in the top right corner
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY($pdf->GetX() + 165, $pdf->GetY() + 2);
$pdf->Write(0, date("d/m/Y"));

if (exists("-pdf-footer")) {
    $pdf->Image(ROOT . "/css/images/" . COMPANY_NAME . "-pdf-footer.png", $pdf->GetY() - 7, 280, 0, 0, 'PNG');
}

$pdf->SetFont('Arial', 'b', 30);

$pdf->Ln(40);

$pdf->Write(0, __($asset->getAsset()), DOMAIN . "?mod=asset&id={$asset->getId()}");

if ($asset->getSn()) {
	$pdf->Write(0, " / ");
	$pdf->SetFont('Arial', '', 16);
	$pdf->SetXY($pdf->GetX(), $pdf->GetY() + 1);
	$pdf->Write(0, __($asset->getSn()));
}

$pdf->Ln(6);

$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 10, __($asset->getRemarque()), 0, 'L');

$pdf->Ln(10);

$pdf->SetFont('Helvetica', 'b', 12);
$pdf->Cell(0, 10, __("Informations générales", 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
$pdf->SetFont('Helvetica', '', 12);
$pdf->MultiCell(0, 10, __("Cet ordinateur est un "
	. DB::findValueInTable('p_marque', 'nom', 'id', $asset->getMarque())
	. " - {$asset->getModele()} avec un clavier {$asset->getClavier()}."), 0, 'L');
$pdf->MultiCell(0, 10, __("L'ordinateur possède {$asset->getRam()} Go de RAM."), 0, 'L');
$pdf->MultiCell(0, 10, __("Il fonctionne sous "
	. DB::findValueInTable('p_os', 'nom', 'id', $asset->getOs())
	. " {$asset->getOs_bits()} - {$asset->getLangue()}, version "
	. DB::findValueInTable('p_os_version', 'nom', 'id', $asset->getOs_version())
	. '.'), 0, 'L');

if ($asset->getAdm_pwd()) {
	$pdf->SetFont('Helvetica', 'b', 12);
	$pdf->Cell(0, 10, "Compte administrateur", 0, 1, 'L');
	$pdf->SetFont('Helvetica', '', 12);
	$pdf->Cell(0, 10, __("Identifiant : " . LOCAL_ADMIN_ACCOUNT), 0, 1, 'L');
	$pdf->Write(10, __("Mot de passe : "));
	$pdf->SetFont('RobotoMono', '', 16);
	$pdf->Write(10, $asset->getAdm_pwd(), 0, 1, 'L');
}

if ($asset->getBitlocker_id()) {
	$pdf->Ln(20);
	$pdf->SetFont('Helvetica', 'b', 12);
	$pdf->Cell(0, 10, "BitLocker Drive Encryption", 0, 1, 'L');
	$pdf->SetFont('Helvetica', '', 12);
	$pdf->Cell(0, 10, __("Le chiffrement du disque a eu lieu le " . Functions::formatDate($asset->getBitlocker_date(), true, true) . "."), 0, 1, 'L');
	$pdf->MultiCell(0, 10, __("Identifiant : {$asset->getBitlocker_id()}"), 0, 'L');
	$pdf->MultiCell(0, 10, __("Mots de passe : {$asset->getBitlocker_password()}"), 0, 'L');
}

$pdf->Ln(10);
$pdf->SetFont('Helvetica', 'b', 12);
$pdf->Cell(0, 10, "Type d'acquisition", 0, 1, 'L');

if ($asset->getType_achat() === "Leasing") {
	$description = "Leasing";
	if ($asset->getDuree_loc()) {
		$description .= " de {$asset->getDuree_loc()} ans";
	}
	if ($asset->getDate_achat()) {
		$description .= " datant du " . Functions::formatDate($asset->getDate_achat(), false, true) . ".";
	}
} else {
	$description = "Achat";
	if ($asset->getDate_achat()) {
		$description .= " datant du " . Functions::formatDate($asset->getDate_achat(), false, true) . ".";
	}
	if ($asset->getNum_immo()) {
		$description .= " Numéro d'immobilisation : {$asset->getNum_immo()}.";
	}
}

$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(0, 10, __($description), 0, 1, 'L');

$repairs = "SELECT * FROM p_reparations WHERE matos_id = {$asset->getId()} ORDER BY date DESC";
$repairs = connectPdo()->query($repairs)->fetchAll(PDO::FETCH_ASSOC);

if ($repairs) {
	$pdf->AddPage();
	$pdf->AddFont('RobotoMono', '', 'RobotoMono-Regular.php');
    if (exists("-pdf-header")) {
        $pdf->Image(ROOT . "/css/images/". COMPANY_NAME ."-pdf-header.png", $pdf->GetX() - 12, $pdf->GetY() - 10, 0, 0, 'PNG');
    }
	$pdf->SetFont('Arial', '', 12);
	$pdf->SetXY($pdf->GetX() + 165, $pdf->GetY() + 2);
	$pdf->Write(0, date("d/m/Y"));
    if (exists("-pdf-footer")) {
        $pdf->Image(ROOT . "/css/images/" . COMPANY_NAME . "-pdf-footer.png", $pdf->GetY() - 7, 280, 0, 0, 'PNG');
    }
	$pdf->SetFont('Arial', 'b', 30);
	$pdf->Ln(30);
	$pdf->Write(0, __($asset->getAsset()), DOMAIN . "?mod=asset&id={$asset->getId()}");
	if ($asset->getSn()) {
		$pdf->Write(0, " / ");
		$pdf->SetFont('Arial', '', 16);
		$pdf->SetXY($pdf->GetX(), $pdf->GetY() + 1);
		$pdf->Write(0, __($asset->getSn()));
	}

	$pdf->Ln(10);

	$pdf->SetFont('Helvetica', 'b', 14);
	$pdf->Cell(0, 10, __("Historique des réparations"), 0, 1, 'L');
	$pdf->Ln(5);
	foreach ($repairs as $repair) {
		if ($repair['num']) {
			$pdf->SetFont('Helvetica', 'b', 12);
			$pdf->Cell(0, 7, __($repair['num']), 0, 1, 'L');
		} else {
			$pdf->SetFont('Helvetica', 'b', 12);
			if ($repair['type'] === "I") {
				$pdf->Cell(0, 7, __("Réparation réalisée en interne"), 0, 1, 'L');
			} else {
				$pdf->Cell(0, 7, __("Réparation externe - aucun numéro renseigné"), 0, 1, 'L');
			}
		}
		$pdf->SetFont('Helvetica', '', 12);
		$isFinished = $repair['is_finished'] === 1 ? __("Terminée") : "En cours";
		if ($repair['date']) {
			$pdf->Cell(0, 7, __("Date d'intervention : " . Functions::formatDate($repair['date'], true, true)) . " ($isFinished).", 0, 1, 'L');
		} else {
			$pdf->Cell(0, 7, __("Aucune date n'a été renseignée.")  . " ($isFinished)", 0, 1, 'L');
		}
		if ($repair['remarque']) {
			$pdf->MultiCell(0, 6, __($repair['remarque']), 0);
		}
		$pdf->Ln(4);
	}
}

$pdf->Output('I', 'fiche-identification.pdf');
