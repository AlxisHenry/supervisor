<?php

/**
 * Cette classe contient les fonctions de filtres
 */
final class Filter
{

	/**
	 * Cette fonction permet de générer une requête SQL à partir d'un tableau de filtres et d'un type de filtre
	 * 
	 * @param string $type - which filter
	 * @param array $filters - filters to apply
	 * @param bool $checkCookies - check if cookies are set
	 * @param int $wheresCount - number of where clauses
	 * @return string - query
	 */
	static function getQuery(string $type, array $filters = [], bool $checkCookies = false, &$wheresCount = 0): string
	{
		/**
		 * Dans le cas où on a des cookies, on les récupère et on applique les filtres
		 */
		if (isset($_COOKIE[$type]) && $checkCookies) {
			$cookie = $_COOKIE[$type];
			if ($cookie) {
				$filters = [];
				$filter = explode("&", $cookie);
				foreach ($filter as $f) {
					$f = explode("=", $f);
					if (isset($f[1]) && $f[1] !== null) {
						$filters[$f[0]] = $f[1];
					}
				}
			}
		}

		switch ($type) {
			case 'filters_repairs':
				$query = "SELECT 
                            p_reparations.id,
                            p_reparations.num,
                            p_reparations.type,
                            p_reparations.intervenant,
                            p_reparations.remarque,
                            p_reparations.cout,
                            p_reparations.date,
                            p_reparations.is_finished,
                            p_reparations.created_at,
							p_matos.id as matos_id,
                            p_matos.asset,
                            p_matos.modele,
                            p_matos.marque,
                            p_matos.sn,
							CONCAT(utilisateurs.prenom, ' ', utilisateurs.nom) as user
                        FROM p_reparations 
                        INNER JOIN p_matos ON p_reparations.matos_id = p_matos.id
						INNER JOIN utilisateurs ON p_matos.user = utilisateurs.id";
				break;
			case 'filters_users':
				$query = "SELECT id, nom, prenom FROM utilisateurs";
				break;
			case 'filters_assets':
				$query = "SELECT p_matos.id FROM p_matos INNER JOIN p_statut ON p_matos.statut = p_statut.id WHERE p_statut.id NOT IN (6,7)";
				break;
			case 'filters_archived_assets':
				$query = "SELECT p_matos.id FROM p_matos INNER JOIN p_statut ON p_matos.statut = p_statut.id WHERE p_statut.id IN (6,7)";
				break;
			case 'filters_manufacturers':
				$query = "SELECT id, nom FROM p_marque";
				break;
			case 'filters_models':
				$query = "SELECT DISTINCT modele as nom, count(id) as nb FROM p_matos";
				break;
			case 'filters_types':
				$query = "SELECT id, nom FROM p_type";
				break;
			case 'filters_statuses':
				$query = "SELECT id, nom FROM p_statut";
				break;
			case 'filters_locations':
				$query = "SELECT id, nom FROM p_localisation";
				break;
		}

		# On exclut les filtres qui nécessitent un traitement particulier
		$exclude = ['search', 'modele', 'date-start', 'date-end'];
		$wheres = [];

		foreach ($filters as $key => $value) {
			switch ($type) {
				case 'filters_repairs':
					if (!in_array($key, $exclude) && $value !== null) {
						switch ($key) {
							case 'marque':
								$wheres[] = 'p_matos.marque = "' . $value . '"';
								break;
							case 'type':
								$wheres[] = 'p_matos.type = "' . $value . '"';
								break;
							case 'typeIntervention':
								$wheres[] = 'p_reparations.type = "' . $value . '"';
								break;
							default:
								if ($value !== null && strlen($value) > 0) {
									$wheres[] = $key . " = " . $value;
								}
								break;
						}
					}
					break;
				default:
					if (!in_array($key, $exclude) && strlen($value) > 0) {
						if (is_string($value)) {
							$wheres[] = $key . " = '" . $value . "'";
						} else {
							$wheres[] = $key . " = " . $value;
						}
					}
					break;
			}
		}

		$model = $filters['modele'] ?? "";

		if ($model) {
			$wheres[] = 'p_matos.modele = "' . str_replace('+', ' ', $model) . '"';
		}

		$search = $filters['search'] ?? "";

		if ($search) {
			$search = htmlspecialchars($search);
			switch ($type) {
				case 'filters_repairs':
					$wheres[] = '(p_reparations.num LIKE "%' . $search . '%"
                        OR p_matos.asset LIKE "%' . $search  . '%"
                        OR p_matos.sn LIKE "%' . $search  . '%"
                        OR p_reparations.remarque LIKE "%' . $search  . '%"
                        OR p_reparations.intervenant LIKE "%' . $search  . '%")
                    ';
					break;
				case 'filters_users':
					$wheres[] = '(CONCAT(
									utilisateurs.prenom,
									" ",
									utilisateurs.nom
								) LIKE "%' . $search . '%" OR
								CONCAT(
									utilisateurs.nom,
									" ",
									utilisateurs.prenom
								) LIKE "%' . $search . '%")
					';
					break;
				case 'filters_archived_assets':
				case 'filters_assets':
					$wheres[] = '(CONCAT(
                                    utilisateurs.prenom,
                                    " ",
                                    utilisateurs.nom
                                ) LIKE "%' . $search . '%"
                        OR asset LIKE "%' . $search  . '%"
                        OR remarque LIKE "%' . $search  . '%"
						OR comment LIKE "%' . $search  . '%"
                        OR p_localisation.nom LIKE "%' . $search  . '%"
                        OR sn LIKE "%' . $search  . '%")
                    ';
					break;
				case 'filters_models':
					$wheres[] = 'modele LIKE "%' . $search . '%"';
					break;
				case in_array($type, ['filters_manufacturers', 'filters_types', 'filters_statuses', 'filters_locations']):
					$wheres[] = 'nom LIKE "%' . $search . '%"';
					break;
			}
		}

		$wheresCount = count($wheres); # On récupère le nombre de condi

		if ($wheresCount > 0) {
			switch ($type) {
				case 'filters_archived_assets':
				case 'filters_assets':
					$query = 'SELECT
                                p_matos.id,
                                asset,
                                remarque,
                                p_os.nom AS os_name,
                                p_os_version.nom AS os_version_name,
                                p_type.nom AS type_name,
                                p_localisation.nom AS localisation_name,
                                sn,
                                CONCAT(
                                    utilisateurs.prenom,
                                    " ",
                                    utilisateurs.nom
                                ) AS user_name,
                                modele AS model,
                                p_marque.nom AS marque_name,
								clavier AS keyboard
                            FROM
                                p_matos
                            INNER JOIN utilisateurs ON p_matos.user = utilisateurs.id
                            INNER JOIN p_os ON p_matos.os = p_os.id
                            INNER JOIN p_os_version ON p_matos.os_version = p_os_version.id
                            INNER JOIN p_type ON p_matos.type = p_type.id
                            INNER JOIN p_localisation ON p_matos.localisation = p_localisation.id
                            INNER JOIN p_marque ON p_matos.marque = p_marque.id
							INNER JOIN p_statut ON p_matos.statut = p_statut.id';
					break;
			}
			$query .= ' WHERE ' . implode(' AND ', $wheres);
			if ($type === "filters_assets") {
				$query .= ' AND p_statut.id NOT IN (6,7)';
			} else if ($type === "filters_archived_assets") {
				$query .= ' AND p_statut.id IN (6,7)';
			}
		}

		/**
		 * Vérification des filtres sur les dates
		 */
		if ($type === "filters_repairs") {
			$start = $filters["date-start"] ?? false;
			$end = $filters["date-end"] ?? false;

			// On récupère la date de la plus ancienne réparation
			$oldest = connectPdo()->query('SELECT MIN(date) FROM p_reparations')->fetchColumn();
			$oldest = date('Y-m-d', strtotime($oldest));

			// On récupère la date de la plus récente réparation
			$latest = connectPdo()->query('SELECT MAX(date) FROM p_reparations')->fetchColumn();
			$latest = date('Y-m-d', strtotime($latest));

			if ($start || $end) {
				if (!$start && $end) {
					$start = $oldest;
				} else if ($start && !$end) {
					$end = $latest;
				}
				$query .= ' AND p_reparations.date BETWEEN "' . $start . '" AND "' . $end . '"';
			}
		}

		switch ($type) {
			case 'filters_repairs':
				$query .= ' ORDER BY date ASC;';
				break;
			case 'filters_assets':
				$query .= ' ORDER BY p_matos.asset;';
				break;
			case 'filters_users':
				$query .= ' ORDER BY nom, prenom ASC;';
				break;
			case 'filters_models':
				$query .= ' GROUP BY modele ORDER BY nom;';
				break;
		}

		return $query;
	}

	/**
	 * Cette fonction permet de récupérer la valeur d'un filtre sauvegardé dans les cookies ou de vérifier si le filtre est égal à la valeur recherchée
	 * 
	 * @param string $cookie - The name of the cookie
	 * @param string $key - The key of the searched filter
	 * @param string|null $search - If null, return the value of the filter, else return true if the filter is equal to the search
	 * @return bool|string - The value of the filter or true if the filter is equal to the search
	 */
	static function find(string $cookie, string $key, ?string $search = null): bool | string
	{
		if (isset($_COOKIE[$cookie])) {
			$cookie = $_COOKIE[$cookie];
			$filter = explode("&", $cookie);
			foreach ($filter as $f) {
				$f = explode("=", $f);
				if ($f[0] == $key) {
					if ($f[0] === "modele") $f[1] = str_replace("+", " ", $f[1]);
					if ($search !== null) {
						return $f[1] == $search;
					} else {
						return $f[1];
					}
				}
			}
		}
		return false;
	}
}
