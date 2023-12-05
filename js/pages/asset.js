/**
 * Cette fonction permet d'afficher une alerte pour demander les identifiants LDAP
 * 
 * @returns {void}
 */
async function showLdapCredentialsAlert() {
	return await Swal.fire({
		...Alert.confirm,
		title: "Connexion expirée",
		html:
			"Votre session LDAP a expiré, veuillez vous reconnecter pour pouvoir modifier l'asset." +
			'<input id="swal-input1" class="swal2-input" placeholder="Identifiant">' +
			'<input type="password" id="swal-input2" class="swal2-input" placeholder="Mot de passe">' +
			'<div class="caps-lock-detector"><i class="fa-solid fa-triangle-exclamation"></i> Touche MAJ enfoncée</div>',
		focusConfirm: false,
		didOpen: () => {
			// On ajoute un événement keyup sur la fenêtre pour détecter si la touche MAJ est enfoncée
			window.addEventListener("keyup", (e) => {
				if (e.getModifierState("CapsLock")) {
					$(".caps-lock-detector").css('visibility', 'visible')
				} else {
					$(".caps-lock-detector").css('visibility', 'hidden')
				}
			});
		},
		preConfirm: () => {
			return [
				document.getElementById('swal-input1').value,
				document.getElementById('swal-input2').value
			]
		},
		didDestroy: () => {
			window.removeEventListener("keyup", () => {
				if (e.getModifierState("CapsLock")) {
					$(".caps-lock-detector").css('visibility', 'visible')
				} else {
					$(".caps-lock-detector").css('visibility', 'hidden')
				}
			});
		},
		allowOutsideClick: () => !Swal.isLoading()
	})
}

/**
 * Cette fonction permet de changer la localisation d'un asset
 * 
 * @param {HTMLDivElement} $el - L'élément HTML de la localisation
 */
function changeLocation($el) {
	let value = $el.html();
	Swal.fire({
		...Alert.confirm,
		title: "Changement de localisation",
		text: "Vous êtes sur le point de changer la localisation de l'asset pour " + value,
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: './tools/ajax/asset_location.php',
				type: 'POST',
				data: `asset_id=${$el.parent().data('asset-id')}&location_id=${$el.data('id')}`,
				success: function (data) {
					if (data) {
						$(".location").removeClass('active'); // On enlève la classe active à toutes les localisations
						$el.addClass('active'); // On ajoute la classe active à la localisation sélectionnée
						$(".location").unbind("click"); // On enlève les événements click sur les localisations
						$(".location:not(.active)").click(function () {
							changeLocation($(this)); // On ajoute l'événement click sur les localisations non actives
						});
						let content = $el.html() === "Utilisateur" ? "" : " - " + $el.html();
						console.log(content);
						$(".asset-location").html(content); // On met à jour la localisation de l'asset
						$("#tab_mvt table").html(data); // On met à jour le tableau des mouvements
						Toast.fire({
							...Alert.success,
							title: "Localisation mise à jour",
							showConfirmButton: false,
						});
						loadEvents();
					} else {
						Swal.fire({
							...Alert.error,
							title: "Oups...",
							text: "Une erreur est survenue lors du changement de localisation",
						});
					}
				}
			})
		}
	});
}

/**
 * Cette fonction permet de générer le commentaire d'un asset en fonction de ce qui a été saisi dans le formulaire
 * 
 * TODO: Mettre à jour cette fonction
 * 
 * @returns {void}
 */
