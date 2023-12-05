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
			window.location.href = '?mod=softwares&id=' + ui.item.id;
		},
		delay: 0,
		minLength: 2
	});
})