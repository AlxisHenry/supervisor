/**
 * Cette fonction permet de récupérer les filtres sélectionnés et de les transformer en query string
 * - Elle retourne la query string ou false si aucun filtre n'est sélectionné
 * - Elle stocke également les filtres dans un cookie qui permet de les conserver lors d'un rechargement de la page
 * 
 * @returns {string|boolean} Retourne la query string ou false si aucun filtre n'est sélectionné
 */
function filtersQuery() {
	let params = [];
	// On récupère tous les filtres sélectionnés et on les stocke dans un tableau ("key=value")
	$(".filter-select option:selected").each(function () {
		if ($(this).val() != 'all') {
			params.push([
				$(this).parent().attr('id'),
				$(this).val() // On remplace les espaces par des "+" pour éviter les problèmes d'encodage
					.replaceAll(' ', '+')
			].join('='));
		}
	});
	// On récupère les filtres de date et on les ajoute au tableau
	const dates = {
		start: $("#date-start").val() ?? null,
		end: $("#date-end").val() ?? null
	}
	if (dates.start) params.push('date-start=' + dates.start);
	if (dates.end) params.push('date-end=' + dates.end);
	// On récupère la recherche et on l'ajoute au tableau
	let search = $(".filter-search").val();
	params.push('search=' + search);
	let filter = $("#filter_type").val();
	let query = params.join('&'); // On transforme le tableau en query string ("key=value&...")
	if (filter) {
		document.cookie = `${filter}=${query};path=/`; // On stocke les filtres dans un cookie qui permet de les conserver lors d'un rechargement de la page
		query += `&filter=${filter}`;
	}
	return !query ? false : query; // On retourne la query string ou false si aucun filtre n'est sélectionné
}

/**
 * Cette fonction permet de mettre en surbrillance le mot recherché dans la liste des matériels
 * 
 * @param {boolean} load Permet de savoir si la fonction est appelée lors du chargement de la page ou non
 * @returns {void}
 */
function highlightSearch() {
	let noResult = '<td colspan="7">Aucun résultat n\'a été trouvé.</td>';
	let search = $('.filter-search').val();
	let filter = $('#filter_type').val();
	let items = $(`#${filter} tr td:not(.actions)`);
	if (!items || !search || !filter) return;
	if (search.length > 2 && items.html().indexOf(noResult) == -1) {
		items.each(function () {
			$(this).html($(this).html().replace(new RegExp(search, 'ig'), function (matched) {
				return "<span class='highlight'>" + matched + "</span>";
			}));
		});
	}
}

/**
 * Cette fonction gère l'ajout/suppression de la classe is-selected sur les selects
 * 
 * @returns {void}
 */
function selectedFilters() {
	$(".filter-select, .filter-date").each(function () {
		if ($(this).val() !== 'all' && $(this).val() !== '') {
			$(this).addClass('is-selected');
		} else {
			$(this).removeClass('is-selected');
		}
	});
}

/**
 * Cette fonction permet de récupérer un paramètre dans l'URL
 * 
 * @param {string} name 
 * @returns 
 */
function getFromURL(name) {
	return new URLSearchParams(window.location.search).get(name);
}

/**
 * Cette fonction permet de vérifier si une chaîne de caractères est un JSON valide
 * 
 * @param {string} str 
 * @returns 
 */
