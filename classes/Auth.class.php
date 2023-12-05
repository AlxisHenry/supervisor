<?php

class Auth
{

    /**
     * @param string $user
     * @param string $asset
     * @return void
     */
	private static function log(string $user, string $asset): void
	{
		$query = "INSERT INTO logs (user, date, asset) VALUES (?, ?, ?)";
		$query = connectPdo()->prepare($query);
		$query->execute([$user, date("Y-m-d H:i:s"), $asset]);
		$query->closeCursor();
	}

	/**
	 * @return bool
	 */
	public static function check(): bool
	{
		return isset($_SESSION["logged"]) && $_SESSION["logged"] === true;
	}

	/**
	 * @return bool
	 */
	public static function attempt(): bool
	{
		if (!Auth::check()) {
			$asset = strtoupper(explode('.', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0]);
			$asset = P_matos::create(DB::findValueInTable("p_matos", "id", "asset", $asset));
			$wmi = new Wmi($asset);
			if ($wmi->isReachable() && $wmi->start()) {
				$user = $wmi->getUser();
				$user = strtolower(explode('\\', $user)[1]);
				$query = "SELECT * FROM access WHERE username = ?";
				$query = connectPdo()->prepare($query);
				$query->execute([$user]);
				$data = $query->fetchAll();
				if (count($data) > 0) {
					$_SESSION["logged"] = true;
					$id = $data[0]["id"];
					$_SESSION["id"] = $id;
					$_SESSION["username"] = $user;
					Auth::log($id, $asset->getAsset());
					return true;
				}
			}
			return false;
		} else {
			return true;
		}
	}
}
