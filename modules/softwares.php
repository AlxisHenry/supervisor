<?php
set_time_limit(0);
$asset = P_matos::create((int) $_GET['id']);
$wmi = new Wmi($asset);
$location = DB::findValueInTable("p_localisation", "nom", "id", $asset->getLocalisation());
$user = DB::findValueInTable("utilisateurs", "nom,prenom", "id", $asset->getUser());
$assets = P_matos::getLinkedAssets($asset);
?>

<header id="assetHeader">
	<div class="important">
		<div class="left">
			<div class="asset-status" data-id="<?= $asset->getId() ?>">
				<div title="" class='status-circle status-pending'></div>
			</div>
			<div class="title">
				<?= $asset->getAsset() ?>
				<span class='asset-location'><?= $location !== "Utilisateur" ? " - $location" : "" ?></span>
				<?= " - $user" ?>
			</div>
		</div>
		<div class="right">
			<a class="loading" href="?mod=softwares&id=<?= $assets["previous"]["id"]; ?>" title="Voir les applications installées sur <?= $assets["previous"]["asset"]; ?>">
				<i class="fa-solid change-asset fa-arrow-left"></i>
			</a>
			<div class="form-element-extended">
				<input class="form-input" id="assets" placeholder="Chercher un ordinateur">
			</div>
			<a class="loading" href="?mod=softwares&id=<?= $assets["next"]["id"]; ?>" title="Voir les applications installées sur <?= $assets["next"]["asset"]; ?>">
				<i class="fa-solid change-asset fa-arrow-right"></i>
			</a>
		</div>
	</div>
	<div class="actions">
		<div class="actions">
			<div class="form-element">
				<a href="?mod=asset&id=<?= $asset->getId() ?>" class="form-button">
					Page de l'asset
				</a>
			</div>
			<div class="form-element">
				<a target="_blank" href="/tools/pdf.php?id=<?= $asset->getId() ?>" class="form-button">
					Fiche d'identification
				</a>
			</div>
		</div>
	</div>
</header>

<table class='table table-striped table-bordered table-hover'>
	<thead>
		<tr>
			<th>Nom de l'application</th>
			<th>Version</th>
			<th>Langue</th>
			<th>Emplacement</th>
			<th>Vendeur</th>
			<th>Date d'installation</th>
		</tr>
	</thead>
	<?php if ($wmi->isReachable() && $wmi->start()) {
		$softwares = $wmi->getSoftwares();
		foreach ($softwares as $software) { ?>
			<tr>
				<td><?= $software->getName() ?></td>
				<td><?= $software->getVersion() ?></td>
				<td style="text-align: center;"><?= $software->getLanguage() ?></td>
				<td><?= $software->getInstallLocation() ?></td>
				<td><?= $software->getVendor() ?></td>
				<td style="text-align: center;"><?= $software->getInstallDate() ?></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr>
			<td colspan='6'>L'ordinateur n'est pas joignable, ou les droits d'accès sont insuffisants.</td>
		</tr>
	<?php } ?>
</table>
