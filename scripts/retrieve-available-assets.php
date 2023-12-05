<?php

$limit = 2;
$statuses = [
	"online" => [],
	"offline" => []
];

if (isset($_GET["_limit"])) {
	$limit = $_GET["_limit"];
}

$query = "SELECT id FROM p_matos LIMIT $limit";

$ids = connectPdo()->query($query)->fetchAll(PDO::FETCH_ASSOC);

foreach ($ids as $id) {
	$id = $id["id"];
	$asset = P_matos::create($id);
	if (Wmi::isAvailable($id)) {
		$statuses["online"][] = $asset->getAsset();
	} else {
		$statuses["offline"][] = $asset->getAsset();
	}
}

?>

<div style="margin-left: 20px; margin-right: 20px;">
	<h1>Assets disponibles</h1>

	<span style="font-size: 16px">Vous pouvez limiter le nombre d'assets à traiter en ajoutant le paramètre <code>_limit</code> à l'URL (2 par défaut)</span>

	<h2>En ligne</h2>
	<ul>
		<?php if (count($statuses["online"]) === 0) : ?>
			<li>Aucun asset disponible</li>
		<?php endif; ?>
		<?php foreach ($statuses["online"] as $asset) : ?>
			<li><?= $asset ?></li>
		<?php endforeach; ?>
	</ul>

	<h2>Hors ligne</h2>
	<ul>
		<?php if (count($statuses["offline"]) === 0) : ?>
			<li>Aucun asset hors ligne</li>
		<?php endif; ?>
		<?php foreach ($statuses["offline"] as $asset) : ?>
			<li><?= $asset ?></li>
		<?php endforeach; ?>
	</ul>

</div>