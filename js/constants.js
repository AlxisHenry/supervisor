
/**
 * Les alertes générées par SweetAlert2 sont personnalisées à l'aide de cette constante,
 * En modifiant les valeurs de cette constante, on modifie les alertes de l'application
 */
const SweetAlertConfig = {
	icons: {
		success: "success",
		error: "error",
		warning: "warning",
		info: "info",
		question: "question",
	},
	colors: {
		base: "#f89a3b",
		danger: "#d33",
	},
	showConfirmButton: true,
	confirmButtonText: "Ok",
	timer: 3000,
	timerProgressBar: true,
}

/**
 * Vous pouvez utiliser les configurations par défaut pour cet application avec la constante Alert
 * 
 * Exemple d'utilisation:
 * 
 * Swal.fire(Alert.success);
 * Swal.fire(Alert.delete);
 * 
 * Pour configurer le titre, ou d'autres paramètres, il suffit de les modifier :
 * 
 * Swal.fire({
 * 	...Alert.success,
 * 	title: "Mon titre",
 * 	text: "Mon texte",
 *  // ...
 * });
 */
const Alert = {
	delete: {
		icon: SweetAlertConfig.icons.warning,
		title: "Êtes-vous sûr ?",
		text: "Vous ne pourrez pas revenir en arrière !",
		showCancelButton: true,
		showConfirmButton: true,
		confirmButtonColor: SweetAlertConfig.colors.danger,
		iconColor: SweetAlertConfig.colors.base,
		cancelButtonColor: SweetAlertConfig.colors.base,
		confirmButtonText: "Supprimer",
		cancelButtonText: "Annuler",
	},
	confirm: {
		icon: SweetAlertConfig.icons.warning,
		title: "Souhaitez-vous continuer ?",
		text: "Un changement va être effectué",
		showCancelButton: true,
		showConfirmButton: true,
		confirmButtonColor: SweetAlertConfig.colors.base,
		iconColor: SweetAlertConfig.colors.base,
		cancelButtonColor: SweetAlertConfig.colors.danger,
		confirmButtonText: "Continuer",
		cancelButtonText: "Annuler",
	},
	success: {
		title: "Succès",
		icon: SweetAlertConfig.icons.success,
		showConfirmButton: SweetAlertConfig.showConfirmButton,
		confirmButtonColor: SweetAlertConfig.colors.base,
		iconColor: SweetAlertConfig.colors.base,
		confirmButtonText: SweetAlertConfig.confirmButtonText,
		timerProgressBar: SweetAlertConfig.timerProgressBar,
		timer: SweetAlertConfig.timer,
	},
	error: {
		title: "Erreur",
		text: "Une erreur est survenue",
		icon: SweetAlertConfig.icons.error,
		showConfirmButton: SweetAlertConfig.showConfirmButton,
		confirmButtonColor: SweetAlertConfig.colors.danger,
		iconColor: SweetAlertConfig.colors.danger,
		confirmButtonText: SweetAlertConfig.confirmButtonText,
		timerProgressBar: SweetAlertConfig.timerProgressBar,
		timer: SweetAlertConfig.timer,
	}
}

/**
 * Vous pouvez utiliser la constante Toast pour afficher des notifications en haut à droite de l'écran
 * 
 * Exemple d'utilisation:
 * 
 * Toast.fire({
 * 	...Alert.success,
 * 	title: "Mon titre",
 *  showConfirmButton: false,
 * });
 */
const Toast = Swal.mixin({
  toast: true,
  position: 'bottom-end',
  timer: SweetAlertConfig.timer,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})