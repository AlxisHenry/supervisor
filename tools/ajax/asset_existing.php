<?php

declare(strict_types=1);

/**
 * Ce script permet de vérifier si un asset existe déjà
 */

include_once "../../prog/start.php";
startPage(false);

$asset = $_POST['asset'];
$id = DB::findValueInTable('p_matos', 'id', 'asset', $asset);

if ($id) {
	echo json_encode([
		'id' => $id,
	]);
} else {
	echo false;
}