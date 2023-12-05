$(document).ready(function () {

	// On demande à l'utilisateur de saisir un asset départ
	Swal.fire({
		title: 'Export type fichier des assets',
		html: '<input id="swal-input1" class="swal2-input show-assets" placeholder="Asset de départ">' +
			'<input id="swal-input2" class="swal2-input show-assets-after" disabled="true" placeholder="Asset de fin">' +
			'<br><br><input type="checkbox" id="only_archived" class="swal2-checkbox"><label style="margin-left: -20px;" for="only_archived" class="swal2-checkbox-label">Cocher pour exporter uniquement les assets cédés ou en de fin leasing.</label>',
		focusConfirm: false,
		didOpen: () => {
			$(".show-assets").autocomplete({
				source: './tools/ajax/autocomplete_assets.php',
				select: function (event, ui) {
					$('#swal-input2').prop('disabled', false);
					$(".show-assets-after").autocomplete({
						source: './tools/ajax/autocomplete_assets.php',
						data: {
							asset: $('#swal-input1').val()
						},
						select: function (event, ui) {

						}
					})
				},
				delay: 0,
				minLength: 2
			});
		},
		preConfirm: () => {
			return [
				$('#swal-input1').val()
			]
		},
		showCancelButton: true,
		confirmButtonText: 'Exporter',
		confirmButtonColor: SweetAlertConfig.colors.base,
		cancelButtonText: 'Annuler',
		cancelButtonColor: SweetAlertConfig.colors.danger,
		allowOutsideClick: false,
		allowEscapeKey: false,
		allowEnterKey: false,
		inputValidator: (value) => {
			if (!value) {
				return 'Vous devez saisir un asset de départ'
			}
		}

	}).then((result) => {
		if (result.isConfirmed) {
			const isChecked = document.getElementById('only_archived').checked ? 1 : 0;
			const from = result.value[0];
			const to = $('#swal-input2').val();
			location.href = `../../tools/exports/for-assets-file.php`;
			localStorage.setItem("toast", JSON.stringify({
				...Alert.success,
				title: 'Fichier exporté',
				showConfirmButton: false,
			}));
			Swal.fire({
				title: `Export en cours...`,
				text: `Veuillez patienter pendant l'export du fichier des assets...`,
				showConfirmButton: false,
				allowOutsideClick: false,
				allowEscapeKey: false,
				allowEnterKey: false,
				didOpen: () => {
					Swal.showLoading();
					setTimeout(() => {
						location.href = '/';
					}, 800);
				},
			})

		}
	})

})
