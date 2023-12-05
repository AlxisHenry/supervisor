<?php

/**
 * P_matos class is the representation of the table p_matos in database
 */
class P_matos
{
    private $id;
    private $asset;
    private $sn = "";
    private $os = 12;
    private $os_version = 24;
    private $os_bits = "64 bits";
    private $localisation = 5;
    private $user = 0;
    private $statut_ad;
    private $statut = 0;
    private $num_immo = "";
    private $type;
    private $marque = 0;
    private $modele = "Inconnu";
    private $clavier = "FR";
    private $langue = "FR";
    private $date_achat = null;
    private $type_achat = "Leasing";
    private $duree_loc = 3;
    private $ram = 0;
    private $ram_txt = "";
    private $remarque = "";
    private $adm_pwd = "";
    private $ds_letter;
    private $ds_volume;
    private $ds_size;
    private $ds_free;
    private $bios = "";
    private $processeur = "";
    private $update_wmi;
    private $update_ad;
    private $slot_ram = 0;
    private $ram_max;
    private $bitlocker_password;
    private $bitlocker_id;
    private $bitlocker_date = null;
    private $comment = "";

    /**
     */
    public function lastUpdate(bool $wmi = false): string
    {
        if (!$wmi) {
            $lastUpdate = Functions::formatDate($this->getUpdate_ad(), isTime: true, readable: true);
        } else {
            $lastUpdate = Functions::formatDate($this->getUpdate_wmi(), isTime: true, readable: true);
        }
        return !$lastUpdate ? "Aucune mise à jour effectuée" : "Dernière mise à jour le " . $lastUpdate;
    }

    /**
     * Return true if the given asset already exists in database
     * 
     * @param string $name
     * @return mixed
     */
    static function alreadyExists(string $asset): bool
    {
        $requete = "SELECT COUNT(*) FROM p_matos WHERE asset =:asset";
        $sql = connectPdo()->prepare($requete);
        $sql->bindValue(':asset', $asset, PDO::PARAM_STR);
        $sql->execute();
        $nb = $sql->fetch();
        $sql->closeCursor();
        return $nb[0] > 0;
    }

    /**
     * Return the count of computers in database with the given status
     * 
     * @param int $statut
     * @return string
     */
    static function countByStatus(int $statut = 0): string
    {
        $filter = "";
        if ($statut != 0) {
            $filter = " WHERE statut = '" . $statut . "'";
        }
        $requete = "SELECT COUNT(*) FROM p_matos" . $filter;
        $sql = connectPdo()->prepare($requete);
        $sql->execute();
        $nb = $sql->fetch();
        $sql->closeCursor();
        return $nb[0];
    }

    /**
     * Return the count of computers in database with the given where condition
     * 
     * @param string $column
     * @param string $value
     * @return int
     */
    static function whereCount(string $column, string $value): int
    {
        $requete = "SELECT COUNT(*) FROM p_matos WHERE $column =:value";
        $sql = connectPdo()->prepare($requete);
        $sql->bindValue(':value', $value, PDO::PARAM_STR);
        $sql->execute();
        $nb = $sql->fetch();
        $sql->closeCursor();
        return $nb[0];
    }

    /**
     * Return the type of the computer
     * 
     * @param string $asset
     * @return int
     */
    static function checkType(string $asset): int
    {
        switch ($asset) {
            case str_starts_with($asset, "COLWL"):
                return (int) DB::findValueInTable("p_type", "id", "nom", "Laptop");
                break;
            case str_starts_with($asset, "COLWD"):
                return (int) DB::findValueInTable("p_type", "id", "nom", "Desktop");
                break;
            case str_starts_with($asset, "COLWT"):
                return (int) DB::findValueInTable("p_type", "id", "nom", "Tablette");
                break;
            case str_starts_with($asset, "COLH"):
                return (int) DB::findValueInTable("p_type", "id", "nom", "Server/VM");
                break;
            default:
                return (int) DB::findValueInTable("p_type", "id", "nom", "Inconnu");
                break;
        }
    }