function isJSON(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

/**
 * Cette fonction permet de récupérer l'index de l'onglet actif dans le menu.
 * Toutes les divs de l'élément #menu doivent avoir la classe .menu-element, et l'onglet actif doit avoir l'attribut data-active à true.
 * 
 * @returns {number|boolean} Retourne l'index de l'onglet actif ou false si aucun onglet n'est actif
 */
function getActiveTab() {
	let index = false;
	const elements = $("#menu .menu-element");
	for (const element of elements) {
		if ($(element).data('active')) {
			index = elements.index(element);
		}
	}
	return index;
}

/**
 * Cette fonction permet de chgerche si un a toast a été sauvegardé dans le localStorage et de l'afficher si c'est le cas.
 */
function showToasts() {
	let toast = localStorage.getItem('toast');
	if (toast) {
		Toast.fire(JSON.parse(toast));
		localStorage.removeItem('toast');
	}
}

/**
 * Cette fonction permet de gérer la mise à jour des assets depuis WMI ou depuis AD en affichant une barre de progression.
 * 
 * @param {string} from 
 */
function updateWithProgress(from) {
	const isWmiUpdate = () => from === 'WMI';
	// On demande confirmation à l'utilisateur
	Swal.fire({
		...Alert.confirm,
		title: `Mise à jour depuis ${from}`,
		html: `Voulez-vous mettre à jour l'ensemble des assets depuis ${from} ? Cette action peut prendre plusieurs minutes.
			` + (isWmiUpdate() ? '' : `
			<input type="checkbox" id="ignore_already_existing_assets" class="swal2-checkbox">
			<label style="margin-left: -20px;" for="ignore_already_existing_assets" class="swal2-checkbox-label">Cocher pour ignorer les assets existants</label>
		`),
	}).then((result) => {
		if (result.isConfirmed) {
			const checkbox = document.getElementById('ignore_already_existing_assets');
			// S'il confirme, on affiche la popup permettant de suivre la progression de la mise à jour
			Swal.fire({
				icon: SweetAlertConfig.icons.warning,
				title: `Mise à jour depuis ${from}`,
				html: `
				Veuillez patienter, cette action peut prendre plusieurs minutes. Ne fermez pas cette fenêtre !
				<div class="progress">
					<div class="progress-bar">
						<div class="indicator"></div>
						<div class="count"><span class="done">0</span>/<span class="total">0</span></div>
					</div>
				</div>
				<div class="log"></div>
				`,
				showConfirmButton: false,
				allowOutsideClick: false,
				allowEscapeKey: false,
				allowEnterKey: false,
			});
			// Dans le cas où une limite est définie dans l'URL, on l'ajoute à la requête
			const limit = getFromURL('limit');
			let data = {};
			if (limit) data = {
				limit: limit
			}
			// On effectue la requête AJAX permettant de récupérer les assets en fonction de la source
			$.ajax({
				type: 'GET',
				url: `./tools/ajax/assets.php?for=${isWmiUpdate() ? 'wmi' : 'ad'}`,
				data: {
					...data,
					for: isWmiUpdate() ? 'wmi' : 'ad',
					ignore_already_existing_assets: !isWmiUpdate() && checkbox && checkbox.checked ? 1 : 0
				},
				success: function (data) {
					// On récupère les assets et on les stocke dans une variable
					const assets = JSON.parse(data);
					// On récupère le nombre d'assets total et on l'affiche dans la barre de progression
					const assetsCount = assets.length;
					if (assetsCount === 0) {
						Swal.fire({
							...Alert.error,
							title: `Mise à jour depuis ${from}`,
							text: 'Aucun asset n\'a été trouvé ! Vous allez être redirigé vers la page précédente.',
							timer: 1200,
							showConfirmButton: false,
						}).then(() => {
							window.history.back();
						})
					}
					$('.progress-bar .count .total').text(assetsCount);
					localStorage.setItem('update_table_content', "");
					assets.forEach((asset) => {
						let data = `from=${from.toLowerCase()}&global=true`;
						if (isWmiUpdate()) {
							data += `&id=${asset.id}`;
						} else {
							data += `&asset=${asset.asset}`;
						}
						$.ajax({
							type: 'POST',
							async: true,
							url: './tools/ajax/asset_update.php',
							data: data,
							success: function (data) {
								console.log(asset.asset, data);
								$('.progress-bar .count .done').text($(".log .log-item").length + 1);
								let percent = Math.round((parseInt($(".log .log-item").length) + 1) / assetsCount * 100);
								$('.progress-bar .indicator').css('width', `${percent}%`);
								let update = isJSON(data) ? JSON.parse(data) : {
									message: "Mise à jour avortée",
									executionTime: "N/A"
								}
								$('.log').prepend(`
										<div class="log-item">
											<div class="log-item-title">${asset.asset}</div>
											<div class="log-item-message">${update.message}</div>
											<div class="log-item-time">${update.executionTime}</div>
										</div>
									`);
								let table = localStorage.getItem('update_table_content') || "";
								table += `
									<tr>
										<td>${asset.asset}</td>
										<td>${update.message}</td>
										<td>${update.executionTime}</td>
									</tr>
								`;
								localStorage.setItem('update_table_content', table);
								if ($('.progress-bar .count .done').text() === $('.progress-bar .count .total').text()) {
									Toast.fire({
										...Alert.success,
										title: "Mise à jour terminée",
										showConfirmButton: false,
									});
									let table = localStorage.getItem('update_table_content');
									$('.update_list').css('display', 'table');
									$('.update_list tbody').html(table);
								}
							}
						});
					});
				}
			});
		} else {
			Swal.fire({
				...Alert.error,
				title: `Mise à jour annulée`,
				text: 'Aucune mise à jour n\'a été effectuée, vous allez être redirigé vers la page précédente.',
				timer: 1200,
				showConfirmButton: false,
			}).then(() => {
				window.history.back();
			})
		}
	})
}

/**
 * Cette fonction permet de charger les évènements sur les éléments de la page
 *
 * @returns {void}
 */
function loadEvents() {
	listEvents();
	repairsEvents();
	usersEvents();
	movementsEvents();
	assetsEvents();
	manufacturersEvents();
	typesEvents();
	statusesEvents();
	locationsEvents();
}

/**
 * @event click - Permet de rediriger vers la page de l'asset cliqué
 * @returns {void}
 */
function listEvents() {
	// Permet de rediriger vers la page de l'asset cliqué
	$(document).on('mousedown', '.list td.list-link', function (e) {
		let _blank = false;
		if (e.which === 2) _blank = true;
		let id = $(this).parent().attr('id');
		if (_blank) {
			window.open(`?mod=asset&id=${id}`, '_blank');
		} else {
			window.location.href = `?mod=asset&id=${id}`;
		}
	});
}

/**
 * @event click - Ouvre la boîte de dialogue permettant de créer ou d'éditer une réparation
 * @event click - Ouvre la popup permettant de confirmer la suppression d'une réparation
 * @returns {void}
 */
function repairsEvents() {
	/**
	 * Ouvre la fenêtre permettant de créer ou d'éditer une réparation
	 */
	$('.repair-dialog-opener').click((e) => {
		// On récupère les informations de l'asset
		const { action, asset, assetId, repairId, fromAssetPage } = $(e.target).data();
		const isEdit = () => action == 'edit';

		$.ajax({
			type: "POST",
			url: "./tools/ajax/jquery_dialogs.php",
			data: `dialog=repair&repair_id=${repairId}&asset_id=${assetId}`,
			cache: false,
			success: function (html) {
				$("body").append(html);
				$('.repair-dialog').dialog({
					title: !isEdit() ? `Nouvelle réparation pour ${asset}` : `Modification de la réparation #${repairId}`,
					autoOpen: true,
					height: 750,
					minHeight: 750,
					maxHeight: 750,
					minWidth: 800,
					maxWidth: 800,
					modal: true,
					buttons: {
						"Valider": () => {
							let data = [
								$(".repair-dialog form").serialize(), // On récupère les données du formulaire
								`asset_id=${assetId}`, // L'id de l'asset
								`action=${action}`, // L'action à effectuer (new ou edit)
								`fromAssetPage=${fromAssetPage}` // Permet de vérifier si on provient de la page de l'asset ou non (booléen)
							];
							if (isEdit()) data.push(`repair_id=${repairId}`);
							$.ajax({
								type: "POST",
								url: "./tools/ajax/repair.php",
								data: data.join('&') + `&action=${!isEdit() ? 'new' : 'edit'}`,
								cache: false,
								success: function (html) {
									$(".repair-dialog").remove();
									if (html) {
										localStorage.setItem("toast", JSON.stringify({
											...Alert.success,
											title: !isEdit() ? 'Réparation ajoutée !' : 'Réparation modifiée !',
											showConfirmButton: false,
										}));
										location.reload();
									}
								}
							});
						},
						"Annuler": () => {
							$(".repair-dialog").remove();
						}
					},
					close: function () {
						$(".repair-dialog").remove();
					}
				});
			}
		})

	});

	/**
	 * Ouvre la fenêtre permettant de suppimer une réparation
	 */
	$('.delete-repair').click((e) => {
		let repairId = $(e.target).data('repair-id');
		let assetId = $(e.target).data('asset-id');
		let fromAssetPage = $(e.target).data('from-asset-page') ?? false;
		Swal.fire(Alert.delete).then((result) => {
			if (result.isConfirmed) {
				let data = [
					`repair_id=${repairId}`,
					`asset_id=${assetId}`,
					`action=delete`,
					`fromAssetPage=${fromAssetPage.toString()}`
				];
				$.ajax({
					type: "POST",
					url: "./tools/ajax/repair.php",
					data: data.join('&'),
					cache: false,
					success: function (html) {
						localStorage.setItem("toast", JSON.stringify({
							...Alert.success,
							title: 'Réparation supprimée !',
							showConfirmButton: false,
						}));
						location.reload();
					}
				});
			}
		});
	});

	$('#reset-date').click((e) => {
		$('.repair-dialog input#date').val("");
	});
}

/**
 * @event click - Sur le bouton "Supprimer l'asset"
 * @return {void}
 */
function assetsEvents() {
	$(".delete-asset").click((e) => {
		Swal.fire({
			...Alert.delete,
			title: "Supprimer l'asset ?",
			text: "Cette action est irréversible !",
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					...Alert.delete,
					title: "Veuillez saisir le nom de l'asset pour confirmer la suppression",
					text: "Tout ce qui est lié à cet asset sera supprimé !",
					input: 'text',
					inputAttributes: {
						autocapitalize: 'off'
					},
					showCancelButton: true,
					confirmButtonText: 'Supprimer',
					showLoaderOnConfirm: true,
					preConfirm: (assetName) => {
						if (assetName !== $(e.target).data("asset-name")) {
							Swal.showValidationMessage(
								`Le nom de l'asset ne correspond pas !`
							)
						}
					},
					allowOutsideClick: () => !Swal.isLoading()
				}).then((result) => {
					if (result.isConfirmed) {
						$.ajax({
							type: "POST",
							url: "./tools/ajax/asset_delete.php",
							data: `id=${$(e.target).data("asset-id")}`,
							cache: false,
							success: function (html) {
								$("#filters_assets").html(html);
								loadEvents();
								Toast.fire({
									...Alert.success,
									title: 'Asset supprimé !',
									showConfirmButton: false,
								});
							}
						})
					}
				})
			}
		})
	});
}

