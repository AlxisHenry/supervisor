$(document).ready(function () {
	$(document).tooltip();
	$(".onglets").tabs();
	$("#menu").accordion({
		collapsible: false,
		active: getActiveTab(),
	});

	showToasts();
	selectedFilters();
	loadEvents();
	highlightSearch();
	pingAsset();
	loadLoadingEvent();
	
	/**
	 * Cet évènement permet de supprimer les filtres et de recharger la page lors du clique sur le bouton de rechargement
	 */
	$('#reload').click(() => {
		let filter = $("#filter_type").val();
		document.cookie = `${filter}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/`;
		location.reload();
	});

	/**
	 * Cet évènement permet de mettre à jour les filtres lors de cliques sur les boutons de filtres
	 */
	$('.change-input-value').click((e) => {
		let action = {
			input: $(e.target).data('input'),
			value: $(e.target).data('value')
		}
		$(`#${action.input}`).val(action.value).change();
	})

	/**
	 * Cet évènement permet de mettre à jour les filtres lors de changement de valeur dans la barrre de recherche
	 */
	$(".filter-search").keyup(() => {
		let filter = $('#filter_type').val();
		if (filtersQuery()) {
			// Si des filtres sont sélectionnés on effectue une recherche avec les filtres et la recherche
			$.ajax({
				type: "POST",
				url: "./tools/ajax/filters.php",
				data: filtersQuery(),
				cache: false,
				success: function (html) {
					// On cache la liste des matériels et on affiche les résultats de la recherche
					$(`#${filter}`).html(html);
					highlightSearch();
					loadEvents();
				}
			});
		}
	})

	/**
	 * Cet évènement permet de mettre à jour les filtres lors de changement de valeur dans les select
	 */
	$(".filter-select, .filter-date").change(() => {
		selectedFilters();
		let filter = $('#filter_type').val();
		$.ajax({
			type: "POST",
			url: "./tools/ajax/filters.php",
			data: filtersQuery(),
			cache: false,
			success: function (html) {
				$(`#${filter}`).html(html);
				highlightSearch();
				loadEvents();
			}
		});
	});

});