    /**
     * Insert a new computer in database
     * 
     * @param P_matos $asset
     * @return P_matos
     */
    static function insert(P_matos $asset): P_matos
    {
        $query = "INSERT INTO p_matos (asset, sn, os, os_version, os_bits, localisation, user, statut_ad, statut, num_immo, type, marque, modele, clavier, langue, date_achat, type_achat, duree_loc, ram, ram_txt, remarque, adm_pwd, ds_letter, ds_volume, ds_size, ds_free, bios, processeur, update_wmi, update_ad, slot_ram, ram_max, bitlocker_password, bitlocker_id, bitlocker_date, comment)
                    VALUES (:asset, :sn, :os, :os_version, :os_bits, :localisation, :user, :statut_ad, :statut, :num_immo, :type, :marque, :modele, :clavier, :langue, :date_achat, :type_achat, :duree_loc, :ram, :ram_txt, :remarque, :adm_pwd, :ds_letter, :ds_volume, :ds_size, :ds_free, :bios, :processeur, :update_wmi, :update_ad, :slot_ram, :ram_max, :bitlocker_password, :bitlocker_id, :bitlocker_date, :comment)";
        $statement = connectPdo()->prepare($query);
        $statement->execute([
            'asset' => strtoupper($asset->getAsset()),
            'sn' => $asset->getSn(),
            'os' => $asset->getOs(),
            'os_version' => $asset->getOs_version(),
            'os_bits' => $asset->getOs_bits(),
            'localisation' => $asset->getLocalisation(),
            'user' => $asset->getUser(),
            'statut_ad' => $asset->getStatut_ad() ?? 0,
            'statut' => $asset->getStatut(),
            'num_immo' => $asset->getNum_immo(),
            'type' => $asset->getType(),
            'marque' => $asset->getMarque(),
            'modele' => $asset->getModele(),
            'clavier' => $asset->getClavier(),
            'langue' => $asset->getLangue(),
            'date_achat' => $asset->getDate_achat() === "" ? null : $asset->getDate_achat(),
            'type_achat' => $asset->getType_achat(),
            'duree_loc' => (int) $asset->getDuree_loc() ?? $asset->getDuree_loc(),
            'ram' => $asset->getRam(),
            'ram_txt' => $asset->getRam_txt(),
            'remarque' => $asset->getRemarque(),
            'adm_pwd' => $asset->getAdm_pwd(),
            'ds_letter' => $asset->getDs_letter(),
            'ds_volume' => $asset->getDs_volume(),
            'ds_size' => $asset->getDs_size(),
            'ds_free' => $asset->getDs_free(),
            'bios' => $asset->getBios(),
            'processeur' => $asset->getProcesseur(),
            'update_wmi' => $asset->getUpdate_wmi(),
            'update_ad' => $asset->getUpdate_ad(),
            'slot_ram' => $asset->getSlot_ram(),
            'ram_max' => $asset->getRam_max(),
            'bitlocker_password' => $asset->getBitlocker_password(),
            'bitlocker_id' => $asset->getBitlocker_id(),
            'bitlocker_date' => $asset->getBitlocker_date() === "" ? null : $asset->getBitlocker_date(),
            'comment' => $asset->getComment()
        ]);
        $statement->closeCursor();
        $asset = P_matos::create(connectPdo()->lastInsertId());
        P_mouvement::new(
            $asset->getId(),
            $asset->getLocalisation(),
            $asset->getUser(),
            date('Y-m-d'),
            "Création de l'asset"
        );
        return $asset;
    }

