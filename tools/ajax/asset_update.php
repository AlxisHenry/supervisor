<?php

declare(strict_types=1);

/**
 * Ce script permet de mettre à jour un asset via AD , WMI ou manuellement
 */

include_once '../../prog/start.php';
startPage(false);

/**
 * @var string $from - la source de la mise à jour (ad, wmi ou manuelle)
 */
$from = $_POST['from'] ?? "";

/**
 * @var string $id - l'id de l'asset à mettre à jour
 */
$id = $_POST['id'] ?? "";

/**
 * @var P_matos $asset
 */
$asset = P_matos::create((int) $id);

/**
 * @var bool $global - true si la mise à jour est globale (pour l'import) , false dans le cas contraire
 */
$global = $_POST['global'] ?? false;

switch ($from) {
	case 'ad':
		$start = microtime(true);
		$ldap = new Ldap();

		if ($ldap) {
			$assetName = $global ? $_POST['asset'] : $asset->getAsset();
			$filter = "(&(objectCategory=Computer)(Name=$assetName))";
			$search = $ldap->search(filter: $filter);
			$entries = $ldap->getEntries($search);
			$compId = DB::findValueInTable("p_matos", "id", "asset", $assetName);
			$computer = P_matos::create((int) $compId);

			// On vérifie le nombre d'entrées retournées par la recherche LDAP
			if (isset($entries['count']) && $entries['count'] === 0) {
				$computer->setStatut_ad(0);
				P_matos::update($computer);
			} else {
				$computer->setStatut_ad(1);
				foreach (Ldap::IGNORED_DN as $ignoredDN) {
					if (str_contains($entries[0]["dn"], $ignoredDN)) {
						$computer->setStatut_ad(0);
						break;
					}
				}
				if ($computer->getStatut_ad()) {
					$tempo = new P_matos();
					Ldap::format($entries[0], $tempo);
					Ldap::updateComputer($computer, $tempo);
				} else {
					P_matos::update($computer);
				}
			}

			if (!$global) {
				echo "done";
			} else {
				$executionTime = Functions::readableMicrotime(microtime(true) - $start);
				echo json_encode([
					"message" => $computer->getId() ? ($computer->getStatut_ad() ? "Mis à jour" : "Hors domaine"
					) : "Importé",
					"executionTime" => $executionTime
				]);
				exit();
			}
		}
		break;
	case 'wmi':
		$start = microtime(true);

		Wmi::updateComputer($asset, $status);

		if ($status && !$global) {
			echo "done";
		} else if ($global) {
			$executionTime = Functions::readableMicrotime(microtime(true) - $start);
			echo json_encode([
				"message" => $status ? "Mis à jour" : "Hors ligne",
				"executionTime" => $executionTime
			]);
			exit();
		}
		break;
	default:
		if ($asset->getId()) {
			/**
			 * Check if the asset already exists
			 */
			if ($asset->getAsset() !== $_POST['asset'] && P_matos::alreadyExists($_POST['asset'])) {
				echo "asset_already_exists";
				exit();
			}

			$remarque = $_POST['remarque'] ?? null;

			if ($remarque === "") {
				$remarque = "-";
				$_POST['remarque'] = "-";
			}

			if ($asset->getStatut_ad() && ($asset->getRemarque() !== $remarque)) {
				if (isset($_SESSION['ldap'])) {
					$credentials = $_SESSION['ldap'];
					$ldap = new Ldap(
						user: $credentials['user'],
						password: $credentials['password']
					);
					if ($ldap) {
						$filter = "(&(objectCategory=Computer)(Name={$asset->getAsset()}))";
						$search = $ldap->search(filter: $filter);
						$entries = $ldap->getEntries($search);
						if (isset($entries['count']) && $entries['count'] !== 0) {
							$a = $entries[0];
							$base = $a['dn'];
							$entry["description"] = [
								0 => $remarque
							];
							$ldap->replace($base, $entry);
						}
					}
				} else {
					echo "ldap_not_connected";
					exit();
				}
			}

			P_matos::update(P_matos::createFromArray($_POST, $asset));
		} else {
			if (P_matos::alreadyExists($_POST['asset'])) {
				echo "asset_already_exists";
				exit();
			}

			$asset = P_matos::insert(P_matos::createFromArray($_POST, new P_matos()));
		}
		echo $asset->getId();
		break;
}
