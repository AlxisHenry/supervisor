<?php
declare(strict_types=1);
require_once('prog/start.php');

if (isset($_GET["mod"])) {
	$mod = $_GET["mod"];
} else {
	$mod = "assets";
}

startPage(withHtml: true, module: $mod);

//if (!Auth::attempt()) {
//    die("You are not allowed to access this page.");
//}

?>

<body>
	<div id='container'>
		<?= View::displayHeaderBloc(); ?>
		<div id='main'>
			<div id="aside">
				<div id='menu'>
					<?= View::displayMenu(); ?>
				</div>
				<div id="support">
					<?= View::displaySupport(); ?>
				</div>
				<div id="documentation">
					<?= View::displayDocumentationLink(); ?>
				</div>
			</div>
			<div id='page'>
				<section id='section'>
					<?php View::displayModule($mod); ?>
				</section>
			</div>
		</div>
	</div>
	<?= View::insertJS($mod); ?>
</body>

</html>
