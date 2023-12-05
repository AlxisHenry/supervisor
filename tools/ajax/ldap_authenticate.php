<?php

declare(strict_types=1);
include_once '../../prog/start.php';
startPage(false);

$username = $_POST['username'] ?? "";
$password = $_POST['password'] ?? "";

if ($username && $password) {
	$ldap = new Ldap(
		user: $username,
		password: $password
	);

	$search = $ldap->search();
	if (!$search) exit();

	$_SESSION['ldap'] = [
		"user" => $username,
		"password" => $password
	];

	echo "ldap_connected";
}