    /**
     * Update the computer in database
     * 
     * @param P_matos $asset
     */
    static function update(P_matos $asset): void
    {
        $computer = P_matos::create($asset->getId());

        // If the user has changed, create a new movement
        if ($computer->getUser() != $asset->getUser()) {
            P_mouvement::new(
                $asset->getId(),
                $asset->getLocalisation(),
                $asset->getUser(),
                date("Y-m-d"),
                "Changement d'utilisateur"
            );
        }

        if (($computer->getStatut() != $asset->getStatut()) && ($computer->getLocalisation() != $asset->getLocalisation())) {
            $oldStatus = DB::findValueInTable("p_statut", "nom", "id", $computer->getStatut());
            $newStatus = DB::findValueInTable("p_statut", "nom", "id", $asset->getStatut());
            $oldLocation = DB::findValueInTable("p_localisation", "nom", "id", $computer->getLocalisation());
            $newLocation = DB::findValueInTable("p_localisation", "nom", "id", $asset->getLocalisation());
            P_mouvement::new(
                $asset->getId(),
                $asset->getLocalisation(),
                $asset->getUser(),
                date("Y-m-d"),
                "Changement de statut de " . $oldStatus . " à " . $newStatus . " et de localisation de " . $oldLocation . " à " . $newLocation
            );
        } else {
            // If the status has changed, create a new movement
            if ($computer->getStatut() != $asset->getStatut()) {
                $old = DB::findValueInTable("p_statut", "nom", "id", $computer->getStatut());
                $new = DB::findValueInTable("p_statut", "nom", "id", $asset->getStatut());
                P_mouvement::new(
                    $asset->getId(),
                    $asset->getLocalisation(),
                    $asset->getUser(),
                    date("Y-m-d"),
                    "Changement de statut de " . $old . " à " . $new
                );
            } else if ($computer->getLocalisation() != $asset->getLocalisation()) {
                $old = DB::findValueInTable("p_localisation", "nom", "id", $computer->getLocalisation());
                $new = DB::findValueInTable("p_localisation", "nom", "id", $asset->getLocalisation());
                P_mouvement::new(
                    $asset->getId(),
                    $asset->getLocalisation(),
                    $asset->getUser(),
                    date("Y-m-d"),
                    "Changement de localisation de " . $old . " à " . $new
                );
            }
        }

        if ($computer->getAsset() !== $asset->getAsset()) {
            P_mouvement::new(
                $asset->getId(),
                $asset->getLocalisation(),
                $asset->getUser(),
                date("Y-m-d"),
                "Changement du nom de l'asset de " . $computer->getAsset() . " à " . $asset->getAsset()
            );
        }

        $query = "UPDATE p_matos SET
                    asset=:asset,
                    remarque=:remarque,
                    sn=:sn,
                    os=:os,
                    os_version=:os_version,
                    os_bits=:os_bits,
                    localisation=:localisation,
                    user=:user,
                    statut_ad=:statut_ad,
                    statut=:statut,
                    num_immo=:num_immo,
                    type=:type,
                    marque=:marque,
                    modele=:modele,
                    clavier=:clavier,
                    langue=:langue,
                    date_achat=:date_achat,
                    type_achat=:type_achat,
                    duree_loc=:duree_loc,
                    ram=:ram,
                    ram_txt=:ram_txt,
                    adm_pwd=:adm_pwd,
                    ds_letter=:ds_letter,
                    ds_volume=:ds_volume,
                    ds_size=:ds_size,
                    ds_free=:ds_free,
                    bios=:bios,
                    processeur=:processeur,
                    update_wmi=:update_wmi,
                    update_ad=:update_ad,
                    slot_ram=:slot_ram,
                    ram_max=:ram_max,
                    bitlocker_password=:bitlocker_password,
                    bitlocker_id=:bitlocker_id,
                    bitlocker_date=:bitlocker_date,
                    comment=:comment
                    WHERE id=:id;
                ";
        $statement = connectPdo()->prepare($query);
        $statement->execute([
            'id' => $asset->getId(),
            'remarque' => $asset->getRemarque(),
            'asset' => strtoupper($asset->getAsset()),
            'sn' => $asset->getSn() ?? "",
            'os' => $asset->getOs() ?? 12,
            'os_version' => $asset->getOs_version() ?? 0,
            'os_bits' => $asset->getOs_bits() ?? "",
            'localisation' => $asset->getLocalisation() ?? 5,
            'user' => $asset->getUser() ?? 0,
            'statut_ad' => $asset->getStatut_ad(),
            'statut' => $asset->getStatut(),
            'num_immo' => $asset->getNum_immo(),
            'type' => $asset->getType(),
            'marque' => $asset->getMarque(),
            'modele' => $asset->getModele(),
            'clavier' => $asset->getClavier() ?? "",
            'langue' => $asset->getLangue() ?? "",
            'date_achat' => $asset->getDate_achat() === "" ? null : $asset->getDate_achat(),
            'type_achat' => $asset->getType_achat() ?? "",
            'duree_loc' => (int) $asset->getDuree_loc() ?? 0,
            'ram' => $asset->getRam() ?? 0,
            'ram_txt' => $asset->getRam_txt() ?? "",
            'adm_pwd' => $asset->getAdm_pwd() ?? "",
            'ds_letter' => $asset->getDs_letter(),
            'ds_volume' => $asset->getDs_volume(),
            'ds_size' => $asset->getDs_size(),
            'ds_free' => $asset->getDs_free(),
            'bios' => $asset->getBios() ?? "",
            'processeur' => $asset->getProcesseur() ?? "",
            'update_wmi' => $asset->getUpdate_wmi(),
            'update_ad' => $asset->getUpdate_ad(),
            'slot_ram' => (int) $asset->getSlot_ram(),
            'ram_max' => (int) $asset->getRam_max(),
            'bitlocker_password' => $asset->getBitlocker_password(),
            'bitlocker_id' => $asset->getBitlocker_id(),
            'bitlocker_date' => $asset->getBitlocker_date() === "" ? null : $asset->getBitlocker_date(),
            'comment' => $asset->getComment() ?? ""
        ]);
    }

