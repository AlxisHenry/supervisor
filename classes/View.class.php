<?php

/**
 * Cette classe contient des fonctions statiques qui sont utilisées dans toutes les pages du site.
 */
final class View
{
    static function displayHeaderBloc(): string
    {
        return "<header id = 'header'></header>";
    }

    static function displayModule(string $name): void
    {
        include REP_MOD . $name . ".php";
    }

    static function displayMenu(): string
    {
        $assetsTabs = [
            "asset" => "Nouveau",
            "" => "Liste",
            "archives" => "Archives",
            "export" => "Export assets"
        ];

        $repairsTabs = [
            "repairs" => "Liste"
        ];

        $updateTabs = [
            "import_ad" => "Depuis l'AD",
            "import_wmi" => "Depuis WMI"
        ];

        $settingsTabs = [
            "types" => "Types",
            "statuses" => "Statuts",
            "manufacturers" => "Marques",
            "models" => "Modèles",
            "users" => "Utilisateurs",
            "locations" => "Localisations",
            "scripts" => "Scripts"
        ];

        return "
            <h3>Ordinateurs</h3>". self::generateMenu($assetsTabs) . "
            <h3>Réparations</h3>". self::generateMenu($repairsTabs) . "
            <h3>Mise à jour</h3>". self::generateMenu($updateTabs) . "
            <h3>Paramètres</h3>". self::generateMenu($settingsTabs);
    }

    private static function generateMenu(array $tabs): string
    {
        $active = fn (array $tabs): string => in_array($_GET["mod"] ?? "", $tabs);

        $menu = "<div class='menu-element' data-active='" . $active(array_keys($tabs)) . "'>";

        foreach ($tabs as $mod => $title) {
            $path = $mod !== "" ? "?mod=$mod" : "/";
            $class = $active([$mod]) ? "active" : "";
            if ($title === "Nouveau" && $mod === "asset") $class = "";
            $menu .= "<a class='$class' href='$path'>" . $title . "</a><br>";
        }
        return $menu . "</div>";
    }

    static function displaySupport(): string
    {
        if (TEL_HP_SUPPORT === "" || TEL_SOLUTION_30 === "") return "";
        return "
            <div><span>Support HP  :</span><span class='important'>" . TEL_HP_SUPPORT . "</span></div>
            <div><span>Solution 30 :</span><span class='important'>" . TEL_SOLUTION_30 . "</span></div>
        ";
    }

    static function displayDocumentationLink(): string
    {
        return "
            <div style='display: flex; flex-direction: column; gap: 12px;'>
                <a href='/docs/README.html' target='_blank'>Voir la documentation</a>
            </div>
        ";
    }

    static function insertJS(string $page): string
    {
        return "
            <script src='" . REP_LIB . JQUERY . "'></script>
            " . ( $page === "scripts" ? "
                <script src='https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
                <script src='https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
            " : "" ) . "<script src='" . REP_LIB . JS_UI . "'></script>
            <script src='" . REP_LIB . JS_SWEETALERT . "'></script>
            <script src='" . REP_JS . "constants.js'></script>
            <script src='" . REP_JS . "functions.js'></script>
            <script src='" . REP_JS . "main.js'></script>
            " . ( file_exists($_SERVER["DOCUMENT_ROOT"] . "/js/pages/$page.js") ? "
                <script src='" . REP_JS . "pages/" . $page . ".js'></script>
            " : "");
    }
}
