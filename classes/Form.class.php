<?php

declare(strict_types=1);

final class Form
{	
	/**
	 * @var string
	 */
	private const GROUP = "form-group";

	/**
	 * @var string
	 */
	private const ELEMENT = "form-element";
	
	/**
	 * @var string
	 */
	private const ELEMENT_EXTENDED = "form-element-extended";
	
	/**
	 * @var string
	 */
	private const LABEL = "form-label";
	
	/**
	 * @var string
	 */
	private const INPUT = "form-input";
	
	/**
	 * @var string
	 */
	private const SELECT = "form-select";
	
	/**
	 * @var string
	 */
	private const TEXTAREA = "form-textarea";
	
	/**
	 * @var string
	 */
	private const DATE = "form-date";

	/**
	 * Affiche le formulaire de gestion d'un ordinateur
	 * 
	 * @param P_matos $computer
	 * @return string
	 */
	public static function computer(P_matos $computer): string
	{
		$cgroup = self::GROUP;
		$celement = self::ELEMENT;
		$celement_extended = self::ELEMENT_EXTENDED;
		$clabel = self::LABEL;
		$cinput = self::INPUT;
		$cselect = self::SELECT;
		$ctextarea = self::TEXTAREA;
		$cdate = self::DATE;

		$disk = Functions::formatDiskSize($computer);
		$id = $computer->getId() ?? 0;
		$user = DB::findValueInTable('utilisateurs', 'nom,prenom', 'id', $computer->getUser()) ?? "";
		$new = $id ? "class='exist'" : "";
		$adStatus = $computer->getStatut_ad() == 1 ? "checked" : "";

		if ($adStatus) {
			$ldap = "ldap";
			$wmi = "wmi";
		} else {
			$ldap = "";
			$wmi = "";
		}

		$form = "<form id='form_computer' $new>";
		$form .= "<input type='hidden' id='assetId' name='id' value='$id'>";
		$form .= "<input type='hidden' name='utilisateur' id='user' value='{$computer->getUser()}'>";

		/**
		 * Fieldset contenant les principaux champs de l'ordinateur
		 */
		$form .= "<fieldset><legend>Informations</legend>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='asset' class='$clabel'>Asset</label>";
		$form .= "<input type='text' id='asset' name='asset' class='$ldap $cinput' value='{$computer->getAsset()}'>";
		$form .= "</div>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='statut' class='$clabel'>Statut</label>";
		$form .= "<select name='statut' id='statut' class='$cselect'>" . Select::statuses(false, (string) $computer->getStatut(), showCount: false) . "</select>";
		$form .= "</div>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='sn' class='$clabel'>SN</label>";
		$form .= "<input type='text' id='sn' name='sn' class='$wmi $cinput' value='{$computer->getSn()}'>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='user-choice' class='$clabel'>Utilisateur</label>";
		$form .= "<input class='$cinput' id='user-choice' placeholder='Attribuer à un utilisateur' name='user-choice' value='$user'>";
		$form .= "</div>";
		$form .= "<div class='$celement'>
			<label for='statut_ad' class='form-label'>Domaine (Oui/Non)</label>
			<label class='switch'>
				<input type='checkbox' id='statut_ad' name='statut_ad' $adStatus>
				<span title='Indiquer si l&#39;ordinateur est dans le domaine ou non.' class='slider round'></span>
			</label>
		</div>";
		$form .= "</div>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='remarque' class='$clabel'>Remarque</label>";
		$form .= "<input type='text' id='remarque' name='remarque' class='$ldap $cinput' value=\"{$computer->getRemarque()}\">";
		$form .= "</div>";
		$form .= "<div class='$celement' style='display: flex; justify-content: flex-end;'>";
		$form .= "<button type='button' id='generate-asset-comment' class='form-button'><div class='fa-solid fa-shuffle'></div></button>";
		$form .= "</div>";
		$form .= "</div>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='comment' class='$clabel'>Commentaire (hors AD)</label>";
		$form .= "<input type='text' id='comment' name='comment' class='$cinput' value=\"{$computer->getComment()}\">";
		$form .= "</div>";
		$form .= "</div>";
		$form .= "</fieldset>";

		/**
		 * Fieldset contenant les caractéristiques
		 */
		$form .= "<fieldset><legend>Caractéristiques</legend>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='type' class='$clabel'>Type</label>";
		$form .= "<select name='type' id='type' class='$cselect'>" . Select::types(false, (string) $computer->getType(), showCount: false) . "</select>";
		$form .= "</div>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='marque' class='$clabel'>Marque</label>";
		$form .= "<select name='marque' id='marque' class='$wmi $cselect'>" . Select::manufacturers(false, (string) $computer->getMarque(), showCount: false) . "</select>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='modele' class='$clabel'>Modèle</label>";
		$form .= "<input type='text' id='modele' name='modele' class='$wmi $cinput' value='{$computer->getModele()}'>";
		$form .= "</div>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='clavier' class='$clabel'>Clavier</label>";
		$form .= "<select name='clavier' id='clavier' class='$cselect'>" . Select::languages($computer->getClavier()) . "</select>";
		$form .= "</div>";
		$form .= "</div>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='bios' class='$clabel'>Bios</label>";
		$form .= "<input type='text' id='bios' name='bios' class='$wmi $cinput' value='{$computer->getBios()}'>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='processeur' class='$clabel'>Processeur</label>";
		$form .= "<input type='text' id='processeur' name='processeur' class='$wmi $cinput' value='{$computer->getProcesseur()}'>";
		$form .= "</div>";
		$form .= "</div>";
		$form .= "</fieldset>";

		/**
		 * Fieldset contenant les informations sur le système d'exploitation
		 */
		$form .= "<fieldset><legend>Windows</legend>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='os' class='$clabel'>Système</label>";
		$form .= "<select name='os' id='os' class='$cselect form-select-extended $ldap'>" .  Select::os(false, false, (string) $computer->getOs(), showCount: false) . "</select>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='os_version' class='$clabel'>Version</label>";
		$form .= "<select name='os_version' id='os_version' class='$cselect form-select-extended $ldap'>" . Select::os(true, false, (string) $computer->getOs_version(), showCount: false) . "</select>";
		$form .= "</div>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='os_bits' class='$clabel'>Bits</label>";
		$form .= "<input type='text' id='os_bits' name='os_bits' class='$wmi $cinput' value='{$computer->getOs_bits()}'>";
		$form .= "</div>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='langue' class='$clabel'>Langue</label>";
		$form .= "<select name='langue' id='langue' class='$cselect'>" . Select::languages($computer->getLangue()) . "</select>";
		$form .= "</div>";
		$form .= "</div>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='disk' class='$clabel'>Disk system</label>";
		$form .= "<input type='text' id='disk' class='$wmi $cinput' value='$disk' readonly=readonly>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='adm_pwd' class='$clabel'>Local Admin Password</label>";
		$form .= "<input type='text' name='adm_pwd' id='adm_pwd' class='$ldap $cinput' value='{$computer->getAdm_pwd()}'>";
		$form .= "</div>";
		$form .= "</div>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='bitlocker_id' class='$clabel'>Identifiant de la clé bitlocker</label>";
		$form .= "<input readonly type='text' id='bitlocker_id' name='bitlocker_id' class='$ldap $cinput' value='{$computer->getBitlocker_id()}'>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='bitlocker_date' class='$clabel'>Date de création de la clé bitlocker</label>";
		$form .= "<input readonly type='datetime-local' id='bitlocker_date' name='bitlocker_date' class='$ldap $cinput' value='{$computer->getBitlocker_date()}'>";
		$form .= "</div>";
		$form .= "</div>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='bitlocker_password' class='$clabel'>Mot de passe de la clé bitlocker</label>";
		$form .= "<input readonly type='text' id='bitlocker_password' name='bitlocker_password' class='$ldap $cinput' value='{$computer->getBitlocker_password()}'>";
		$form .= "</div>";
		$form .= "</fieldset>";

		/**
		 * Fieldset contenant les informations sur la RAM
		 */
		$form .= "<fieldset><legend>RAM</legend>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='ram_max' class='$clabel'>Nombre d'emplacements</label>";
		$form .= "<input type='text' id='slot_ram' name='slot_ram' class='$wmi $cinput' value='{$computer->getSlot_ram()}'>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='ram' class='$clabel'>Installée (Go)</label>";
		$form .= "<input type='text' id='ram' name='ram' class='$wmi $cinput' value='{$computer->getRam()}'>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='ram_max' class='$clabel'>Maximale (Go)</label>";
		$form .= "<input type='text' id='ram_max' name='ram_max' class='$wmi $cinput' value='{$computer->getRam_max()}'>";
		$form .= "</div>";
		$form .= "</div>";
		$form .= "<div class='$celement'>";
		$form .= "<textarea id='text_ram' name='ram_txt' cols='30' class='$wmi $ctextarea'>{$computer->getRam_txt()}</textarea>";
		$form .= "</div>";
		$form .= "</fieldset>";

		/**
		 * Fieldset contenant les informations sur l'achat
		 */
		$form .= "<fieldset><legend>Achat</legend>";
		$form .= "<div class='$cgroup'>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='date_achat' class='$clabel'>Date</label>";
		$form .= "<input type='date' id='date_achat' name='date_achat' class='$cdate' value='". $computer->getDate_achat() ."'>";
		$form .= "</div>";
		$form .= "<div class='$celement'>";
		$form .= "<label for='type_achat' class='$clabel'>Type</label>";
		$form .= "<select id='type_achat' name='type_achat' class='$cselect'>" . Select::acquisitionTypes(findSavedFilter: false, value: $computer->getType_achat()) . "</select>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='duree_loc' class='$clabel'>Durée location (en années)</label>";
		$form .= "<input type='text' id='duree_loc' name='duree_loc' class='$cinput' value='{$computer->getDuree_loc()}'>";
		$form .= "</div>";
		$form .= "<div class='$celement_extended'>";
		$form .= "<label for='num_immo' class='$clabel'>N° immobilisation</label>";
		$form .= "<input type='text' id='num_immo' name='num_immo' class='$cinput' value='". $computer->getNum_immo() ."'>";
		$form .= "</div>";
		$form .= "</div>";
		$form .= "</fieldset>";

		return $form . "</form>";
	}
}
