<?php

/**
 * Démarrage page PHP
 */
function startPage(bool $withHtml, string $module = "assets"): void
{
    session_start();
    set_time_limit(60 * 30); // Temps d'exécution du script en secondes (0 = pas de limite)
    define("AUTH", true);
    define("ROOT", $_SERVER['DOCUMENT_ROOT'] . "/");
    define("STYLE_VAR", "variables.css");
    define("STYLE_UI", "jquery-ui.css");
    define("STYLE_MAIN", "main.css");
    define("JS_UI", "jquery-ui.js");
    define("JS_SWEETALERT", "sweetalert2.all.min.js");
    define("JQUERY", "jquery-3.6.0.min.js");
    $title = "Supervisor - Gestion du parc informatique";
    define("DOMAIN", "http://localhost:5050/");
    define("TITRE_SITE", $title);
    $str = (basename($_SERVER['PHP_SELF']) == "index.php" ? "/" : "../");
    define("REP_CSS", $str . "css/");
    define("REP_JS", $str . "js/");
    define("REP_JS_PAGES", $str . "js/pages/");
    define("REP_PROG", $str . "prog/");
    define("REP_CLASSES", ROOT . "classes/");
    define("REP_SCRIPTS", ROOT . "scripts/");
    define("REP_LIB", $str . "js/lib/");
    define("REP_MOD", ROOT . "modules/");
    define('TEL_SOLUTION_30', '');
    define('TEL_HP_SUPPORT', '');
    define('LOCAL_ADMIN_ACCOUNT', '');
    spl_autoload_register("loadClass");
    if (AUTH) {
        if (!Auth::attempt()) {
            die();
        }
    }
    if ($withHtml) {
        displayHeader($module);
    }
}

function displayHeader(string $module): void
{
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel='icon' href='data:;base64,iVBORw0KGgo='>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css'>
        <link href='<?= REP_CSS . STYLE_VAR ?>' media='all' rel='stylesheet' type='text/css' />
        <link href='<?= REP_CSS . STYLE_UI ?>' media='all' rel='stylesheet' type='text/css' />
        <link href='<?= REP_CSS . STYLE_MAIN ?>' media='all' rel='stylesheet' type='text/css' />
        <?php if ($module === "scripts") { ?>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <?php } ?>
        <meta http-equiv='content-type' content='text/html' charset='utf-8' />
        <title><?= TITRE_SITE ?></title>
    </head>
<?php
}

function loadClass(string $className): void
{
    include(REP_CLASSES . $className . ".class.php");
}

function connectPdo(): PDO
{
    $host = "localhost";
    if ($_SERVER['SERVER_PORT'] == 81) {
        $dbname = "supervisor";
    } else {
        $dbname = "supervisor";
    }
    $dblogin = "alexis";
    $dbpassword = "password";
    try {
        $bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dblogin, $dbpassword, array(
            PDO::ATTR_PERSISTENT => true
        ));
        // set the PDO error mode to exception
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // on affiche le message d'erreur si la connection plante
        die('Impossible de se connecter à la bd ' . $dbname . '.<br/>Erreur -> ' . $e->getMessage());
    }
    return $bdd;
}
