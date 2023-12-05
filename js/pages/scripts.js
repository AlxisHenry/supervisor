$(document).ready(() => {
	/**
	 * Propose la liste de tous les assets dans l'input Assets.
	 * On peut sélectionner un asset existant, ce qui redirige vers la page des applications de cet asset.
	 */
	$("#assets").autocomplete({
		source: './tools/ajax/autocomplete_assets.php',
		select: function (event, ui) {
			Swal.fire({
				title: `Chargement des applications...`,
				text: `Veuillez patienter pendant la récupération des applications...`,
				showConfirmButton: false,
				allowOutsideClick: false,
				allowEscapeKey: false,
				allowEnterKey: false,
				didOpen: () => {
					Swal.showLoading()
				}
			});
			window.location.href = '?mod=scripts&script=retrieve-asset-softwares&asset=' + ui.item.label;
		},
		delay: 0,
		minLength: 2
	});

	$("#scripts-assets").autocomplete({
		source: './tools/ajax/autocomplete_assets.php',
		select: function (event, ui) {
			$('#selected-asset').html(ui.item.label);
			$('.script-link').each(function () {
				if ($(this).html().indexOf('softwares') > -1 && $(this).attr('href').indexOf('asset') > -1) {
					$(this).addClass('loading');
				} else {
					$(this).removeClass('loading');
				}
				loadLoadingEvent();
				$(this).attr('href', $(this).attr('href') + '&asset=' + ui.item.label);
			});
		},
		delay: 0,
		minLength: 2
	})
})