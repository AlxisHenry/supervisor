<?php

declare(strict_types=1);

/**
 * Ce script permet de vérifier si un ordinateur est disponible
 */

include_once "../../prog/start.php";
startPage(false);

$status = Wmi::isAvailable($_GET['id']);

if ($status) {
	echo Wmi::getIp($_GET['id']);
} else {
	echo false;
}