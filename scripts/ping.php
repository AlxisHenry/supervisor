<?php

$asset = $_GET['asset'] ?? null;

if (!$asset) {
	throw new Exception("Asset manquant en paramètre de requête get");
}

$id = DB::findValueInTable("p_matos", "id", "asset", $asset);

if (!$id) {
	throw new Exception("Asset introuvable");
}

?>

<div class="alert alert-success" role="alert">
	<h4 class="alert-heading">Récupération de l'IP de <?= $asset ?></h4>
	<h5>L'adresse récupérée est la suivante : <?= Wmi::getIp($id) ?></h5>
</div>