<?php

declare(strict_types=1);

/**
 * Ce script permet de changer la localisation d'un asset
 */

include_once "../../prog/start.php";
startPage(false);

$locationId = (int) $_POST['location_id'];
$assetId = (int) $_POST['asset_id'];

$computer = P_matos::create($assetId);

if ($computer->getLocalisation() != $locationId) {
	$computer->setLocalisation($locationId);
	P_matos::update($computer);
	echo Table::movements($computer);
} else {
	echo false;
}
