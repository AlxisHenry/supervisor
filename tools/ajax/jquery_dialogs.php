<?php

declare(strict_types=1);

include_once '../../prog/start.php';
startPage(false);

$dialog = $_POST['dialog'] ?? "";

switch ($dialog) {
	case 'user':
		$id = $_POST['user_id'] ?? null;
		echo Dialog::user($id);
		break;
	case 'movement':
		$id = $_POST['movement_id'] ?? null;
		echo Dialog::movements($id);
		break;
	case 'repair':
		$id = $_POST['repair_id'] ?? null;
		$asset = P_matos::create((int) $_POST['asset_id'] ?? null);
		echo Dialog::repairs($asset, $id);
		break;
	default:
		$dialog = $_POST['dialog'] ?? "";
		$text = $_POST['text'] ?? "";
		$id = $_POST['id'] ?? null;
		$table = $_POST['table'] ?? null;
		echo Dialog::sample($dialog, $text, $id, $table);
}