/**
 * @method openUserDialog(edit: boolean, $el: HTMLElement) Ouvre la fenêtre permettant d'ajouter ou de modifier un utilisateur
 * @event click Sur le bouton "Ajouter un utilisateur"
 * @event click Sur le bouton "Modifier" d'un utilisateur
 * @event click Sur le bouton "Supprimer" d'un utilisateur
 */
function usersEvents() {

	const openUserDialog = (edit = false, $el = null) => {
		$.ajax({
			type: "POST",
			url: "./tools/ajax/jquery_dialogs.php",
			data: `dialog=user${edit ? `&user_id=${$el?.data("user-id") ?? null}` : ''}`,
			cache: false,
			success: function (html) {
				$("body").append(html);
				$(".user-dialog").dialog({
					title: edit ? `Edition d'un utilisateur` : "Nouvel utilisateur",
					autoOpen: true,
					height: 450,
					minHeight: 450,
					maxHeight: 450,
					minWidth: 500,
					maxWidth: 500,
					modal: true,
					buttons: {
						"Valider": () => {
							$.ajax({
								type: "POST",
								url: "./tools/ajax/user.php",
								data: $(".user-dialog form").serialize() + "&action=" + (edit ? "edit" : "new"),
								cache: false,
								success: function (html) {
									$(".user-dialog").remove();
									Toast.fire({
										...Alert.success,
										title: edit ? 'Utilisateur modifié !' : 'Utilisateur ajouté !',
										showConfirmButton: false,
									})
									$("#filters_users").html(html);
									loadEvents();
								}
							})
						},
						"Annuler": () => {
							$(".user-dialog").remove();
						}
					},
					close: () => {
						$(".user-dialog").remove();
					}
				});
			}
		})
	}

	$("#new-user").click(() => {
		openUserDialog();
	});

	$(".edit-user").click((e) => {
		openUserDialog(true, $(e.target));
	});

	$(".delete-user").click((e) => {
		Swal.fire(Alert.delete)
			.then((result) => {
				if (result.isConfirmed) {
					const fullName = $(e.target).data("user-full")
					Swal.fire({
						...Alert.delete,
						title: "Supprimer l'utilisateur ?",
						text: `Pour confirmer, saisissez le nom de l'utilisateur.`,
						input: 'text',
						inputAttributes: {
							autocapitalize: 'off',
							placeholder: fullName
						},
						showCancelButton: true,
						confirmButtonText: 'Supprimer',
						showLoaderOnConfirm: true,
						preConfirm: (user) => {
							if (user !== fullName) {
								Swal.showValidationMessage(
									`Le nom de l'utilisateur ne correspond pas !`
								)
							}
						},
						allowOutsideClick: () => !Swal.isLoading()
					}).then((result) => {
						if (result.isConfirmed) {
							$.ajax({
								type: "POST",
								url: "./tools/ajax/user.php",
								data: `action=delete&id=${$(e.target).data("user-id")}`,
								cache: false,
								success: function (html) {
									if (!html) {
										Swal.fire({
											...Alert.error,
											title: 'Erreur !',
											text: "Une erreur est survenue lors de la suppression de l'utilisateur.",
										})
									} else {
										Toast.fire({
											...Alert.success,
											title: 'Utilisateur supprimé !',
											showConfirmButton: false,
										})
										$("#filters_users").html(html);
										loadEvents();
									}
								}
							})
						}
					})
				}
			});
	});
}

