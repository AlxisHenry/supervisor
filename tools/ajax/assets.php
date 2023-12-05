<?php

/**
 * Renvoie une liste d'assets
 */

declare(strict_types=1);
include_once '../../prog/start.php';
startPage(false);

$limit = $_GET['limit'] ?? null;
$for = $_GET['for'] ?? null;
$ignoreAlreadyExistingAssets = (int) $_GET['ignore_already_existing_assets'] ?? null;

$query = 'SELECT asset FROM p_matos';

if ($for === 'wmi') {
	$query = 'SELECT id, asset FROM p_matos';
	if ($limit) $query .= " LIMIT $limit";
} 

$result = connectPdo()->query($query);
$assets = $result->fetchAll(PDO::FETCH_ASSOC);

if ($for === "ad") {
	$ldap = new Ldap();
	if ($ldap) {
		$search = $ldap->search();
		$entries = $ldap->getEntries($search);
		$newAssets = [];
		foreach ($entries as $key => $entry) {
			if (is_int($key)) {
				foreach (LDAP::IGNORED_DN as $ignoredDn) {
					if (str_contains($entry['dn'], $ignoredDn)) continue 2;
				}
				$asset = [
					'asset' => $entry['cn'][0],
				];
				if (!$ignoreAlreadyExistingAssets) {
					if (!in_array($asset, $assets)) array_push($assets, $asset);
				} else {
					if (!in_array($asset, $assets)) array_push($newAssets, $asset);
				}
			}
		}
		if ($ignoreAlreadyExistingAssets) $assets = $newAssets;
	}
}

echo json_encode($assets);