function generateAssetComment() {

	const defaultUser = "SERVICE, It";
	const rooms = ["Salle prépa", "Salle serveurs", "Stock IT"];
	const locations = ["Utilisateur", "Autre"];
	const specificsAccounts = ["collect", "cpg", "yantai", "ctrl_ttb", "ctrl_tth", "coltemp", "ESSAI0SON", "stockeur", "opac2"];

	/**
	 * Formate le nom d'un utilisateur pour qu'il soit affiché correctement dans le commentaire
	 * Retourne le nom de l'utilisateur au format "Nom, Prénom"
	 * 
	 * @param {string} user - NAME FIRSTNAME 
	 * @returns {string}
	 */
	const formatUserName = (user) => {
		for (let specificAccount of specificsAccounts) {
			if (user.toUpperCase().includes(specificAccount.toUpperCase())) {
				return user;
			}
		}
		let words = user.split(" ");
		let firstName = words[words.length - 1].charAt(0).toUpperCase() + words[words.length - 1].slice(1).toLowerCase();
		if (firstName.includes('-')) {
			firstName = firstName.split('-').map((name) => name.charAt(0).toUpperCase() + name.slice(1).toLowerCase()).join('-');
		}
		firstName = firstName.replace('ee', 'ée');
		let lastName = words.slice(0, words.length - 1).join(" ");
		return `${lastName}, ${firstName}`;
	};

	const formatOsName = (os) => os.replace('&nbsp;', '')
		.replace('Windows ', 'W')
		.replace('Windows', 'W')
		.replace('Professional', 'Pro')
		.replace('Professionnel', 'Pro')
		.replace('Enterprise', 'Ent')
		.replace('Entreprise', 'Ent');

	let asset = {
		location: $(".locations").find(".location.active").html(),
		user: formatUserName($("#user-choice").val()), // L'input #user-choice est un autocomplete, on formate donc le nom de l'utilisateur
		model: $("#modele").val(),
		keyboard: $("#clavier").val(),
		os: {
			bits: $("#os_bits").val(),
			name: formatOsName($("#os").find(":selected").html()),
			lang: $("#langue").val()
		}
	}

	if (rooms.includes(asset.location)) {
		if (asset.user !== defaultUser) {
			asset.location = `${asset.location}`;
		} else {
			asset.location = `== SPARE IT == ${asset.location}`;
		}
	}

	let comment = "";

	if (asset.user !== defaultUser || (locations.includes(asset.location) && asset.user === defaultUser)) {
		comment += `${asset.user} - `;
	}

	comment += `( ${asset.model} / ${asset.keyboard} - ${asset.os.name} ${asset.os.lang} `;

	if (asset.os.bits.includes('32')) {
		comment += `x32 `;
	}

	comment += `)`;

	if (!locations.includes(asset.location)) {
		comment = `${asset.location} - ${comment}`
	};

	$("#remarque").val(comment);
}

/**
 * Met à jour les informations d'un asset depuis WMI ou Active Directory
 * 
 * @param {string} from - L'origine de la mise à jour
 * @param {HTMLButtonElement} $el - L'élément HTML du bouton
 * @returns {void}
 */
function updateAsset(from, $el) {
	Swal.fire({
		...Alert.confirm,
		title: `Mise à jour depuis ${from}`,
		text: `Vous êtes sur le point de mettre à jour les informations de l'asset depuis ${from} !`,
	}).then((result) => {
		if (result.isConfirmed) {
			Swal.fire({
				title: `Mise à jour depuis ${from}`,
				text: `Veuillez patienter pendant la mise à jour des informations de l'asset depuis ${from}...`,
				showConfirmButton: false,
				allowOutsideClick: false,
				allowEscapeKey: false,
				allowEnterKey: false,
				didOpen: () => {
					Swal.showLoading()
				}
			});
			$.ajax({
				type: 'POST',
				url: './tools/ajax/asset_update.php',
				data: `id=${$el.data('id')}&from=${from.toLowerCase()}`,
				success: function (data) {
					if (data !== "done") {
						Swal.fire({
							...Alert.error,
							timer: 5000,
							title: `Echec de la mise à jour...`,
							text: `L'asset n'a pas pu être mis à jour depuis ${from}, car l'hôte n'est pas joignable. Veuillez réessayer ultérieurement.`,
						})
					} else {
						localStorage.setItem("toast", JSON.stringify({
							...Alert.success,
							title: `Asset mis à jour depuis ${from}`,
							showConfirmButton: false,
						}));
						setTimeout(() => {
							location.reload();
						}, 1000);
					}
				}
			})
		}
	})
}