/**
 * @event click Sur le bouton "Editer" d'un mouvement
 * @event click Sur le bouton "Supprimer" d'un mouvement
 */
function movementsEvents() {
	$(".edit-movement").click((e) => {
		$.ajax({
			type: "POST",
			url: "./tools/ajax/jquery_dialogs.php",
			data: `dialog=movement&movement_id=${$(e.target).data('id')}`,
			cache: false,
			success: function (html) {
				$("body").append(html);
				$(".movement-dialog").dialog({
					title: "Modifier un mouvement",
					autoOpen: true,
					height: 450,
					minHeight: 450,
					maxHeight: 450,
					minWidth: 500,
					maxWidth: 500,
					modal: true,
					buttons: {
						"Valider": () => {
							$.ajax({
								type: "POST",
								url: "./tools/ajax/movement.php",
								data: $(".movement-dialog form").serialize() + `&action=edit&asset_id=${$(e.target).data('asset-id')}&movement_id=${$(e.target).data('id')}`,
								cache: false,
								success: function (html) {
									$(".movement-dialog").remove();
									Toast.fire({
										...Alert.success,
										title: 'Mouvement modifié !',
										showConfirmButton: false,
									})
									$("#tab_mvt table").html(html);
									loadEvents();
								}
							});
						},
						"Annuler": () => {
							$(".movement-dialog").remove();
						},
					},
					close: () => {
						$(".movement-dialog").remove();
					}
				});
			}
		})
	});

	$(".delete-movement").click((e) => {
		Swal.fire(Alert.delete)
			.then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						type: "POST",
						url: "./tools/ajax/movement.php",
						data: `action=delete&asset_id=${$(e.target).data('asset-id')}&movement_id=${$(e.target).data('id')}`,
						cache: false,
						success: function (html) {
							console.log(html);
							if (!html) {
								Swal.fire({
									...Alert.error,
									title: 'Erreur !',
									text: "Une erreur est survenue lors de la suppression du mouvement.",
								})
							} else {
								$("#tab_mvt table").html(html);
								loadEvents();
								Toast.fire({
									...Alert.success,
									title: 'Mouvement supprimé !',
									showConfirmButton: false,
								})
							}
						}
					})
				}
			});
	});
}

