<?php

$file = fopen(ROOT . "tools/purchase-details.csv", "r");

if (!$file) {
	throw new Exception("Impossible d'ouvrir le fichier");
}

$assets = [];

while (($data = fgetcsv($file, 0, ";")) !== false) {
	$assets[] = $data;
}

fclose($file);

?>
<div style="margin-left: 20px; margin-right: 20px;">
	<ul>
		<?php

		foreach ($assets as $asset) {
			$name = $asset[0];
			$date = $asset[1];
			$immobilisation = $asset[2];
			if ($name && $date && $immobilisation) {
				$typeAchat = "Achat";
				$duration = "0";
				if (in_array($immobilisation, ["pas SAP", "LEASING", "transfert ROM", "LEASING "])) {
					if (in_array($immobilisation, ["LEASING", "LEASING "])) {
						$typeAchat = "Leasing";
						$duration = "3";
					} else {
						$typeAchat = "Achat";
					}
					$immobilisation = null;
				}
				$d = explode("/", $asset[1]);
				$purchaseDate = "20{$d[2]}-{$d[1]}-{$d[0]}";
		?>
				<li>
					<?= $name ?> - <?= $purchaseDate ?> - <?= $immobilisation ?> - <?= $typeAchat ?> - <?= $duration ?>
				</li>
		<?php
				$query = "UPDATE p_matos SET date_achat = '$purchaseDate', num_immo = '$immobilisation', type_achat = '$typeAchat', duree_loc = '$duration' WHERE asset = '$name'";
				connectPdo()->query($query);
			}
		}

		?>
	</ul>
</div>