<?php

class Dialog
{

	public static function sample(string $dialog, string $text, ?string $id = null, ?string $table = null): string
	{
		if ($id && $table) {
			$value = DB::findValueInTable($table, "nom", "id", $id);
		} else {
			$value = "";
		}

		return '<div class="dialog ' . $dialog . '-dialog">
			<form class="form-group-column form-dialog-container">
				<input type="hidden" name="id" id="id" value="' . $id . '">
				<div class="form-group justify-between">
					<div class="form-group-column">
						<div class="form-element">
							<label class="form-label" for="nom">Intitulé de la ' . $text . '</label>
							<input type="text" class="form-input" value="' . $value . '" name="nom" id="nom" placeholder="Saisir l\'intitulé de la ' . $text . '">
						</div>
					</div>
				</div>
			</form>
		</div>';
	}

	public static function user(?string $id = null): string
	{
		if ($id) {
			$firstname = DB::findValueInTable("utilisateurs", "prenom", "id", $id);
			$name = DB::findValueInTable("utilisateurs", "nom", "id", $id);
		}

		return '<div class="dialog user-dialog">
			<form class="form-group-column form-dialog-container">
				<input type="hidden" name="id" id="id" value="' . $id . '">
				<div class="form-group justify-between">
					<div class="form-group-column">
						<div class="form-element">
							<label class="form-label" for="nom">Nom</label>
							<input type="text" class="form-input" name="nom" id="nom" value="' . ($name ?? "") . '" placeholder="Saisir le nom de l\'utilisateur">
						</div>
						<div class="form-element">
							<label class="form-label" for="prenom">Prénom</label>
							<input type="text" class="form-input" name="prenom" value="' . ($firstname ?? "") . '" id="prenom" placeholder="Saisir le prénom de l\'utilisateur">
						</div>
					</div>
				</div>
			</form>
		</div>';
	}

	public static function movements(?string $id = null): string
	{
		if ($id) {
			$query = "SELECT 
				date,
				p_localisation.nom as localisation, 
				CONCAT(utilisateurs.nom, ' ', utilisateurs.prenom) AS user,
				remarque
				FROM p_mouvements
			INNER JOIN p_localisation ON p_mouvements.localisation = p_localisation.id
			INNER JOIN utilisateurs ON p_mouvements.user = utilisateurs.id
			WHERE p_mouvements.id = ? LIMIT 1;";
			$sql = connectPdo()->prepare($query);
			$sql->execute([$id]);
			$data = $sql->fetch(PDO::FETCH_ASSOC);
			$sql->closeCursor();
		}

		if (isset($data["date"])) {
			$date = explode(" ", $data["date"])[0];
		}
		$localisation = $data['localisation'] ?? "";
		$user = $data['user'] ?? "";
		$remarque = $data['remarque'] ?? "";

		return '<div class="dialog movement-dialog">
			<form class="form-group-column form-dialog-container">
				<div class="form-group">
					<div class="form-element">
						<label for="date" class="form-label">Date du mouvement</label>
						<input type="date" disabled class="form-input" value="' . ($date ?? "") . '">
					</div>
					<div class="form-element-extended">
						<label for="localisation" class="form-label">Localisation</label>
						<input type="text" disabled class="form-input" value="' . $localisation . '" id="localisation">
					</div>
				</div>
				<div class="form-group justify-between">
					<div class="form-group-column">
						<div class="form-element">
							<label class="form-label" for="user_movement">Utilisateur</label>
							<input type="text" disabled value="' . $user . '" class="form-input" name="user_movement" id="user_movement">
						</div>
						<div class="form-element">
							<label class="form-label" for="remarque">Remarque</label>
							<input type="text" value="' . $remarque . '" class="form-input" name="remarque" id="remarque" placeholder="Saisir une remarque">
						</div>
					</div>
				</div>
			</form>
		</div>';
	}