/**
 * @event click Sur le bouton "Créer une marque"
 */
function manufacturersEvents() {
	$("#new-manufacturer").click(() => {
		$.ajax({
			type: "POST",
			url: "./tools/ajax/jquery_dialogs.php",
			data: "dialog=manufacturer&text=marque",
			cache: false,
			success: function (html) {
				$("body").append(html);
				$(".manufacturer-dialog").dialog({
					title: "Créer une marque",
					autoOpen: true,
					height: 250,
					minHeight: 250,
					maxHeight: 250,
					minWidth: 500,
					maxWidth: 500,
					modal: true,
					buttons: {
						"Valider": () => {
							$.ajax({
								type: "POST",
								url: "./tools/ajax/manufacturer.php",
								data: $(".manufacturer-dialog form").serialize() + "&action=new",
								cache: false,
								success: function (html) {
									$(".manufacturer-dialog").remove();
									Toast.fire({
										...Alert.success,
										title: 'Marque créée !',
										showConfirmButton: false,
									})
									$("#filters_manufacturers").html(html);
									loadEvents();
								}
							})
						}
					},
					close: () => {
						$(".manufacturer-dialog").remove();
					}
				});
			}
		})
	});
}

/**
 * @event click Sur le bouton "Créer un statut"
 */
function statusesEvents() {
	$("#new-status").click(() => {
		$.ajax({
			type: "POST",
			url: "./tools/ajax/jquery_dialogs.php",
			data: "dialog=status&text=statut",
			cache: false,
			success: function (html) {
				$("body").append(html);
				$(".status-dialog").dialog({
					title: "Créer un statut",
					autoOpen: true,
					height: 250,
					minHeight: 250,
					maxHeight: 250,
					minWidth: 500,
					maxWidth: 500,
					modal: true,
					buttons: {
						"Valider": () => {
							$.ajax({
								type: "POST",
								url: "./tools/ajax/status.php",
								data: $(".status-dialog form").serialize() + "&action=new",
								cache: false,
								success: function (html) {
									$(".status-dialog").remove();
									Toast.fire({
										...Alert.success,
										title: 'Statut créée !',
										showConfirmButton: false,
									})
									$("#filters_statuses").html(html);
									loadEvents();
								}
							})
						}
					},
					close: () => {
						$(".status-dialog").remove();
					}
				});
			}
		})
	});
}

