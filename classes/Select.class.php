<?php

/**
 * Cette classe contient les méthodes pour générer les listes déroulantes
 */
final class Select
{

	private const REPAIR_STATUSES = ["En attente", "Terminé"];
	private const INTERVENTION_TYPES = [
		["E", "Externe"],
		["I", "Interne"]
	];
	private const LANGUAGES = ["DE", "FR", "UK", "US"];
	private const AD_STATUSES = ["Hors domaine", "Connecté"];
	private const BUYING_TYPES = ["Achat", "Leasing"];

	/**
	 * @param bool $findSavedFilter
	 * @param string|null $value
	 * @param string $key
	 * @param bool $showCount
	 * @return string
	 */
	static function os(bool $versions = false, bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets", bool $showCount = true): string
	{
		if ($versions) {
			$req = "SELECT id, nom, number FROM p_os_version ORDER BY ordre DESC;";
		} else {
			$req = "SELECT id, nom FROM p_os ORDER BY nom;";
		}
		$sql = connectPdo()->prepare($req);
		$sql->execute();
		$data = $sql->fetchAll(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		$result = "";
		foreach ($data as $row) {
			$formatted = $row['nom'] . ($versions ? " - " . $row['number'] : "");
			$count = "";
			if ($showCount) {
				$count = $versions ? P_matos::whereCount("os_version", $row['id']) : P_matos::whereCount("os", $row['id']);
				$count = " ($count)";
			}
			$selected = "";
			if ($findSavedFilter) {
				$table = $versions ? "os_version" : "os";
				$selected = Filter::find($key, $table, $row['id']) ? "selected" : "";
			} else if ($value == $row['id']) {
				$selected = "selected";
			}
			$result .= "<option $selected value='{$row['id']}'>$formatted$count</option>";
		}
		// si une aucune version n'est sélectionnée, on ajoute une option vide
		if (!$findSavedFilter && !$value && $versions) {
			$result = "<option selected disabled value=''>Choisir une version</option>" . $result;
		}
		return $result;
	}

	/**
	 * @param bool $findSavedFilter
	 * @param string|null $value
	 * @param string $key
	 * @param bool $showCount
	 * @return string
	 */
	static function locations(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets", bool $showCount = true): string
	{
		$req = "SELECT id, nom FROM p_localisation ORDER BY nom;";
		$sql = connectPdo()->prepare($req);
		$sql->execute();
		$data = $sql->fetchAll(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		$result = "";
		foreach ($data as $row) {
			$selected = "";
			$count = "";
			if ($showCount) {
				$count = P_matos::whereCount("localisation", $row['id']);
				$count = " ($count)";
			}
			if ($findSavedFilter) {
				$selected = Filter::find($key, "localisation", $row['id']) ? "selected" : "";
			} else if ($value == $row['id']) {
				$selected = "selected";
			}
			$result .= "<option $selected value='{$row['id']}'>{$row['nom']}$count</option>";
		}
		return $result;
	}

	/**
	 * @param bool $findSavedFilter
	 * @param string|null $value
	 * @param string $key
	 * @param bool $showCount
	 * @return string
	 */
	static function types(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets", bool $showCount = true): string
	{
		$req = "SELECT id, nom FROM p_type ORDER BY nom;";
		$sql = connectPdo()->prepare($req);
		$sql->execute();
		$data = $sql->fetchAll(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		$result = "";
		foreach ($data as $row) {
			$selected = "";
			$count = "";
			if ($showCount) {
				$count = P_matos::whereCount("type", $row['id']);
				$count = " ($count)";
			}
			if ($findSavedFilter) {
				$selected = Filter::find($key, "type", $row['id']) ? "selected" : "";
			} else if ($value == $row['id']) {
				$selected = "selected";
			}
			$result .= "<option $selected value='{$row['id']}'>{$row['nom']}$count</option>";
		}
		return $result;
	}

	/**
	 * @param bool $findSavedFilter
	 * @param string|null $value
	 * @param string $key
	 * @return string
	 */
	static function manufacturers(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets", bool $showCount = true): string
	{
		$req = "SELECT id, nom FROM p_marque ORDER BY nom;";
		$sql = connectPdo()->prepare($req);
		$sql->execute();
		$data = $sql->fetchAll(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		$result = "";
		foreach ($data as $row) {
			$selected = "";
			$count = "";			
			if ($showCount) {
				$count = P_matos::whereCount("marque", $row['id']);
				$count = " ($count)";
			}
			if ($findSavedFilter) {
				$selected = Filter::find($key, "marque", $row['id']) ? "selected" : "";
			} else if ($value == $row['id']) {
				$selected = "selected";
			}
			$result .= "<option $selected value='{$row['id']}'>{$row['nom']}$count</option>";
		}
		return $result;
	}

	/**
	 * @param bool $findSavedFilter
	 * @param string|null $value
	 * @param string $key
	 * @return string
	 */
	static function statuses(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets", bool $showCount = true, ?string $archive = null): string
	{
		$req = "SELECT id, nom FROM p_statut";
		if ($archive === "without") {
			$req .= " WHERE id NOT IN (6,7)";
		} else if ($archive === "only") {
			$req .= " WHERE id IN (6,7)";
		}
		$req .= " ORDER BY nom;";
		$sql = connectPdo()->prepare($req);
		$sql->execute();
		$data = $sql->fetchAll(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		$result = "";
		foreach ($data as $row) {
			$selected = "";
			$count = "";
			if ($showCount) {
				$count = P_matos::whereCount("statut", $row['id']);
				$count = " ($count)";
			}
			if ($findSavedFilter) {
				$selected = Filter::find($key, "statut", $row['id']) ? "selected" : "";
			} else if ($value == $row['id']) {
				$selected = "selected";
			}
			$result .= "<option $selected value='{$row['id']}'>{$row['nom']}$count</option>";
		}
		return $result;
	}

	/**
	 * @param bool $findSavedFilter
	 * @param string|null $value
	 * @param string $key
	 * @return string
	 */
	static function models(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets", bool $showCount = true): string
	{
		$req = "SELECT DISTINCT modele FROM p_matos ORDER BY modele;";
		$sql = connectPdo()->prepare($req);
		$sql->execute();
		$data = $sql->fetchAll(PDO::FETCH_ASSOC);
		$sql->closeCursor();
		$result = "";
		foreach ($data as $row) {
			$count = "";
			if ($showCount) {
				$count = P_matos::whereCount("modele", $row['modele']);
				$count = " ($count)";
			}
			$selected = "";
			if ($findSavedFilter) {
				$selected = Filter::find($key, "modele", $row['modele']) ? "selected" : "";
			} else if ($value === $row['modele']) {
				$selected = "selected";
			}
			$result .= "<option $selected value='{$row['modele']}'>{$row['modele']}$count</option>";
		}
		return $result;
	}

	/**
	 * @param bool $findSavedFilter
	 * @param string|null $value
	 * @param string $key
	 * @return string
	 */
	static function repairsStatuses(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets"): string
	{
		$filter = Filter::find($key, "is_finished");
		$options = [];
		foreach (self::REPAIR_STATUSES as $i => $v) {
			$selected = "";
			if ($findSavedFilter && gettype($filter) !== "boolean") {
				$selected = $filter == $i ? "selected" : "";
			} else if ($value == $v) {
				$selected = "";
			}
			$options[] = "<option $selected value='$i'>$v</option>";
		}
		return implode("", $options);
	}

	/**
	 * @param bool $findSavedFilter
	 * @param string|null $value
	 * @param string $key
	 * @return string
	 */
	static function interventionTypes(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets"): string
	{
		$filter = Filter::find($key, "typeIntervention");
		$options = [];
		foreach (self::INTERVENTION_TYPES as $type) {
			$selected = "";
			if ($findSavedFilter) {
				$selected = $filter === $type[0] ? "selected" : "";
			} else if ($value === $type[0]) {
				$selected = "selected";
			}
			$options[] = "<option $selected value='{$type[0]}'>{$type[1]}</option>";
		}
		return implode("", $options);
	}

	/**
	 * @param string|null $value
	 * @return string
	 */
	static function languages(?string $value = null): string
	{
		$options = [];
		foreach (self::LANGUAGES as $lang) {
			$selected = "";
			if ($value === $lang) {
				$selected = "selected";
			}
			$options[] = "<option $selected value='$lang'>$lang</option>";
		}
		return implode("", $options);
	}

	/**
	 * @param bool $findSavedFilter
	 * @param string|null $value
	 * @param string $key
	 * @return string
	 */
	static function activeDirectoryStatuses(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets"): string
	{
		$filter = Filter::find($key, "statut_ad");
		$options = [];
		foreach (self::AD_STATUSES as $i => $v) {
			$selected = "";
			if ($findSavedFilter && gettype($filter) !== "boolean") {
				$selected = $filter == $i ? "selected" : "";
			} else if ($value == $v) {
				$selected = "";
			}
			$options[] = "<option $selected value='$i'>$v</option>";
		}
		return implode("", $options);
	}

	static function keyboardTypes(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets") : string
	{
		$filter = Filter::find($key, "clavier");
		$options = [];
		foreach (self::LANGUAGES as $type) {
			$selected = "";
			if ($findSavedFilter && gettype($filter) !== "boolean") {
				$selected = $filter == $type ? "selected" : "";
			} else if ($value === $type) {
				$selected = "selected";
			}
			$options[] = "<option $selected value='$type'>$type</option>";
		}
		return implode("", $options);
	}

	static function systemLanguages(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets"): string
	{
		$filter = Filter::find($key, "langue");
		$options = [];
		foreach (self::LANGUAGES as $type) {
			$selected = "";
			if ($findSavedFilter && gettype($filter) !== "boolean") {
				$selected = $filter == $type ? "selected" : "";
			} else if ($value === $type) {
				$selected = "selected";
			}
			$options[] = "<option $selected value='$type'>$type</option>";
		}
		return implode("", $options);
	}

	static function acquisitionTypes(bool $findSavedFilter = true, ?string $value = null, string $key = "filters_assets"): string
	{
		$filter = Filter::find($key, "type_achat");
		$options = [];
		foreach (self::BUYING_TYPES as $type) {
			$selected = "";
			if ($findSavedFilter && gettype($filter) !== "boolean") {
				$selected = $filter == $type ? "selected" : "";
			} else if ($value === $type) {
				$selected = "selected";
			}
			$options[] = "<option $selected value='$type'>$type</option>";
		}
		return implode("", $options);
	}

}
