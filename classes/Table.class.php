<?php

/**
 * Cette classe permet de gérer l'affichage des formulaires et des tableaux
 */
final class Table
{

    const ACTIONS_TD_WIDTH = 15;

    /**
     * Affiche le tableau des utilisateurs
     *
     * @param array $filters
     * @return string
     */
    static function users(array $filters = []): string
    {
        $query = Filter::getQuery("filters_users", $filters, true, $wheresCount);
        // return $query; // Pour debug la requête il suffit de la retourner
        $sql = connectPdo()->prepare((string) $query);
        $sql->execute();
        $data = $sql->fetchAll();
        $sql->closeCursor();

        $tab = "<table class='list'><thead><tr>";
        $tab .= "<th class='nom'>Nom</th>";
        $tab .= "<th class='prenom'>Prénom</th>";
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";

        if (count($data) > 0) {
            foreach ($data as $c) {
                $formattedName = $c['nom'] && $c['prenom'] ? $c['nom'] . " " . $c['prenom'] : $c['nom'] . $c['prenom'];
                $tab .= "<tr id='{$c['id']}'>";
                $tab .= "<td class='nom'>{$c['nom']}</td>";
                $tab .= "<td class='prenom'>{$c['prenom']}</td>";
                $tab .= "<td width='" . self::ACTIONS_TD_WIDTH . "' class='actions'><div class='edit'>
                    <i title='Editer $formattedName' class='icon-small fa-solid fa-pen edit-user' data-user-id='{$c['id']}'></i>
                </div></td>";
                $tab .= "<td width='" . self::ACTIONS_TD_WIDTH . "' class='actions'><div class='delete'>
                    <i title='Supprimer $formattedName' class='icon-small fa-solid fa-trash-alt delete-user' data-user-full='{$formattedName}' data-user-id='{$c["id"]}'></i>
                </div></td>";
                $tab .= "</tr>";
            }
        } else {
            $tab .= "<tr><td colspan='10'>Aucun résultat n'a été trouvé.</td></tr>";
        }

        $tab .= "</tbody></table>";
        return $tab;
    }