	public static function repairs(?P_matos $asset = null, ?string $id = null): string
	{
		if ($id) {
			$query = "SELECT num, intervenant, p_reparations.type, cout, p_reparations.date, p_reparations.remarque, is_finished
				FROM p_reparations
				INNER JOIN p_matos ON p_reparations.matos_id = p_matos.id
				WHERE p_reparations.id = :id";
			$sql = connectPdo()->prepare($query);
			$sql->execute(["id" => $id]);
			$data = $sql->fetch(PDO::FETCH_ASSOC);	
			$sql->closeCursor();
		}

		$status = $asset->getStatut();
		$location = $asset->getLocalisation();

		function typeOptions(string $value = null) {
			$options = "";
			$types = ["E" => "Externe", "I" => "Interne"];
			foreach ($types as $key => $type) {
				$options .= '<option value="' . $key . '" ' . ($value == $key ? "selected" : "") . '>' . $type . '</option>';
			}
			return $options;
		}

		$checked = ($data['is_finished'] ?? null)== "1" ? "checked" : "";

		return '<div class="dialog repair-dialog">
			<form class="form-group form-dialog-container">
				<div class="form-group">
					<div class="form-element-extended">
						<label class="form-label" for="num">Numéro de dossier</label>
						<input type="text" class="form-input" name="num" id="num" data-value="" value="'. ($data['num'] ?? "") .'" placeholder="Saisir le numéro de dossier">
					</div>
					<div class="form-element-extended">
						<label class="form-label" for="cost">Coût de l\'intervention (TTC)</label>
						<input type="text" class="form-input cost" name="cost" id="cost" data-value="" value="'. ($data['cout'] ?? "") .'" placeholder="Coût en € (TTC)">
					</div>
					<div class="form-element-extended">
						<label class="form-label" for="intervenant">Intervenant</label>
						<input type="text" class="form-input not-reset" name="intervenant" value="'. ($data['intervenant'] ?? "") .'" data-value="Technicien" id="intervenant" placeholder="Saisir l\'intervenant" value="Technicien">
					</div>
				</div>
				<div class="form-group">
					<div class="form-element-extended">
						<label class="form-label" for="type">Type d\'invervention</label>
						<select class="form-select form-select-extended" name="type" data-value="E" id="type">
							<option disabled>Type d\'intervention</option>
							"'. typeOptions($data['type'] ?? "") .'"
						</select>
					</div>
					<div class="form-element-extended">
						<label class="form-label" for="statut">Statut de l\'ordinateur</label>
						<select class="form-select form-select-extended" name="statut" id="statut">
							<option disabled selected>Statut</option>
							' . Select::statuses(findSavedFilter: false, showCount: false, value: $status) . '
						</select>
					</div>
					<div class="form-element-extended">
						<label class="form-label" for="localisation">Localisation de l\'ordinateur</label>
						<select class="form-select form-select-extended" name="localisation" id="localisation">
							<option disabled selected>Localisation</option>
							' . Select::locations(findSavedFilter: false, showCount: false , value: $location) . '
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="form-element-extended">
						<label class="form-label" for="date">Date de l\'intervention</label>
						<input type="datetime-local" class="form-date extended" name="date" id="date" data-value="' . date("Y-m-d\TH:i") . '" date="' . date("Y-m-d\TH:i") . '" value="'. ($data['date'] ?? "") .'" placeholder="Saisir la date de l\'intervention">
					</div>
					<div class="form-element" style="display: flex; justify-content: flex-end;">
						<button type="button" id="reset-date" class="form-button">
							<div class="fa-regular fa-calendar-xmark"></div>
						</button>
					</div>
				</div>
				<div class="form-element">
					<label class="form-label" for="remarque">Remarque</label>
					<textarea class="form-area remarque" name="remarque" id="remarque" data-value="" cols="30" rows="10" placeholder="Saisir une remarque" style="resize: none;">'. ($data['remarque'] ?? "") .'</textarea>
				</div>
				<div class="form-element-extended">
					<label for="is_finished" class="form-label">Intervention terminée (Oui/Non)</label>
					<label class="form-label switch">
						<input type="checkbox" id="is_finished" name="is_finished" '. $checked .'>
						<span class="slider round"></span>
					</label>
				</div>
			</form>
		</div>';
	}
}
