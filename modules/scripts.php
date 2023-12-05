<?php

if (isset($_GET["script"])) {
	include REP_SCRIPTS . $_GET["script"] . ".php";
} else {
	$scriptsDir = scandir(REP_SCRIPTS); ?>
	<div style="margin-left: 20px;">
		<h1>Liste des scripts</h1>
		<p>Veuillez sélectionner un asset pour exécuter un script.</p>
		<div class="form-element-extended">
			<input class="form-input" id="scripts-assets" placeholder="Sélectionner un ordinateur">
		</div>
		<p style="margin-top: 12px;">Asset sélectionné : <code id="selected-asset">aucun</code></p>
		<ul>
			<?php
			foreach ($scriptsDir as $script) {
				if ($script == "." || $script == ".." || !str_contains($script, ".php")) continue;
				$name = explode(".", $script)[0];
			?>
				<li>
					<a class="script-link" href="?mod=scripts&script=<?= $name ?>"><?= $script ?></a><br>
				</li>
			<?php } ?>
		</ul>
		<p>Si vous souhaitez exécuter un script sur un asset qui n'est pas dans la liste, veuillez l'ajouter à la fin du lien du script (ex: <code><?= DOMAIN ?>?mod=scripts&script=nom-du-script&asset=asset</code>).</p>
	</div>
<?php } ?>