/**
 * @event click Sur le bouton "Créer un type"
 */
function typesEvents() {
	$("#new-type").click(() => {
		$.ajax({
			type: "POST",
			url: "./tools/ajax/jquery_dialogs.php",
			data: "dialog=type&text=type",
			cache: false,
			success: function (html) {
				$("body").append(html);
				$(".type-dialog").dialog({
					title: "Créer un type",
					autoOpen: true,
					height: 250,
					minHeight: 250,
					maxHeight: 250,
					minWidth: 500,
					maxWidth: 500,
					modal: true,
					buttons: {
						"Valider": () => {
							$.ajax({
								type: "POST",
								url: "./tools/ajax/type.php",
								data: $(".type-dialog form").serialize() + "&action=new",
								cache: false,
								success: function (html) {
									$(".type-dialog").remove();
									Toast.fire({
										...Alert.success,
										title: 'Type créé !',
										showConfirmButton: false,
									})
									$("#filters_types").html(html);
									loadEvents();
								}
							})
						}
					},
					close: () => {
						$(".type-dialog").remove();
					}
				});
			}
		})
	});
}

/**
 * @event click Sur le bouton "Créer une localisation"
 * @event click Sur le bouton "Editer" d'une localisation
 */
function locationsEvents() {
	$("#new-location").click(() => {
		$.ajax({
			type: "POST",
			url: "./tools/ajax/jquery_dialogs.php",
			data: "dialog=location&text=localisation",
			cache: false,
			success: function (html) {
				$("body").append(html);
				$(".location-dialog").dialog({
					title: "Créer une localisation",
					autoOpen: true,
					height: 250,
					minHeight: 250,
					maxHeight: 250,
					minWidth: 500,
					maxWidth: 500,
					modal: true,
					buttons: {
						"Valider": () => {
							$.ajax({
								type: "POST",
								url: "./tools/ajax/location.php",
								data: $(".location-dialog form").serialize() + "&action=new",
								cache: false,
								success: function (html) {
									$(".location-dialog").remove();
									Toast.fire({
										...Alert.success,
										title: 'Localisation créée !',
										showConfirmButton: false,
									})
									$("#filters_locations").html(html);
									loadEvents();
								}
							})
						}
					},
					close: () => {
						$(".location-dialog").remove();
					}
				});
			}
		});
	});
	$(".edit-location").click((e) => {
		$.ajax({
			type: "POST",
			url: "./tools/ajax/jquery_dialogs.php",
			data: `dialog=location&text=localisation&id=${e.target.dataset.id}&table=p_localisation`,
			cache: false,
			success: function (html) {
				$("body").append(html);
				$(".location-dialog").dialog({
					title: "Modifier une localisation",
					autoOpen: true,
					height: 450,
					minHeight: 450,
					maxHeight: 450,
					minWidth: 500,
					maxWidth: 500,
					modal: true,
					buttons: {
						"Valider": () => {
							$.ajax({
								type: "POST",
								url: "./tools/ajax/location.php",
								data: $(".location-dialog form").serialize() + `&action=edit`,
								cache: false,
								success: function (html) {
									$(".location-dialog").remove();
									Toast.fire({
										...Alert.success,
										title: 'Localisation modifiée !',
										showConfirmButton: false,
									})
									$("#filters_locations").html(html);
									loadEvents();
								}
							});
						},
						"Annuler": () => {
							$(".location-dialog").remove();
						},
					},
					close: () => {
						$(".location-dialog").remove();
					}
				});
			}
		})
	});
}

function loadLoadingEvent() {
	$(".loading").click(function () {
		Swal.fire({
			title: `Chargement en cours...`,
			text: `Veuillez patienter pendant le chargement de la page`,
			showConfirmButton: false,
			allowOutsideClick: false,
			allowEscapeKey: false,
			allowEnterKey: false,
			didOpen: () => {
				Swal.showLoading()
			}
		});
	})
}

/**
 * Cette fonction permet de vérifier si un asset est en ligne ou non via un ping
 * 
 * @returns {void}
 */
function pingAsset() {
	$(".asset-status").each(function () {
		let item = $(this);
		$.ajax({
			url: './tools/ajax/asset_ping.php',
			type: 'GET',
			data: "id=" + $(this).data('id'),
			success: function (data) {
				item.children().addClass(data ? 'status-online' : 'status-offline');
				item.children().attr('title', data ? data : "Hors ligne");
			}
		});
	})
}