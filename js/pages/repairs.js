/**
 * Cette fonction permet de mettre is_finished à 0 sur la liste des réparations
 */
function repairsListInitializeState() {
	let cookies = document.cookie.split(';');
	// check if status is set in cookies
	let status = cookies.find(cookie => cookie.includes('is_finished'));
	if (!status) $('#is_finished').val("0").change();
}

$(document).ready(function () {
  repairsListInitializeState();

	/**
	 * Gère le changement des filtres de dates sur la liste des réparations
	 */
	$(".change-dates-values").click(function (e) {
		const dates = {
			start: $(e.target).data('start'),
			end: $(e.target).data('end')
		}
		$('#date-start').val(dates.start).change();
		$('#date-end').val(dates.end).change();
	})
});