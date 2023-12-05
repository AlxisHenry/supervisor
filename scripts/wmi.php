<?php

$asset = $_GET['asset'] ?? "";

if (!$asset) {
	throw new Exception("Aucun asset en paramètre de requête GET");
}

$asset = P_matos::create((int) DB::findValueInTable("p_matos", "id", "asset", $asset));

if (!$asset->getId()) {
	throw new Exception("Asset introuvable");
}

$update = Wmi::updateComputer($asset, $status, update: false);

if (!$update) {
	throw new Exception("Impossible de mettre à jour l'asset");
} else { ?>
	<div class="alert alert-success" role="alert">
		<h4 class="alert-heading">Requêtes WMI réalisées avec succès</h4>
		<p>Le nom de l'asset est <code><?= $asset->getAsset() ?></code></p>
		<hr>
		<ul>
			<li class="mb-0">L'asset est un <code><?= $update['wmi_informations']['model'] ?></code></li>
			<li class="mb-0">Le numéro de série de l'asset est <code><?= $update['wmi_informations']['serial_number'] ?></code></li>
			<li class="mb-0">La version du bios de l'asset est <code><?= $update['wmi_informations']['bios'] ?></code></li>
		</ul>
		<div class="see-more">
			<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
				Voir toutes les informations
			</button>
		</div>
		<div class="collapse" id="collapse">
			<div class="card card-body">
				<pre><?php print_r($update['computer']) ?></pre>
				<pre><?php print_r($update['wmi_informations']) ?></pre>
			</div>
		</div>
<?php } ?>