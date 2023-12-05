<div class="filter scroll-follow">
	<input class='form-input' id='filter_type' type='hidden' value='filters_repairs'>
	<div class="form">
		<div class="form-group form-group-inline">
			<div class="form-element form-element-extended">
				<label class="form-label" for="search">Rechercher une réparation</label>
				<input name="search" id="search" name="search" autofocus onfocus="this.setSelectionRange(this.value.length,this.value.length);" value="<?= Filter::find("filters_repairs", "search"); ?>" class="form-input filter-search" type='text' placeholder='Rechercher une réparation (par n° de réparation, n° de série, remarque)' />
			</div>
		</div>
		<div class="form-section">
			<div class="form-element">
				<label class="form-label" for="is_finished">Statut</label>
				<select name="is_finished" class='form-select filter-select' id="is_finished">
					<option value='all'>Tous</option>
					<?= Select::repairsStatuses(key: "filters_repairs"); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="typeIntervention">Type d'inter.</label>
				<select name="typeIntervention" class='form-select filter-select' id="typeIntervention">
					<option value='all'>Tous</option>
					<?= Select::interventionTypes(key: "filters_repairs"); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="marque">Marque</label>
				<select name="marque" class='form-select filter-select' id="marque">
					<option value='all'>Tous</option>
					<?= Select::manufacturers(key: "filters_repairs"); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="modele">Modèle</label>
				<select name="modele" class='form-select filter-select' id="modele">
					<option value='all'>Tous</option>
					<?= Select::models(key: "filters_repairs"); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="type">Type de matériel</label>
				<select name="type" class='form-select filter-select' id="type">
					<option value='all'>Tous</option>
					<?= Select::types(key: "filters_repairs"); ?>
				</select>
			</div>
			<div class="form-element">
				<label class="form-label" for="localisation">Localisation</label>
				<select name="localisation" class='form-select filter-select' id="localisation">
					<option value='all'>Tous</option>
					<?= Select::locations(key: "filters_repairs"); ?>
				</select>
			</div>
			<div class="form-element">
				<label for="date-start" class="form-label">
					<span class="tooltip" data-tooltip="Date de début de la réparation">Date de début</span>
				</label>
				<input name="date-start" class="form-date filter-date" type="date" id="date-start">
			</div>
			<div class="form-element">
				<label for="date-end" class="form-label">Date de fin</label>
				<input name="date-end" class="form-date filter-date" type="date" id="date-end">
			</div>
		</div>
		<div class="form-group form-action">
			<div class="form-action-uo">
				<div class="form-element">
					<button class="form-button change-input-value" data-value="1" data-input="marque">Voir les HP</button>
				</div>
				<div class="form-element">
					<button class="form-button change-input-value" data-value="2" data-input="marque">Voir les Lenovo</button>
				</div>
				<div class="form-element">
					<button class="form-button change-input-value" data-value="I" data-input="typeIntervention">Réparations internes</button>
				</div>
				<div class="form-element">
					<button class="form-button change-input-value" data-value="E" data-input="typeIntervention">Réparations externes</button>
				</div>
				<div class="form-element">
					<button class="form-button change-dates-values" data-start="<?= date("Y-01-01"); ?>" data-end="<?= date("Y-12-31"); ?>">Pour l'année <?= date("Y"); ?></button>
				</div>
			</div>
			<div class="form-action-uo">
				<div class="form-element">
					<a href="./tools/exports/repairs.php">
						<button class="form-button" title="Exporter la liste en appliquant les filtres actuels" id="export">Exporter la liste</button>
					</a>
				</div>
				<div class="form-element">
					<button class="form-button" title="Réinitialiser les filtres"  id="reload">Réinitialiser les filtres</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id='filters_repairs'>
	<?= Table::repairs(); ?>
</div>