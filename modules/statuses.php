<div class="filter scroll-follow">
	<input class='form-input' id='filter_type' type='hidden' value='filters_statuses'>
	<div class="form">
		<div class="form-group form-group-inline">
			<div class="form-element form-element-extended">
				<label class="form-label" for="search">Rechercher un statut</label>
				<input autofocus id="search" name="search" onfocus="this.setSelectionRange(this.value.length,this.value.length);" value="<?= Filter::find("filters_statuses", "search"); ?>" class="form-input filter-search" type='text' placeholder='Rechercher un statut (par intitulé)' />
			</div>
		</div>
		<div class="form-group form-action">
			<div class="form-action-uo">
				<div class="form-element">
					<button class="form-button" title="Créer un nouveau statut" id="new-status">Créer un statut</button>
				</div>
				<div class="form-element">
					<a href="./tools/exports/statuses.php">
						<button class="form-button" title="Exporter la liste en appliquant les filtres actuels" id="export">Exporter la liste</button>
					</a>
				</div>
				<div class="form-element">
					<button class="form-button" title="Réinitialiser les filtres" id="reload">Réinitialiser les filtres</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id='filters_statuses'>
	<?= Table::statuses() ?>
</div>