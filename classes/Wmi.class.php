<?php

/**
 * Cette classe permet de récupérer des informations sur un ordinateur distant via WMI
 */
final class Wmi
{
    const NAMESPACE = "root\\cimv2";
    private $WbemLocator;
    private $WbemServices;
    private ?string $hostname;
    const SRV_HOSTNAME = "COLHCCDLIC01";

    public function __construct(P_matos $computer)
    {
        $this->hostname = $computer->getAsset();
    }

    /**
     * Cette fonction permet de mettre à jour les informations d'un ordinateur s'il est joignable
     *
     * @return string
     */
    public static function updateComputer(P_matos $computer, &$status, bool $update = true)
    {
        $wmi = new Wmi($computer);
        if ($wmi->isReachable() && !$wmi->isCurrentServer() && $wmi->start()) {

            // Get disks informations
            $tabDisk = $wmi->getDisks();
            foreach ($tabDisk as $disk) {
                if ($disk[0] === "C:") {
                    $computer->setDs_letter($disk[0]);
                    $computer->setDs_volume($disk[1]);
                    $computer->setDs_size($disk[2]);
                    $computer->setDs_free($disk[3]);
                    break;
                }
            }
            // Serial Number
            $sn = $wmi->getSerialNumber();
            if ($sn) {
                $computer->setSn($sn);
            }
            // BIOS informations
            $bios = $wmi->getBiosNumber();
            if ($bios) {
                $computer->setBios($bios);
            }

            // Manufacturer
            $marque = $wmi->getManufacturer();
            $computer->setMarque($marque);

            // Model
            $modele = $wmi->getModel();
            $computer->setModele($modele);

            // RAM emplacements
            $emplRam = $wmi->getRamSlots();
            $computer->setSlot_ram($emplRam["nbre"]);
            $computer->setRam_max($emplRam["cap"]);

            // RAM installed
            $ram = $wmi->getRam();
            $computer->setRam($ram);

            // RAM détail
            $ramTxt = $wmi->getRamDescription();
            $computer->setRam_txt(mb_convert_encoding($ramTxt, "UTF-8"));

            // Processor
            $proc = $wmi->getProc();
            $computer->setProcesseur($proc);

            //bits
            $bit = $wmi->getBits();
            $computer->setOs_bits($bit);

            $computer->setUpdate_wmi(date("Y-m-d H:i:s"));

            if ($update) {
                P_matos::update($computer);
            } else {
                return [
                    "wmi_informations" => [
                        "disks" => $tabDisk,
                        "serial_number" => $sn,
                        "bios" => $bios,
                        "manufacturer" => $wmi->getManufacturer(debug: true),
                        "model" => $modele,
                        "ram" => $ram,
                        "ram_txt" => $ramTxt,
                        "proc" => $proc,
                        "bits" => $bit,
                        "empl_ram" => $emplRam,
                    ],
                    "computer" => $computer
                ];
            }

            $status = true;
        } else {
            $status = false;
        }
    }

    /**
     *
     */
    public static function getIp(string $id): string
    {
        $computer = P_matos::create($id);
        $wmi = new self($computer);
        $ip = $wmi->ping($wmi->hostname, ip: true);
        if (!$ip) return "Unknown";
        $ip = explode(" ", $ip)[2];
        $ip = explode(":", $ip)[0];
        $ip = substr($ip, 0, -1);
        return $ip;
    }

    /**
     * Cette fonction permet de vérifier si l'ordinateur est joignable (utilisable statiquement)
     *
     * @return bool
     */
    public static function isAvailable(string $id): bool
    {
        $computer = P_matos::create($id);
        $wmi = new self($computer);
        return $wmi->ping($wmi->hostname);
    }

    /**
     * Cette fonction permet de vérifier si l'ordinateur est joignable
     *
     * @return bool
     */
    public function isReachable(): bool
    {
        return $this->ping($this->hostname);
    }

    /**
     * Cette fonction permet d'effectuer la connexion WMI à un ordinateur distant
     *
     * @return bool
     */
    public function start(): bool
    {
        $this->WbemLocator = new COM("WbemScripting.SWbemLocator");
        try {
            $this->WbemServices = $this->WbemLocator->ConnectServer($this->hostname, Wmi::NAMESPACE, Ldap::USER, Ldap::PASSWORD);
            $this->WbemServices->Security_->ImpersonationLevel = 3;
            return true;
        } catch (Exception $e) {
            $this->WbemServices = null;
            return false;
        }
    }

    /**
     * Cette fonction permet de vérifier s'il s'agit du serveur actuel
     *
     * @return bool
     */
    private function isCurrentServer(): bool
    {
        return $this->hostname === self::SRV_HOSTNAME;
    }

    /**
     * Cette fonction effectue un ping sur l'ordinateur et traite le résultat
     *
     * @return bool|string
     */
    private function ping(?string $hostname, bool $ip = false): bool|string
    {
		if ($hostname === null) {
			return false;
		}
        exec("ping -n 1 $hostname", $output, $status);
        $output = $output[2] ?? $output[0];
        if (
            str_contains($output, 'Impossible de joindre') ||
            str_contains($output, 'a pas pu trouver') ||
            str_contains($output, 'attente de la demande')
        ) {
            return false;
        }
        return $ip ? $output : true;
    }

