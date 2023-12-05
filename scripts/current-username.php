<?php

$asset = strtoupper(explode('.', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0]);

echo gethostbyaddr($_SERVER['REMOTE_ADDR']);
echo "<br>";
echo $asset;
echo "<br>";

$asset = P_matos::create(DB::findValueInTable("p_matos", "id", "asset", $asset));

$wmi = new Wmi($asset);

if ($wmi->isReachable() && $wmi->start()) {
	$user = $wmi->getUser();
	echo $user;
}