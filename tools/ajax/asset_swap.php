<?php

declare(strict_types=1);

/**
 * Ce script permet d'intervertir deux assets
 */

include_once "../../prog/start.php";
startPage(false);

$currentAssetId = (int) $_POST['currentAssetId'];
$enteredAssetId = (int) $_POST['enteredAssetId'];

$currentAssetSave = P_matos::create($currentAssetId);
$enteredAssetSave = P_matos::create($enteredAssetId);

$enteredAsset = Functions::formatAssetOjectForSwap($enteredAssetId, $currentAssetSave);
$currentAsset = Functions::formatAssetOjectForSwap($currentAssetId, $enteredAssetSave);

P_matos::update($enteredAsset);
P_matos::update($currentAsset);

echo true;