    /**
     * Return a P_matos object with the given id
     * 
     * @param int $id
     * @return P_matos
     */
    static function create(int|string $id): P_matos
    {
        $id = (int) $id;
        if ($id == 0) return new P_matos();
        $requete = "SELECT * FROM p_matos WHERE id=:id LIMIT 1";
        $sql = connectPdo()->prepare($requete);
        $sql->bindValue(':id', $id, PDO::PARAM_INT);
        $sql->setFetchMode(PDO::FETCH_CLASS, P_matos::class);
        $sql->execute();
        $result = $sql->fetch();
        $sql->closeCursor();
        if ($result === false) {
            $result = new P_matos();
        }
        return $result;
    }

    /**
     * Create a P_matos object from an array 
     *
     * @param array $data
     * @return P_matos
     */
    static function createFromArray(array $data, ?P_matos $asset = null): P_matos
    {
        $asset = $asset ?? new P_matos();
        $asset->setAsset($data['asset'] ?? $asset->getAsset());
        $asset->setRemarque($data['remarque'] ?? $asset->getRemarque());
        $asset->setSn($data['sn'] ?? $asset->getSn() ?? "");
        $asset->setOs($data['os'] ?? $asset->getOs());
        $asset->setOs_version($data['os_version'] ?? $asset->getOs_version());
        $asset->setOs_bits($data['os_bits'] ?? $asset->getOs_bits() ?? "");
        $asset->setLocalisation($data['localisation'] ?? $asset->getLocalisation());
        $asset->setUser($data['user'] ?? $asset->getUser());
        if (isset($data['statut_ad']) && ($data['statut_ad'] === "on" || $data['statut_ad'] === 1)) {
            $asset->setStatut_ad(1);
        } else {
            $asset->setStatut_ad(0);
        }
        $asset->setStatut($data['statut'] ?? $asset->getStatut());
        $asset->setNum_immo($data['num_immo'] ?? $asset->getNum_immo());
        $asset->setType($data['type'] ?? $asset->getType());
        $asset->setMarque($data['marque'] ?? $asset->getMarque());
        $asset->setModele($data['modele'] ?? $asset->getModele());
        $asset->setClavier($data['clavier'] ?? $asset->getClavier() ?? "");
        $asset->setLangue($data['langue'] ?? $asset->getLangue() ?? "");
        $asset->setDate_achat($data['date_achat'] ?? $asset->getDate_achat());
        $asset->setType_achat($data['type_achat'] ?? $asset->getType_achat() ?? "");
        $asset->setDuree_loc($data['duree_loc'] ?? $asset->getDuree_loc() ?? 0);
        $asset->setRam($data['ram'] ?? $asset->getRam() ?? 0);
        $asset->setRam_txt($data['ram_txt'] ?? $asset->getRam_txt() ?? "");
        $asset->setRemarque($data['remarque'] ?? $asset->getRemarque() ?? "");
        $asset->setAdm_pwd($data['adm_pwd'] ?? $asset->getAdm_pwd() ?? "");
        $asset->setDs_letter($data['ds_letter'] ?? $asset->getDs_letter());
        $asset->setDs_volume($data['ds_volume'] ?? $asset->getDs_volume());
        $asset->setDs_size($data['ds_size'] ?? $asset->getDs_size());
        $asset->setDs_free($data['ds_free'] ?? $asset->getDs_free());
        $asset->setBios($data['bios'] ?? $asset->getBios() ?? "");
        $asset->setProcesseur($data['processeur'] ?? $asset->getProcesseur() ?? "");
        $asset->setSlot_ram($data['slot_ram'] ?? $asset->getSlot_ram());
        $asset->setRam_max((int) ($data['ram_max'] ?? $asset->getRam_max()));
        $asset->setBitlocker_password($data['bitlocker_password'] ?? $asset->getBitlocker_password());
        $asset->setBitlocker_id($data['bitlocker_id'] ?? $asset->getBitlocker_id());
        $asset->setBitlocker_date($data['bitlocker_date'] ?? $asset->getBitlocker_date() ?? null);
        $asset->setComment($data['comment'] ?? $asset->getComment() ?? "");
        return $asset;
    }
    