/**
 * Cette fonction permet de vérifier si l'on rentre un asset déjà existant dans l'input correspondant au nom de l'asset
 * 
 * @returns {void}
 */
function toggleAssetChanges() {
	let currentAsset = $("#asset").val();
	$("#asset").change(function () {
		let enteredAsset = $(this).val();
		if ($(this).val() !== currentAsset) {
			$.ajax({
				url: './tools/ajax/asset_existing.php',
				type: 'POST',
				data: `asset=${$(this).val()}`,
				success: function (data) {
					if (data) {
						const enteredAssetId = JSON.parse(data).id;
						Swal.fire({
							...Alert.confirm,
							title: "Cet asset existe déjà !",
							text: `Souhaitez-vous intervertir les deux assets ? (${currentAsset} et ${enteredAsset})`,
						}).then((result) => {
							if (result.isConfirmed) {
								Swal.fire({
									...Alert.delete,
									title: "Veuillez saisir l'asset actuel pour confirmer l'opération",
									text: "Attention, cette action créera des mouvements !",
									input: 'text',
									inputAttributes: {
										autocapitalize: 'off'
									},
									showCancelButton: true,
									confirmButtonText: 'Confirmer',
									showLoaderOnConfirm: true,
									preConfirm: (assetName) => {
										if (assetName !== currentAsset) {
											Swal.showValidationMessage(
												`Le nom de l'asset ne correspond pas !`
											)
										}
									},
									allowOutsideClick: () => !Swal.isLoading()
								}).then((result) => {
									if (result.isConfirmed) {
										let currentAssetId = $("#assetId").val();
										$.ajax({
											type: "POST",
											url: "./tools/ajax/asset_swap.php",
											data: `enteredAssetId=${enteredAssetId}&currentAssetId=${currentAssetId}`,
											cache: false,
											success: function (html) {
												localStorage.setItem("toast", JSON.stringify({
													...Alert.success,
													title: 'Assets intervertis !',
													showConfirmButton: false,
												}));
												console.log(html);
												location.reload();
											}
										})
									}
								})
							}
						})
					}
				}
			})
		}
	})
}

function assetBuyingTypeChange() {
	const toggle = () => {
		let buyingType = $("#type_achat").val();
		switch (buyingType) {
			case "Leasing":
				$("#duree_loc").parent().removeClass("hidden");
				$("#num_immo").parent().addClass("hidden");
				$("#num_immo").val("");
				break;
			case "Achat":
				$("#num_immo").parent().removeClass("hidden");
				$("#duree_loc").parent().addClass("hidden");
				$("#duree_loc").val("");
				break;
		}
	}
	toggle();
	$("#type_achat").change(toggle);
}