    /**
     * Cette fonction permet de générer la liste des matériels en fonction des filtres
     *
     * @param array $filters - The filters to apply
     * @return string - The HTML list
     */
    static function assets(array $filters = [], bool $archived = false): string
    {
        $type = $archived ? "filters_archived_assets" : "filters_assets";
        $query = Filter::getQuery($type, $filters, true, $wheresCount);
        // return $query; // Pour debug la requête il suffit de la retourner
        $sql = connectPdo()->prepare((string) $query);
        $sql->execute();
        $data = $sql->fetchAll();
        $sql->closeCursor();

        $tab = "<table class='list' id='assets-list'><thead><tr>";
        $tab .= "<th class='asset'>Asset</th>";
        $tab .= "<th class='sn'>N° de série</th>";
        $tab .= "<th class='remarque'>Remarque</th>";
        $tab .= "<th class='localisation'>Localisation</th>";
        $tab .= "<th class='user'>Utilisateur</th>";
        $tab .= "<th class='os'>OS</th>";
        $tab .= "<th class='model'>Modèle</th>";
        $tab .= "<th class='marque'>Marque</th>";
        $tab .= "<th class='type'>Type</th>";
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";

        if (count($data) > 0) {
            foreach ($data as $c) {
                if ($wheresCount === 0) {
                    $computer = P_matos::create($c["id"]);
                    $id = $computer->getId();
                    $asset = $computer->getAsset();
                    $remarque = $computer->getRemarque();
                    $os = DB::findValueInTable("p_os", "nom", "id", $computer->getOs());
                    $vos = DB::findValueInTable("p_os_version", "nom", "id", $computer->getOs_version());
                    $type = DB::findValueInTable("p_type", "nom", "id", $computer->getType());
                    $localisation = DB::findValueInTable("p_localisation", "nom", "id", $computer->getLocalisation());
                    $sn = $computer->getSn() === "" ? "Non récupéré " : $computer->getSn();
                    $model = $computer->getModele();
                    $marque = DB::findValueInTable("p_marque", "nom", "id", $computer->getMarque());
                    $user = DB::findValueInTable("utilisateurs", "nom,prenom", "id", $computer->getUser());
                    $keyboard = $computer->getClavier();
                } else {
                    $id = $c["id"];
                    $asset = $c["asset"];
                    $remarque = $c["remarque"];
                    $os = $c['os_name'];
                    $vos = $c["os_version_name"];
                    $type = $c["type_name"];
                    $localisation = $c["localisation_name"];
                    $sn = $c["sn"] === "" ? "Non récupéré " : $c["sn"];
                    $model = $c["model"];
                    $marque = $c["marque_name"];
                    $user = $c["user_name"];
                    $keyboard = $c["keyboard"];
                }

                $formattedOsName = Functions::formatOsName($os) . " - " . $vos;

                $tab .= "<tr id='$id'>";
                $tab .= "<td class='list-link asset asset-list-cell";
                if ($keyboard !== "FR") {
                    $tab .= " bold'>";
                    $tab .= "<div class='keyboard lang-$keyboard'></div>";
                } else {
                    $tab .= "'>";
                }
                $tab .= "$asset";
                $tab .= "</td>";
                $tab .= "<td class='list-link sn'>$sn</td>";
                $tab .= "<td width='400' class='list-link remarque'>$remarque</td>";
                $tab .= "<td class='list-link localisation'>$localisation</td>";
                $tab .= "<td class='list-link user'>$user</td>";
                $tab .= "<td title='$os' width='120' class='list-link os'>$formattedOsName</td>";
                $tab .= "<td width='225' class='list-link model'>$model</td>";
                $tab .= "<td class='list-link marque'>$marque</td>";
                $tab .= "<td class='list-link type'>$type</td>";
                $tab .= "<td width='" . self::ACTIONS_TD_WIDTH . "' class='actions'><div class='repair'><i title='Créer une réparation pour $asset' class='icon-small fa-solid fa-screwdriver-wrench repair-dialog-opener' data-action='new' data-asset='$asset' data-asset-id='$id'></i></div></td>";
                $tab .= "<td width='" . self::ACTIONS_TD_WIDTH . "' class='actions'><div class='delete'><i title='Supprimer $asset' class='icon-small fa-solid fa-trash-alt delete-asset' data-asset-name='$asset' data-asset-id='$id'></i></div></td>";
                $tab .= "</tr>";
            }
        } else {
            $tab .= "<tr><td colspan='10'>Aucun résultat n'a été trouvé.</td></tr>";
        }

        $tab .= "</tbody></table>";
        return $tab;
    }

    /**
     * Cette fonction permet de générer la liste des mouvements d'un matériel
     *
     * @param P_matos $computer - The computer to display
     * @return string - The HTML table
     */
    static function movements(P_matos $asset): string
    {
        $id = $asset->getId();
        $query = "SELECT
                    p_mouvements.id,
                    p_mouvements.remarque as comment,
                    p_mouvements.date,
                    p_localisation.nom as 'loc',
                    CONCAT(utilisateurs.nom,' ',utilisateurs.prenom) as 'user'
                FROM p_mouvements
                INNER JOIN p_localisation
                    ON p_mouvements.localisation = p_localisation.id
                INNER JOIN utilisateurs
                    ON p_mouvements.user = utilisateurs.id
                WHERE matos = '{$asset->getId()}'
                ORDER BY id DESC;";
        $sql = connectPdo()->prepare($query);
        $sql->execute();
        $movements = $sql->fetchAll();
        $sql->closeCursor();

        $tab = "<table class='list'><thead><tr>";
        $tab .= "<th width='50'>Date</th>";
        $tab .= "<th width='150'>Localisation</th>";
        $tab .= "<th width='300'>Utilisateur</th>";
        $tab .= "<th>Remarque</th>";
        $tab .= "</tr></thead><tbody>";

        if ($movements) {
            foreach ($movements as $movement) {
                $tab .= "<tr>";
                $tab .= "<td>" . Functions::formatDate($movement["date"], false) . "</td>";
                $tab .= "<td>" . $movement["loc"] . "</td>";
                $tab .= "<td>" . $movement["user"] . "</td>";
                $tab .= "<td>" . $movement["comment"] . "</td>";
                $tab .= "<td width='" . self::ACTIONS_TD_WIDTH . "' class='actions'><div class='edit'>
                    <i title='Editer le mouvement' class='icon-small fa-solid fa-pen edit-movement'  data-asset-id='{$id}' data-id='{$movement["id"]}'></i>
                </div></td>";
                $tab .= "<td width='" . self::ACTIONS_TD_WIDTH . "' class='actions'><div class='delete'>
                    <i title='Supprimer le mouvement' class='icon-small fa-solid fa-trash-alt delete-movement' data-asset-id='{$id}' data-id='{$movement["id"]}'></i>
                </div></td>";
                $tab .= "</tr>";
            }
        } else {
            $tab .= "<tr><td colspan='10'>Aucun résultat n'a été trouvé.</td></tr>";
        }
        $tab .= "</tbody></table>";
        return $tab;
    }

