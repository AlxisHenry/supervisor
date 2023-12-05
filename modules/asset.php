<?php
$asset = P_matos::create($_GET['id'] ?? 0);
if (!$asset->getId()) $asset = new P_matos();
$location = DB::findValueInTable("p_localisation", "nom", "id", $asset->getLocalisation());
$user = DB::findValueInTable("utilisateurs", "nom,prenom", "id", $asset->getUser());
$assets = P_matos::getLinkedAssets($asset);
$isNew = $asset->getId() ? false : true;
?>

<header id="assetHeader">
	<div class="important">
		<div class="left">
			<?php if (!$isNew && $asset->getStatut_ad()) { ?>
				<div class="asset-status" data-id="<?= $asset->getId() ?>">
					<div title="" class='status-circle status-pending'></div>
				</div>
			<?php } ?>
			<div class="title">
				<?php if (!$isNew) { ?>
					<?= $asset->getAsset() ?>
					<span class='asset-location'><?= $location !== "Utilisateur" ? " - $location" : "" ?></span>
					<?= " - $user" ?>
				<?php } else { ?>
					Création d'un nouvel ordinateur
				<?php } ?>
			</div>
		</div>
		<div class="right">
			<?php if (!$isNew) { ?>
				<a href="?mod=asset&id=<?= $assets["previous"]["id"]; ?>" title="Redirection vers <?= $assets["previous"]["asset"]; ?>">
					<i class="fa-solid change-asset fa-arrow-left"></i>
				</a>
			<?php } ?>
			<div class="form-element-extended">
				<input class="form-input" id="assets" placeholder="Chercher un ordinateur">
			</div>
			<?php if (!$isNew) { ?>
				<a href="?mod=asset&id=<?= $assets["next"]["id"]; ?>" title="Redirection vers <?= $assets["next"]["asset"]; ?>">
					<i class="fa-solid change-asset fa-arrow-right"></i>
				</a>
			<?php } ?>
		</div>
	</div>

	<div class="actions">
		<div class="actions">
			<?php if ($asset->getStatut_ad()) { ?>
				<button class="form-button ldap" id="update-from-ad" data-id="<?= $asset->getId(); ?>" title="<?= $asset->lastUpdate() ?>">Mettre à jour depuis AD</button>
				<button class="form-button wmi" id="update-from-wmi" data-id="<?= $asset->getId(); ?>" title="<?= $asset->lastUpdate(wmi: true) ?>">Mettre à jour depuis WMI</button>
				<div class="form-element wmi">
					<a href="?mod=softwares&id=<?= $asset->getId() ?>" class="form-button loading">
						Applications installées
					</a>
				</div>
			<?php } ?>
			<?php if (!$isNew) { ?>
				<div class="form-element">
					<a target="_blank" href="/tools/pdf.php?id=<?= $asset->getId() ?>" class="form-button">
						Fiche d'identification
					</a>
				</div>
			<?php } ?>
			<div class="form-element">
				<button type="submit" class="form-button" id="submit" data-id="<?= $asset->getId(); ?>">
					<?php if (!$isNew) { ?>
						Confirmer les changements
					<?php } else { ?>
						Créer l'ordinateur
					<?php } ?>
				</button>
			</div>
		</div>
	</div>
</header>

<div class="container assetForm">
	<?= Form::computer($asset); ?>
	<?php if (!$isNew) { ?>
		<div class="locations" data-asset-id="<?= $asset->getId(); ?>">
			<?= Functions::generateLocationsHTMLElements($asset->getLocalisation()); ?>
		</div>
	<?php } ?>
</div>

<?php if (!$isNew) { ?>
	<div id="rep_mvt" class="onglets">
		<ul>
			<li><a href='#tab_mvt'>Mouvements</a></li>
			<li><a href='#tab_rep'>Réparations</a></li>
		</ul>
		<div id='tab_mvt'>
			<div class="movements-list">
				<?= Table::movements($asset); ?>
			</div>
		</div>
		<div id='tab_rep'>
			<div class="form-element">
				<button class="form-button repair-dialog-opener" data-from-asset-page="true" data-action='new' data-asset-id="<?= $asset->getId(); ?>" data-asset="<?= $asset->getAsset(); ?>">Nouvelle répération</button>
			</div>
			<div class='repairs-list'>
				<?= Table::repairs(asset: $asset); ?>
			</div>
		</div>
	</div>
<?php } ?>