    private function getProc(): string
    {
        $tab = $this->WbemServices->ExecQuery("Select * from CIM_Processor");
        $proc = "";
        foreach ($tab as $elt) {
            $proc = $elt->Name;
        }
        return $proc;
    }

    private function getBits(): string
    {
        $tab = $this->WbemServices->ExecQuery("Select * from Win32_OperatingSystem");
        $bit = "";
        foreach ($tab as $elt) {
            try {
                $bit = $elt->OSArchitecture;
            } catch (Exception $e) {
                $bit = "Non récupéré";
            }
        }
        return $bit;
    }

    private function getRam(): int
    {
        $tab = $this->WbemServices->ExecQuery("Select * from CIM_PhysicalMemory");
        $ram = 0;
        foreach ($tab as $elt) {
            $ram += (int) Functions::displayOctetIntoGiga($elt->Capacity);
        }
        return $ram;
    }

    private function getRamDescription(): string
    {
        $tab = $this->WbemServices->ExecQuery("Select * from CIM_PhysicalMemory");
        $ramTxt = "";
        $nb_ram = 0;
        foreach ($tab as $elt) {
            $nb_ram++;
            if ($nb_ram > 1) {
                $ramTxt .= "\n";
            }
            $ramTxt .= Functions::displayOctetIntoGiga($elt->Capacity) . " Go | " . $elt->DeviceLocator;
            $ramTxt .= " | " . $elt->Manufacturer . ($elt->Speed ? " | " . $elt->Speed . " MHz" : "");
        }
        return $ramTxt;
    }

    private function getRamSlots(): array
    {
        $tab = $this->WbemServices->ExecQuery("Select * from Win32_PhysicalMemoryArray");
        $emplRam = ["nbre" => 0, "cap" => 0];
        foreach ($tab as $obj) {
            $emplRam["nbre"] = $obj->MemoryDevices; //nbre d'emplacements RAM
            $emplRam["cap"] = Functions::displayKiloIntoGiga($obj->MaxCapacity); //Cap MAX par PC
        }
        return $emplRam;
    }

    private function getModel(): string
    {
        $tab = $this->WbemServices->ExecQuery("Select * from Win32_ComputerSystemProduct");
        foreach ($tab as $elt) {
            if (empty(trim($elt->Version)) || $elt->Version == "None") {
                $mod = $elt->Name;
            } else {
                $mod = ($elt->Version == "AU100042.3" ? "Zebra ET51" : $elt->Version);
            }
        }
        return $mod;
    }

    private function getManufacturer(bool $debug = false): int|array
    {
        $tab = $this->WbemServices->ExecQuery("Select * from Win32_BaseBoard");
        $man = "";
        $marqueId = 0;
        foreach ($tab as $elt) {
            $man = strtoupper($elt->Manufacturer);
            if ($man == "HP" || $man == "HEWLETT PACKARD" || $man == "HEWLETT-PACKARD") {
                $marqueId = 1;
            } elseif ($man == "LENOVO") {
                $marqueId = 2;
            } elseif ($man == "DELL" || $man == "DELL INC" || $man == "DELL INC.") {
                $marqueId = 6;
            } elseif ($man == "ZEBRA TECHNOLOGIES INC") {
                $marqueId = 4;
            } elseif ($man == "MSIT") {
                $marqueId = 14;
            } elseif ($man == "INTEL CORPORATION") {
                $marqueId = 16;
            }
        }
        if (!$debug) {
            return $marqueId;
        } else {
            return [
                "manufacturer_name" => $man,
                "find_manufacturer" => $marqueId,
            ];
        }
    }

    private function getSerialNumber(): string
    {
        $tab = $this->WbemServices->ExecQuery("Select * from CIM_BIOSElement");
        $sn = "";
        foreach ($tab as $elt) {
            if (substr($elt->SerialNumber, 0, 2) != "VM") {
                $sn = $elt->SerialNumber;
            }
        }
        return $sn;
    }

    private function getBiosNumber(): string
    {
        $tab = $this->WbemServices->ExecQuery("Select * from Win32_BIOS");
        $bios = "";
        foreach ($tab as $elt) {
            $bios = $elt->Name;
        }
        return $bios;
    }

    private function getDisks(): array
    {
        $disks = $this->WbemServices->ExecQuery("Select * from Win32_LogicalDisk");
        $tabInfo = [];
        foreach ($disks as $disk) {
            $tabInfo[] = [$disk->Name, $disk->VolumeName, $disk->Size, $disk->FreeSpace];
        }
        return $tabInfo;
    }

    /**
     * @return array<Software>
     */
    public function getSoftwares(): array
    {
        $softs = $this->WbemServices->ExecQuery("Select * from Win32_Product");
        $tabInfo = [];
        foreach ($softs as $soft) {
            if ($soft->Name !== null) {
                $tabInfo[] = new Software($soft);
            }
        }
        return $tabInfo;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        $tab = $this->WbemServices->ExecQuery("Select * from Win32_ComputerSystem");
        $user = "";
        foreach ($tab as $elt) {
            $user = $elt->UserName;
        }
        return $user;
    }
}