    /**
     * Cette fonction permet de générer la liste de toutes les réparations ou celles d'un matériel en particulier
     *
     * @param array $filters - The filters to apply
     * @param P_matos|null $asset - The computer to display
     * @return string - The HTML table
     */
    static function repairs(array $filters = [], P_matos|null $asset = null): string
    {
        if ($asset) {
            $query = "SELECT * FROM p_reparations WHERE matos_id = '{$asset->getId()}';";
        } else {
            $query = Filter::getQuery("filters_repairs", $filters, true);
        }
        // return $query; // Debug
        $sql = connectPdo()->prepare($query);
        $sql->execute();
        $data = $sql->fetchAll();
        $sql->closeCursor();

        $cost = 0; # Total cost of current repairs

        $tab = "<table class='list'><thead><tr>";
        $tab .= "<th class='num'>N° de réparation</th>";
        if (!$asset) {
            $tab .= "<th class='sn'>N° de série</th>";
            $tab .= "<th class='asset'>Asset</th>";
            $tab .= "<th class='asset'>Utilisateur</th>";
        }
        $tab .= "<th class='asset'>Type</th>";
        $tab .= "<th class='asset'>Intervenant</th>";
        $tab .= "<th class='asset'>Cout</th>";
        $tab .= "<th class='asset'>Date</th>";
        $tab .= "<th class='remarque'>Remarque</th>";
        $tab .= "<th class='asset'>Statut</th>";
        $tab .= "<th class='asset'>Créé le</th>";
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";

        if (count($data) > 0) {
            foreach ($data as $c) {
                $repairId = $c["id"];
                $assetId = $c["matos_id"] ?? DB::findValueInTable("p_matos", "id", "asset", $c["asset"]) ?? null;
                $type = $c["type"] === "E" ? "Externe" : "Interne";
                $statut = $c["is_finished"] === 1 ? "Terminé" : "En attente";
                $tab .= "<tr id='{$c["id"]}'>";
                $tab .= "<td class='num'>". ($c["num"] !== "" ? $c["num"] : "Non renseigné") ."</td>";
                if (!$asset) {
                    $user = explode(" ", $c["user"]);
                    $date = Functions::formatDate($c["date"], isTime: true, fullYear: true);
                    $formattedUser = $user[1] . " " . ucfirst(strtolower($user[0]));
                    $formattedDate = Functions::formatDateToText(explode(" ", $date)[0]) . " à " . (explode(" ", $date)[1] ?? "");
                    if (explode(" ", $date)[1] ?? "" === "00:00") $formattedDate = Functions::formatDateToText(explode(" ", $date)[0]);
                    $email = [
                        "to" => rawurlencode(ucfirst(strtolower($user[1])) . ", "  . ucfirst(strtolower($user[0]))),
                        "subject" => rawurlencode("Disponibilité pour la réparation de votre matériel {$c["asset"]}"),
                        "message" => rawurlencode("Bonjour,
                            \nLe technicien chargé de la réparation de votre matériel {$c["asset"]} est disponible le $formattedDate.
                            \nMerci de confirmer votre disponibilité en répondant à ce mail.
                            \nCordialement,
                            \n\nIT Service")
                    ];
                    $mailto = "mailto:{$email["to"]}?subject={$email["subject"]}&body={$email["message"]}";
                    $tab .= "<td class='sn'>" . ($c["sn"] === "" ? "Non récupéré" : $c["sn"]) . "</td>";
                    $tab .= "<td class='asset'><a href='?mod=asset&id={$c["matos_id"]}' class='link' title='{$c["modele"]}' >{$c["asset"]}</a></td>";
                    $tab .= "<td class='user'><a title='Envoyer un email à {$formattedUser} pour l&#39;informer de la date de rendez-vous.' class='link' href='{$mailto}'>";
                    $tab .= "{$c["user"]}</a>";
                    $tab .= "</td>";
                }
                $tab .= "<td class='type'>$type</td>";
                $tab .= "<td class='intervenant'>{$c["intervenant"]}</td>";
                $tab .= "<td class='cout'>" . Functions::displayNumberIntoMonetary($c["cout"], false) . "</td>";
                $tab .= "<td class='date'>" . Functions::formatDate($c["date"], true) . "</td>";
                $tab .= "<td class='remarque'>{$c["remarque"]}</td>";
                $tab .= "<td class='asset'>$statut</td>";
                $tab .= "<td class='asset'>" . Functions::formatDate($c["created_at"], true) . "</td>";
                $tab .= "<td width='" . self::ACTIONS_TD_WIDTH . "' class='actions'><div class='edit'>
                    <i title='Editer la réparation' class='icon-small fa-solid fa-pen repair-dialog-opener' " . ($asset ? "data-from-asset-page='true'" : "") . " data-action='edit' data-repair-id='$repairId' data-asset-id='$assetId'></i>
                </div></td>";
                $tab .= "<td width='" . self::ACTIONS_TD_WIDTH . "' class='actions'><div class='delete'>
                    <i title='Supprimer la réparation' class='icon-small fa-solid fa-trash-alt delete-repair' " . ($asset ? "data-from-asset-page='true'" : "") . " data-repair-id='$repairId' data-asset-id='$assetId'></i>
                </div></td>";
                $tab .= "</tr>";
                $cost += $c["cout"];
            }
        } else {
            $tab .= "<tr><td colspan='10'>Aucun résultat n'a été trouvé.</td></tr>";
        }
        $tab .= "</tbody></table>";
        return "<div class='total_cost'>Coût total :&nbsp;<span>" . Functions::displayNumberIntoMonetary($cost, true) . " TTC</span></div>" . $tab;
    }

    /**
     * Cette fonction permet de générer la liste de toutes les marques
     *
     * @return string - The HTML table
     */
    static function manufacturers(array $filters = []): string
    {
        $query = Filter::getQuery("filters_manufacturers", $filters, true);
        $sql = connectPdo()->prepare((string) $query);
        $sql->execute();
        $data = $sql->fetchAll();
        $sql->closeCursor();
        $tab = "<table class='list'><thead><tr>";
        $tab .= "<th class='id'>ID</th>";
        $tab .= "<th class='name'>Nom</th>";
        $tab .= "</tr></thead>";
        if (count($data) > 0) {
            foreach ($data as $c) {
                $tab .= "<tr id='{$c["id"]}'>";
                $tab .= "<td class='id'>{$c["id"]}</td>";
                $tab .= "<td class='name'>{$c["nom"]}</td>";
                $tab .= "</tr>";
            }
        } else {
            $tab .= "<tr><td colspan='2'>Aucun résultat n'a été trouvé.</td></tr>";
        }
        return $tab .= "</tbody></table>";
    }

    /**
     * Cette fonction permet de générer la liste de tous les statuts
     *
     * @return string - The HTML table
     */
    static function statuses(array $filters = []): string
    {
        $query = Filter::getQuery("filters_statuses", $filters, true);
        $sql = connectPdo()->prepare((string) $query);
        $sql->execute();
        $data = $sql->fetchAll();
        $sql->closeCursor();
        $tab = "<table class='list'><thead><tr>";
        $tab .= "<th class='id'>ID</th>";
        $tab .= "<th class='name'>Nom</th>";
        $tab .= "</tr></thead>";
        if (count($data) > 0) {
            foreach ($data as $c) {
                $tab .= "<tr id='{$c["id"]}'>";
                $tab .= "<td class='id'>{$c["id"]}</td>";
                $tab .= "<td class='name'>{$c["nom"]}</td>";
                $tab .= "</tr>";
            }
        } else {
            $tab .= "<tr><td colspan='2'>Aucun résultat n'a été trouvé.</td></tr>";
        }
        return $tab .= "</tbody></table>";
    }

    /**
     * Cette fonction permet de générer la liste de tous les types
     *
     * @return string - The HTML table
     */
    static function types(array $filters = []): string
    {
        $query = Filter::getQuery("filters_types", $filters, true);
        $sql = connectPdo()->prepare((string) $query);
        $sql->execute();
        $data = $sql->fetchAll();
        $sql->closeCursor();
        $tab = "<table class='list'><thead><tr>";
        $tab .= "<th class='id'>ID</th>";
        $tab .= "<th class='name'>Nom</th>";
        $tab .= "</tr></thead>";
        if (count($data) > 0) {
            foreach ($data as $c) {
                $tab .= "<tr id='{$c["id"]}'>";
                $tab .= "<td class='id'>{$c["id"]}</td>";
                $tab .= "<td class='name'>{$c["nom"]}</td>";
                $tab .= "</tr>";
            }
        } else {
            $tab .= "<tr><td colspan='2'>Aucun résultat n'a été trouvé.</td></tr>";
        }
        return $tab .= "</tbody></table>";
    }

    /**
     * Cette fonction permet de générer la liste de tous les modèles
     *
     * @return string - The HTML table
     */
    static function models(array $filters = []): string
    {
        $query = Filter::getQuery("filters_models", $filters, true);
        $sql = connectPdo()->prepare((string) $query);
        $sql->execute();
        $data = $sql->fetchAll();
        $sql->closeCursor();
        $tab = "<table class='list'><thead><tr>";
        $tab .= "<th class='id'>Intitulé du modèle</th>";
        $tab .= "<th class='name'>Nombre d'ordinateurs</th>";
        $tab .= "</tr></thead>";
        if (count($data) > 0) {
            foreach ($data as $c) {
                $tab .= "<tr>";
                $tab .= "<td class='nom'>{$c["nom"]}</td>";
                $tab .= "<td class='nb'>{$c["nb"]}</td>";
                $tab .= "</tr>";
            }
        } else {
            $tab .= "<tr><td colspan='2'>Aucun résultat n'a été trouvé.</td></tr>";
        }
        return $tab .= "</tbody></table>";
    }

    /**
     * Cette fonction permet de générer la liste de tous les localisations
     *
     * @return string - The HTML table
     */
    static function locations(array $filters = []): string
    {
        $query = Filter::getQuery("filters_locations", $filters, true);
        // return $query;
        $sql = connectPdo()->prepare((string) $query);
        $sql->execute();
        $data = $sql->fetchAll();
        $sql->closeCursor();
        $tab = "<table class='list'><thead><tr>";
        $tab .= "<th class='id'>ID</th>";
        $tab .= "<th class='name'>Nom</th>";
        $tab .= "</tr></thead>";
        if (count($data) > 0) {
            foreach ($data as $c) {
                $tab .= "<tr id='{$c["id"]}'>";
                $tab .= "<td class='id'>{$c["id"]}</td>";
                $tab .= "<td class='name'>{$c["nom"]}</td>";
                $tab .= "<td width='" . self::ACTIONS_TD_WIDTH . "' class='actions'><div class='edit'>
                    <i title='Editer la localisation' class='icon-small fa-solid fa-pen edit-location' data-action='edit' data-id='{$c["id"]}'></i>
                </div></td>";
                $tab .= "</tr>";
            }
        } else {
            $tab .= "<tr><td colspan='2'>Aucun résultat n'a été trouvé.</td></tr>";
        }
        return $tab .= "</tbody></table>";
    }
}
