<div class="filter scroll-follow">
	<input class='form-input' id='filter_type' type='hidden' value='filters_models'>
	<div class="form">
		<div class="form-group form-group-inline">
			<div class="form-element form-element-extended">
				<label class="form-label" for="search">Rechercher un modèle</label>
				<input autofocus id="search" name="search" onfocus="this.setSelectionRange(this.value.length,this.value.length);" value="<?= Filter::find("filters_models", "search"); ?>" class="form-input filter-search" type='text' placeholder='Rechercher un modèle (par intitulé)' />
			</div>
		</div>
		<div class="form-group form-action">
			<div class="form-action-uo">
				<div class="form-element">
					<a href="./tools/exports/models.php">
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

<div id='filters_models'>
	<?= Table::models() ?>
</div>