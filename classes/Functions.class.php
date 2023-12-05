<?php

final class Functions
{

    private static array $months = [
        "01" => "janvier",
        "02" => "février",
        "03" => "mars",
        "04" => "avril",
        "05" => "mai",
        "06" => "juin",
        "07" => "juillet",
        "08" => "août",
        "09" => "septembre",
        "10" => "octobre",
        "11" => "novembre",
        "12" => "décembre"
    ];

    /**
     * Retourne un nombre au format monétaire
     * 
     * @param int|float $nombre
     * @param bool $devise
     * @return string
     */
    static function displayNumberIntoMonetary(int|float $nombre, bool $devise): string
    {
        if (!$nombre) $nombre = 0;
        $nombre = floatval($nombre);
        $result = number_format($nombre, 2, ",", " ");
        if ($devise) $result .= " €";
        return $result;
    }

    /**
     * Rend une date en microtime lisible
     * 
     * @param float $microtime
     * @return string
     */
    static function readableMicrotime(float $microtime): string
    {
        return $microtime < 1 ? round($microtime * 1000) . ' ms' : round($microtime, 2) . ' s';
    }

    /**
     * Retourne une taille donnée en octets en Go
     * 
     * @param null|float $octet
     * @return string
     */
    static function displayOctetIntoGiga(null|float $octet): string
    {
        if (!$octet) return "";
        return number_format(($octet / pow(1024, 3)), 0, ",", " ");
    }
    static function displayKiloIntoGiga(null|float $kilo): string
    {
        if (!$kilo) return "";
        return number_format(($kilo / pow(1024, 2)), 0, ",", " ");
    }

    /**
     * Affiche la date au format FR 'd/m/Y H:i:s'
     * 
     * @param String $date
     * @param Bool $isTime
     * @param bool $fullYear
     * @param bool $readable
     * @return string
     */
    static function formatDate(?String $date, Bool $isTime, bool $fullYear = false, bool $readable = false)
    {
        $return = "";
        if ($date != null) {
            if ($date == "0000-00-00 00:00:00") return $return;
            if (strlen($date) > 10) {
                $format = "Y-m-d H:i:s";
            } else {
                $format = "Y-m-d";
            }
            if (self::isDate($date, $format)) {
                $result = date_create($date);
                $year =  $fullYear ? "Y" : "y";
                if ($isTime) {
                    $return = date_format($result, "d/m/$year H:i");
                    if ($readable) $return = join(" à ", explode(" ", $return));
                } else {
                    $return = date_format($result, "d/m/$year");
                }
            }
        }
        return $return;
    }

    /**
     * Affiche la date au format 13 Juin 2023
     * 
     * @param String $date
     * @return string
     */
    static function formatDateToText(?string $date): string
    {
        if (!$date) return "";
        $day = explode("/", $date)[0];
        $month = self::$months[explode("/", $date)[1]];
        $year = explode("/", $date)[2];
        return "$day $month $year";
    }

    /**
     * Check si une date est valide
     * 
     * @param String $date
     * @param String $format
     * @return boolean
     */
    static function isDate(String $date, String $format)
    {
        $result = false;
        if ($date) {
            try {
                $d = DateTime::createFromFormat($format, $date);
                if ($d && $d->format($format) == $date) {
                    $result = true;
                } else {
                    echo "ERREUR FORMAT DATE";
                }
            } catch (Exception $e) {
                echo "ERREUR DATE";
            }
        }
        return $result;
    }

    /**
     * Generate HTML elements for locations
     * 
     * @param string $value
     * @return string
     */
    static function generateLocationsHTMLElements(?string $value = null): string
    {
        $query = "SELECT id, nom FROM p_localisation ORDER BY nom;";
        $statement = connectPdo()->query($query);
        $statement->execute();
        $locations = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        $html = "";
        foreach ($locations as $location) {
            $active = $location["id"] == $value ? "active" : "";
            $html .= "<div class='location $active' data-id='{$location["id"]}'>{$location["nom"]}</div>";
        }
        return $html;
    }

    /**
     * Generate a string to show the rest of disk space
     * 
     * @param P_matos $asset
     * @return string
     */
    static function formatDiskSize(P_matos $asset): string
    {
        return $asset->getDs_letter() ? ($asset->getDs_volume() . " - " . Functions::displayOctetIntoGiga($asset->getDs_free()) . " Go de libre sur " . Functions::displayOctetIntoGiga($asset->getDs_size()) . " Go") : "";
    }

    static function count(string $table, string $where = null): int
    {
        $query = "SELECT COUNT(*) FROM $table";
        if ($where) $query .= " WHERE $where";
        $statement = connectPdo()->query($query);
        $statement->execute();
        $count = $statement->fetchColumn();
        $statement->closeCursor();
        return $count;
    }

    /**
     * Format an asset object for swap
     * 
     * @param int $id
     * @param P_matos $data
     * @return P_matos
     */
    static function formatAssetOjectForSwap(int $id, P_matos $data): P_matos
    {
        $asset = P_matos::create($id);
        $asset->setAsset($data->getAsset());
        $asset->setOs($data->getOs());
        $asset->setOs_version($data->getOs_version());
        $asset->setOs_bits($data->getOs_bits());
        $asset->setLangue($data->getLangue());
        $asset->setAdm_pwd($data->getAdm_pwd());
        $asset->setBitlocker_id($data->getBitlocker_id());
        $asset->setBitlocker_password($data->getBitlocker_password());
        $asset->setBitlocker_date($data->getBitlocker_date());
        $asset->setDs_free($data->getDs_free());
        $asset->setDs_size($data->getDs_size());
        $asset->setDs_letter($data->getDs_letter());
        $asset->setDs_volume($data->getDs_volume());
        return $asset;
    }

    /**
     * Format the name of an OS
     * 
     * @param string $os
     * @return string
     */
    static function formatOsName(string $os): string
    {
        $os = str_replace("Windows ", "W", $os);
        $os = str_replace("Server ", "S", $os);
        $os = str_replace("Professionnel", "Pro", $os);
        $os = str_replace("Professional", "Pro", $os);
        $os = str_replace("Entreprise", "Ent", $os);
        $os = str_replace("Enterprise", "Ent", $os);
        $os = str_replace("Education", "Edu", $os);
        $os = str_replace("Standard", "", $os);
        $os = str_replace("Datacenter", "", $os);
        $os = str_replace("Workstation", "Wks", $os);
        $os = str_replace("R2", "", $os);
        $os = str_replace("Édition Intégrale", "Ult", $os);
        $os = str_replace("Home", "H", $os);
        return $os;
    }
    
    static function isoToEmoji(string $code): string
    {
        $codeArray = str_split($code);
        $emoji = "";

        foreach ($codeArray as $char) {
            $emojiCode = ord($char) % 32 + 0x1F1E5;
            $emoji .= mb_chr($emojiCode);
        }

        return $emoji;
    }

}
