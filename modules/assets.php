<div class="filter scroll-follow">
	<input class='form-input' id='filter_type' type='hidden' value='filters_assets'>
	<div class="form">
		<div class="form-group form-group-inline">
			<div class="form-element form-element-extended">
				<label class="form-label" for="search">Rechercher un matériel</label>
				<input autofocus onfocus="this.setSelectionRange(this.value.length,this.value.length);" value="<?= Filter::find("filters_assets", "search"); ?>" class="form-input filter-search" type='text' id="search" name="search" placeholder='Rechercher un matériel (par nom, remarque, attribution, numéro de série)' />
			</div>
		</div>
		<div class="form-section">
			<div class="form-element">
				<label class="form-label" for="statut">Statut</label>
				<select class='form-select filter-select' id="statut">
					<option value='all'>Tous</option>
					<?= Select::statuses(archive: "without"); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="marque">Marque</label>
				<select class='form-select filter-select' id="marque">
					<option value='all'>Tous</option>
					<?= Select::manufacturers(); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="modele">Modèle</label>
				<select class='form-select filter-select' id="modele">
					<option value='all'>Tous</option>
					<?= Select::models(); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="os">Sys. d'exploit.</label>
				<select class='form-select filter-select' id="os">
					<option value='all'>Tous</option>
					<?= Select::os(versions: false); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="os_version">Version de l'OS</label>
				<select class='form-select filter-select' id="os_version">
					<option value='all'>Tous</option>
					<?= Select::os(versions: true); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="type">Type de matériel</label>
				<select class='form-select filter-select' id="type">
					<option value='all'>Tous</option>
					<?= Select::types(); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="localisation">Localisation</label>
				<select class='form-select filter-select' id="localisation">
					<option value='all'>Tous</option>
					<?= Select::locations(); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="statut_ad">Statut AD</label>
				<select class='form-select filter-select' id="statut_ad">
					<option value='all'>Tous</option>
					<?= Select::activeDirectoryStatuses(); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="clavier">Langue du clavier</label>
				<select class='form-select filter-select' id="clavier">
					<option value='all'>Toutes</option>
					<?= Select::keyboardTypes(); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="langue">Langue du système</label>
				<select class='form-select filter-select' id="langue">
					<option value='all'>Toutes</option>
					<?= Select::systemLanguages(); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="type_achat">Type d'acquisition</label>
				<select class='form-select filter-select' id="type_achat">
					<option value='all'>Tous</option>
					<?= Select::acquisitionTypes(); ?>
				</select>
			</div>
		</div>
		<div class="form-group form-action">
			<div class="form-action-uo">
				<div class="form-element">
					<button class="form-button change-input-value" title="Voir uniquement les desktops" data-value="1" data-input="type">Voir les desktops</button>
				</div>
				<div class="form-element">
					<button class="form-button change-input-value" title="Voir uniquement les laptops" data-value="2" data-input="type">Voir les laptops</button>
				</div>
				<div class="form-element">
					<button class="form-button change-input-value" title="Voir uniquement les serveurs" data-value="3" data-input="type">Voir les serveurs</button>
				</div>
				<div class="form-element">
					<button class="form-button change-input-value" title="Voir uniquement les tablettes" data-value="4" data-input="type">Voir les tablettes</button>
				</div>
			</div>
			<div class="form-action-uo">
				<div class="form-element">
					<a href="./tools/exports/assets.php">
						<button class="form-button" title="Exporter la liste en appliquant les filtres actuels" id="export">Exporter la liste</button>
					</a>
				</div>
				<div class="form-element">
					<button class="form-button" id="reload" title="Réinitialiser les filtres" >Réinitialiser les filtres</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id='filters_assets'>
	<?= Table::assets(); ?>
</div>