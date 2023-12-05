<?php

/**
 * Cette classe permet de se connecter à l'Active Directory et de récupérer des informations
 */
final class Ldap
{
    const SERVER = "";
    const USER = "";
    const PASSWORD = '';

    # Liste des OU à ignorer (ex: OU=InactiveComputers...)
    public const IGNORED_DN = [];
    private $ldap = null;
    # La base du domaine
    private string $base = "";
    /**
     * Filtre de recherche de base (ex: (&(objectCategory=Computer)))
     * Ajoute de (Name=<name>*) pour trier par nom
     */
    private string $filter = "";

    /**
     * Update table p_matos avec objet asset
     * @param P_matos $asset
     * @return array
     */
    static function updateComputer(P_matos $asset, P_matos $tempo): void
    {
        $asset->setBitlocker_date($tempo->getBitlocker_date() ?? null);
        $asset->setBitlocker_id(($tempo->getBitlocker_id() ? $tempo->getBitlocker_id() : ""));
        $asset->setBitlocker_password(($tempo->getBitlocker_password() ? $tempo->getBitlocker_password() : ""));
        $asset->setStatut_ad(1);
        $asset->setRemarque(($tempo->getRemarque() ? $tempo->getRemarque() : ""));
        $asset->setAdm_pwd(($tempo->getAdm_pwd() ? $tempo->getAdm_pwd() : ""));
        $asset->setOs($tempo->getOs());
        $asset->setOs_version($tempo->getOs_version());
        $asset->setAsset($asset->getAsset() ?? $tempo->getAsset());
        $asset->setType($asset->getType() ?? P_matos::checkType($asset->getAsset()));
        $asset->setUpdate_ad(date("Y-m-d H:i:s"));
        if ($asset->getId()) {
            P_matos::update($asset);
        } else {
            P_matos::insert($asset);
        }
    }

    /**
     * Cette fonction permet de formater les données récupérées depuis l'AD
     *
     * @param array $data
     * @param P_matos $computer
     * @return void
     */
    static function format(array $data, P_matos $computer): void
    {
        foreach ($data as $prop => $value) {
            switch ($prop) {
                case "dn":
                    $ldap = new Ldap();
                    $currentKey = [
                        "id" => null,
                        "key" => null,
                        "date" => null
                    ];
                    if ($ldap) {
                        $attr = array("msFVE-RecoveryPassword", "name", "distinguishedName");
                        $search = $ldap->search($value, "(objectClass=msFVE-RecoveryInformation)", $attr);
                        $keys = $ldap->getEntries($search);

                        $savedKeys = [];
                        foreach ($keys as $key) {
                            if (isset($key["msfve-recoverypassword"][0])) {
                                $regex = "/(?<date>[0-9]{4}-[0-9]{2}-[0-9]{2})T(?<time>[0-9]{2}:[0-9]{2}:[0-9]{2})(.+)\{(?<id>[A-Z0-9\-]+)\}/";
                                preg_match($regex, $key["name"][0], $matches);
                                $savedKeys[] = [
                                    "id" => $matches["id"],
                                    "key" => $key["msfve-recoverypassword"][0],
                                    "date" => $matches["date"] . " " . $matches["time"]
                                ];
                            }
                        }

                        // On trie les clés par date
                        if (count($savedKeys) > 0) {
                            usort($savedKeys, function ($b, $a) {
                                return strtotime($b["date"]) - strtotime($a["date"]);
                            });
                            $currentKey = $savedKeys[count($savedKeys) - 1]; // On récupère la clé la plus récente
                        }

                        $computer->setBitlocker_password($currentKey["key"] ?? $computer->getBitlocker_password() ?? "");
                        $computer->setBitlocker_id($currentKey["id"] ?? $computer->getBitlocker_id() ?? "");
                        $computer->setBitlocker_date($currentKey["date"] ?? $computer->getBitlocker_date() ??  null);
                    }
                    break;
                case "cn":
                    $computer->setAsset($value[0]);
                    break;
                case "description":
                    $computer->setRemarque($value[0]);
                    break;
                case "ms-mcs-admpwd":
                    $computer->setAdm_pwd($value[0]);
                    break;
                case "operatingsystem":
                    $val = str_replace("Entreprise", "Enterprise", $value[0]);
                    $computer->setOs(DB::findValueInTable("p_os", "id", "nom", $val));
                    break;
                case "operatingsystemversion":
                    $computer->setOs_version(DB::findValueInTable("p_os_version", "id", "number", $value[0]));
                    break;
                default:
                    break;
            }
        }
    }

    public function __construct()
    {
        $this->ldap = @ldap_connect($this->server);
        ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->ldap, LDAP_OPT_REFERRALS, 0);
        return @ldap_bind($this->ldap, $this->user, $this->password);
    }

    /**
     * Cette fonction permet de rechercher dans l'AD
     *
     * @param string $base
     * @param string $filter
     * @return LDAP\Result|bool
     */
    public function search(?string $base = null, ?string $filter = null): LDAP\Result | bool
    {
        try {
            if (!$base) $base = $this->base;
            if (!$filter) $filter = $this->filter;
            return @ldap_search($this->ldap, $base, $filter);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Cette fonction permet de récupérer les informations depuis l'AD
     *
     * @param string $base
     * @param string $filter
     * @return array
     */
    public function getEntries($search): array
    {
        $entries = ldap_get_entries($this->ldap, $search);
        return $entries;
    }

    /**
     * Cette fonction permet de fermer la connexion à l'AD
     *
     * @return void
     */
    public function close(): void
    {
        ldap_close($this->ldap);
    }

    public function replace(string $base, array $entry): bool
    {
        return ldap_mod_replace($this->ldap, $base, $entry);
    }

    public function __destruct()
    {
        $this->close();
    }
}
