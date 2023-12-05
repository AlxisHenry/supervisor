<?php

$ldap = new Ldap();

if ($ldap) {

	$asset = $_GET["asset"] ?? null;

	if (!$asset) {
		throw new Error('Aucun asset fourni en paramètre de requête GET');
	}

	$filter = "(&(objectCategory=Computer)(Name=$asset))";
	$search = $ldap->search(filter: $filter);
	$entries = $ldap->getEntries($search);

	if (isset($entries['count']) && $entries['count'] === 0) { ?>
		<div class="alert alert-danger" role="alert">
			<h4 class="alert-heading">Asset introuvable</h4>
			<p>Il n'y a aucun asset correspondant à <code><?= $asset ?></code></p>
			<hr>
			<p class="mb-0">Vérifiez que l'asset existe bien dans l'Active Directory</p>
		</div>
	<?php } else {
		$cn = $entries[0]["cn"][0];
		$dn = $entries[0]["dn"];
		$dnshostname = $entries[0]["dnshostname"][0];
		$adminPassword = $entries[0]["ms-mcs-admpwd"][0];
		$objectCategory = $entries[0]["objectcategory"][0];
		$description = $entries[0]["description"][0];
		$isIgnored = false;
		foreach (Ldap::IGNORED_DN as $ignoredDN) {
			if (str_contains($entries[0]["dn"], $ignoredDN)) {
				$isIgnored = true;
				$ou = explode('=', explode(',', $entries[0]["dn"])[1])[1];
				break;
			}
		}
		?>

		<div class="alert alert-success" role="alert">
			<h4 class="alert-heading">Asset trouvé dans l'Active Directory</h4>
			<p>Le nom de l'asset est <code><?= $cn ?></code>, il se situe à l'adresse <code><?= $dn ?></code></p>
			<hr>
			<ul>
				<li class="mb-0">La description de l'asset est <code><?= $description ?></code></li>
				<li class="mb-0"><?= $isIgnored ? "L'asset appartient à l'OU <code>" . $ou . "</code>." : "L'asset provient d'<code>Europe</code>" ?></li>
				<li class="mb-0">Le DN de l'asset est <code><?= $dn ?></code></li>
				<li class="mb-0">Le CN de l'asset est <code><?= $cn ?></code></li>
				<li class="mb-0">Le nom d'hôte de l'asset est <code><?= $dnshostname ?></code></li>
				<li class="mb-0">Le mot de passe administrateur de l'asset est <code><?= $adminPassword ?></code></li>
				<li class="mb-0">La catégorie de l'objet est <code><?= $objectCategory ?></code></li>
			</ul>
			<div class="see-more">
				<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
					Voir toutes les informations
				</button>
			</div>
			<div class="collapse" id="collapse">
				<div class="card card-body">
					<pre><?php print_r($entries[0]) ?></pre>
				</div>
			</div>
		</div>
	<?php }
}