    /**
     * Return an array with all the properties of the object
     * 
     * @param array $exclude
     * @return array
     */
    public function __toArray(array $exclude = []): array {
        $array = [];
        foreach ($this as $key => $value) {
            if (!in_array($key, $exclude)) {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * Return an array with two keys : previous and next
     * 
     * @param P_matos $asset
     * @return array
     */
    static function getLinkedAssets(P_matos $asset): array
    {
        $query = "SELECT id, asset FROM p_matos ORDER BY asset";
        $statement = connectPdo()->query($query);
        $assets = $statement->fetchAll(PDO::FETCH_ASSOC);
        $assetsCount = count($assets);
        $assetIndex = array_search($asset->getId(), array_column($assets, 'id'));
        if ($assetIndex === false) return [];
        return [
            "previous" => $assets[($assetIndex - 1 + $assetsCount) % $assetsCount],
            "next" => $assets[($assetIndex + 1) % $assetsCount]
        ];
    }

    public function getRam_txt()
    {
        return $this->ram_txt;
    }

    public function setRam_txt($ram_txt): void
    {
        $this->ram_txt = $ram_txt;
    }

    public function getDuree_loc()
    {
        return $this->duree_loc;
    }

    public function setDuree_loc($duree_loc): void
    {
        $this->duree_loc = $duree_loc;
    }

    public function getProcesseur()
    {
        return $this->processeur;
    }

    public function setProcesseur($processeur): void
    {
        $this->processeur = $processeur;
    }

    public function getSlot_ram()
    {
        return $this->slot_ram;
    }

    public function getRam_max()
    {
        return $this->ram_max;
    }

    public function setSlot_ram($slot_ram): void
    {
        $this->slot_ram = $slot_ram;
    }

    public function setRam_max($ram_max): void
    {
        $this->ram_max = $ram_max;
    }

    public function getUpdate_wmi()
    {
        return $this->update_wmi;
    }

    public function getUpdate_ad()
    {
        return $this->update_ad;
    }

    public function setUpdate_wmi($update_wmi): void
    {
        $this->update_wmi = $update_wmi;
    }

    public function setUpdate_ad($update_ad): void
    {
        $this->update_ad = $update_ad;
    }

    public function getBios()
    {
        return $this->bios;
    }

    public function setBios($bios): void
    {
        $this->bios = $bios;
    }

    public function getDs_letter()
    {
        return $this->ds_letter;
    }

    public function getDs_volume()
    {
        return $this->ds_volume;
    }

    public function getDs_size()
    {
        return $this->ds_size;
    }

    public function getDs_free()
    {
        return $this->ds_free;
    }

    public function setDs_letter($ds_letter): void
    {
        $this->ds_letter = $ds_letter;
    }

    public function setDs_volume($ds_volume): void
    {
        $this->ds_volume = $ds_volume;
    }

    public function setDs_size($ds_size): void
    {
        $this->ds_size = $ds_size;
    }

    public function setDs_free($ds_free): void
    {
        $this->ds_free = $ds_free;
    }


    public function getOs_version()
    {
        return $this->os_version;
    }

    public function setOs_version($os_version): void
    {
        $this->os_version = $os_version;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAsset()
    {
        return $this->asset;
    }

    public function getSn()
    {
        return $this->sn;
    }

    public function getOs()
    {
        return $this->os;
    }

    public function getOs_bits()
    {
        return $this->os_bits;
    }

    public function getLocalisation()
    {
        return $this->localisation;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getStatut_ad()
    {
        return $this->statut_ad;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function getNum_immo()
    {
        return $this->num_immo;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getMarque()
    {
        return $this->marque;
    }

    public function getModele()
    {
        return $this->modele;
    }

    public function getClavier()
    {
        return $this->clavier;
    }

    public function getLangue()
    {
        return $this->langue;
    }

    public function getDate_achat()
    {
        return $this->date_achat;
    }

    public function getType_achat()
    {
        return $this->type_achat;
    }

    public function getRam()
    {
        return $this->ram;
    }

    public function getRemarque()
    {
        return $this->remarque;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setAsset($asset): void
    {
        $this->asset = $asset;
    }

    public function setSn($sn): void
    {
        $this->sn = $sn;
    }

    public function setOs($os): void
    {
        $this->os = $os;
    }

    public function setOs_bits($os_bits): void
    {
        $this->os_bits = $os_bits;
    }

    public function setLocalisation($localisation): void
    {
        $this->localisation = $localisation;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function setStatut_ad($statut_ad): void
    {
        $this->statut_ad = $statut_ad;
    }

    public function setStatut($statut): void
    {
        $this->statut = $statut;
    }

    public function setNum_immo($num_immo): void
    {
        $this->num_immo = $num_immo;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function setMarque($marque): void
    {
        $this->marque = $marque;
    }

    public function setModele($modele): void
    {
        $this->modele = $modele;
    }

    public function setClavier($clavier): void
    {
        $this->clavier = $clavier;
    }

    public function setLangue($langue): void
    {
        $this->langue = $langue;
    }

    public function setDate_achat($date_achat): void
    {
        $this->date_achat = $date_achat;
    }

    public function setType_achat($type_achat): void
    {
        $this->type_achat = $type_achat;
    }

    public function setRam($ram): void
    {
        $this->ram = $ram;
    }

    public function setRemarque($remarque): void
    {
        $this->remarque = $remarque;
    }

    public function getAdm_pwd()
    {
        return $this->adm_pwd;
    }

    public function setAdm_pwd($adm_pwd): void
    {
        $this->adm_pwd = $adm_pwd;
    }


    /**
     * Get the value of bitlocker_id
     */ 
    public function getBitlocker_id()
    {
        return $this->bitlocker_id;
    }

    /**
     * Set the value of bitlocker_id
     *
     * @return  self
     */ 
    public function setBitlocker_id($bitlocker_id)
    {
        $this->bitlocker_id = $bitlocker_id;

        return $this;
    }

    /**
     * Get the value of bitlocker_password
     */ 
    public function getBitlocker_password()
    {
        return $this->bitlocker_password;
    }

    /**
     * Set the value of bitlocker_password
     *
     * @return  self
     */ 
    public function setBitlocker_password($bitlocker_password)
    {
        $this->bitlocker_password = $bitlocker_password;

        return $this;
    }

    /**
     * Get the value of bitlocker_date
     */ 
    public function getBitlocker_date()
    {
        return $this->bitlocker_date;
    }

    /**
     * Set the value of bitlocker_date
     *
     * @return  self
     */ 
    public function setBitlocker_date($bitlocker_date)
    {
        $this->bitlocker_date = $bitlocker_date;

        return $this;
    }

    /**
     * Get the value of comment
     */ 
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the value of comment
     *
     * @return  self
     */ 
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
