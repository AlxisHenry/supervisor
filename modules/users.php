<div class="filter scroll-follow">
	<input class='form-input' id='filter_type' type='hidden' value='filters_users'>
	<div class="form">
		<div class="form-group form-group-inline">
			<div class="form-element form-element-extended">
				<label class="form-label" for="search">Rechercher un utilisateur</label>
				<input autofocus onfocus="this.setSelectionRange(this.value.length,this.value.length);" value="<?= Filter::find("filters_users", "search"); ?>" class="form-input filter-search" id="search" name="search" type='text' placeholder='Rechercher un utilisateur (par nom, prénom)' />
			</div>
		</div>
		<div class="form-group form-action">
			<div class="form-action-uo">
				<div class="form-element">
					<button class="form-button" title="Créer un nouvel utilisateur" id="new-user">Créer un utilisateur</button>
				</div>
				<div class="form-element">
					<a href="./tools/exports/users.php">
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

<div id='filters_users'>
	<?= Table::users(); ?>
</div>