$(document).ready(function () {
	toggleAssetChanges();
	assetBuyingTypeChange();

	/**
	 * Ajoute l'évènement click sur les localisations non actives
	 */
	$(".location:not(.active)").click(function () {
		changeLocation($(this));
	});

	/**
	 * Génère le commentaire de l'asset
	 */
	$("#generate-asset-comment").click(function () {
		Swal.fire({
			...Alert.confirm,
			title: "Génération du commentaire",
			text: "Vous êtes sur le point de générer la remarque !",
		}).then((result) => {
			if (result.isConfirmed) {
				generateAssetComment();
			}
		});
	});

	/**
	 * Propose la liste de tous les assets dans l'input Assets.
	 * On peut sélectionner un asset existant, ce qui redirige vers la page de l'asset
	 */
	$("#assets").autocomplete({
		source: './tools/ajax/autocomplete_assets.php',
		select: function (event, ui) {
			window.location.href = '?mod=asset&id=' + ui.item.id;
		},
		delay: 0,
		minLength: 2
	});

	/**
	 * Propose la liste de tous les utilisateurs dans l'input User.
	 */
	$("#user-choice").autocomplete({
		source: './tools/ajax/autocomplete_users.php',
		select: function (event, ui) {
			$("#user").val(ui.item.id);
		},
		delay: 0,
		minLength: 2
	});

	/**
	 * Propose la liste des différents bios dans l'input Bios.
	 */
	$("#bios").autocomplete({
		source: './tools/ajax/autocomplete_bios.php',
	});

	/**
	 * Propose la liste des différents processeurs dans l'input Processeur.
	 */
	$("#processeur").autocomplete({
		source: './tools/ajax/autocomplete_processors.php',
	});

	/*
	 * Propose la liste de tous les modèles dans l'input Modele.
	 * On peut sélectionner un modèle existant ou en saisir un nouveau
	 */
	$("#modele").autocomplete({
		source: "./tools/ajax/autocomplete_models.php"
	});

	/**
	 * On ajoute la classe edited au bouton submit lorsqu'un champ est modifié
	 */
	$("#form_computer").change(function () {
		$("#submit").addClass("edited");
	});

	/**
	 * Soumet le formulaire de l'asset
	 */
	$("#submit").click(function () {
		$(this).removeClass("edited"); // On retire la classe edited pour éviter de demander une confirmation lors de la redirection
		Swal.fire({
			...Alert.confirm,
			text: "Vous êtes sur le point de soumettre le formulaire !"
		}).then((result) => {
			if (result.isConfirmed) {
				let serializedData = $("#form_computer").serialize();
				serializedData += `&user=${$("#user").val()}`; // On ajoute l'ID de l'utilisateur
				$.ajax({
					type: 'POST',
					url: './tools/ajax/asset_update.php',
					data: serializedData,
					success: function (id) {
						console.log(id)
						if (id === "asset_already_exists") {
							Swal.fire({
								...Alert.error,
								title: `Echec de la mise à jour...`,
								text: `L'asset n'a pas pu être mis à jour, car un asset avec le même nom existe déjà.`,
							})
						} else if (id === "ldap_not_connected") {
							(async () => {
								const { value: formValues } = await showLdapCredentialsAlert();
								if (formValues) {
									console.log(formValues);
									$.ajax({
										type: 'POST',
										url: './tools/ajax/ldap_authenticate.php',
										data: {
											username: formValues[0],
											password: formValues[1]
										},
										success: function (data) {
											console.log(data);
											if (data === "ldap_connected") {
												$("#submit").click();
											} else {
												Swal.fire({
													...Alert.error,
													title: "Erreur de connexion",
													text: "L'identifiant ou le mot de passe est incorrect."
												})
											}
										}
									})
								}
							})();
						} else {
							if (id.includes('SQL') || id.includes('Modify: Insufficient access in')) {
								const isSQL = () => id.includes('SQL');
								Swal.fire({
									...Alert.error,
									title: `Echec de la mise à jour...`,
									text: isSQL()
										? `L'asset n'a pas pu être mis à jour, car une erreur SQL est survenue. Veuillez réessayer ultérieurement.`
										: `La remarque AD de l'asset n'a pas pu être mise à jour, car vous n'avez pas les droits suffisants...`,
									showConfirmButton: false,
								})
							} else {
								localStorage.setItem("toast", JSON.stringify({
									...Alert.success,
									title: `Asset mis à jour`,
									showConfirmButton: false,
								}));
								location.href = `?mod=asset&id=${id}`; // On redirige vers la page de l'asset pour actualiser les données
							}
						}
					}
				})
			}
		})
	});

	/**
	 * Si une modification a été effectuée, on demande une confirmation avant de quitter la page
	 */
	window.addEventListener('beforeunload', (event) => {
		if ($("#submit").hasClass("edited")) {
			event.preventDefault();
			event.returnValue = '';
		}
	});

	/**
	 * Evènement click sur le bouton de mise à jour depuis WMI
	 */
	$("#update-from-wmi").click(function () {
		updateAsset("WMI", $(this));
	});

	/**
	 * Evènement click sur le bouton de mise à jour depuis Active Directory
	 */
	$("#update-from-ad").click(function () {
		updateAsset("AD", $(this));
	});

});