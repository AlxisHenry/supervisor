<?php

final class Software
{

	private $assignmentType;
	private ?string $caption;
	private ?string $description;
	private ?string $identifyingNumber;
	private ?string $installDate;
	private ?string $installDate2;
	private ?string $installLocation;
	private $installState;
	private ?string $helpLink;
	private ?string $helpTelephone;
	private ?string $installSource;
	private ?string $language;
	private ?string $localPackage;
	private ?string $name;
	private ?string $packageCache;
	private ?string $packageCode;
	private ?string $packageName;
	private ?string $productID;
	private ?string $regOwner;
	private ?string $regCompany;
	private ?string $SKUNumber;
	private ?string $transforms;
	private ?string $URLInfoAbout;
	private ?string $URLUpdateInfo;
	private ?string $vendor;
	private $wordCount;
	private ?string $version;

	public function __construct(object $software)
	{
		$this->assignmentType = $software->AssignmentType;
		$this->caption = $software->Caption;
		$this->description = $software->Description;
		$this->identifyingNumber = $software->IdentifyingNumber;
		$this->setInstallDate($software->InstallDate);
		$this->installDate2 = $software->InstallDate2;
		$this->installLocation = $software->InstallLocation;
		$this->installState = $software->InstallState;
		$this->helpLink = $software->HelpLink;
		$this->helpTelephone = $software->HelpTelephone;
		$this->installSource = $software->InstallSource;
		$this->setLanguage($software->Language);
		$this->localPackage = $software->LocalPackage;
		$this->name = $software->Name;
		$this->packageCache = $software->PackageCache;
		$this->packageCode = $software->PackageCode;
		$this->packageName = $software->PackageName;
		$this->productID = $software->ProductID;
		$this->regOwner = $software->RegOwner;
		$this->regCompany = $software->RegCompany;
		$this->SKUNumber = $software->SKUNumber;
		$this->transforms = $software->Transforms;
		$this->URLInfoAbout = $software->URLInfoAbout;
		$this->URLUpdateInfo = $software->URLUpdateInfo;
		$this->vendor = $software->Vendor;
		$this->wordCount = $software->WordCount;
		$this->version = $software->Version;
	}

	/**
	 * Get the value of name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the value of name
	 *
	 * @return  self
	 */
	private function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get the value of installDate
	 */
	public function getInstallDate()
	{
		return $this->installDate;
	}

	/**
	 * Set the value of installDate
	 *
	 * @return  self
	 */
	private function setInstallDate($installDate)
	{
		if ($installDate === null) {
			$this->installDate = null;
		} else {
			$this->installDate = (new DateTime($installDate))->format("d/m/Y");
		}
		return $this;
	}

	/**
	 * Get the value of installLocation
	 */
	public function getInstallLocation()
	{
		return $this->installLocation;
	}

	/**
	 * Set the value of installLocation
	 *
	 * @return  self
	 */
	private function setInstallLocation($installLocation)
	{
		$this->installLocation = $installLocation;

		return $this;
	}

	/**
	 * Get the value of language
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Set the value of language
	 *
	 * @return  self
	 */
	private function setLanguage($language)
	{
		switch ($language) {
			case '1033':
				$this->language = "US";
				break;
			case '1036':
				$this->language = "FR";
				break;
			case '0':
				$this->language = "N/A";
				break;
			default:
				$this->language = $language;
				break;
		}
		return $this;
	}

	/**
	 * Get the value of vendor
	 */
	public function getVendor()
	{
		return $this->vendor;
	}

	/**
	 * Set the value of vendor
	 *
	 * @return  self
	 */
	private function setVendor($vendor)
	{
		$this->vendor = $vendor;

		return $this;
	}

	/**
	 * Get the value of version
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Set the value of version
	 *
	 * @return  self
	 */
	private function setVersion($version)
	{
		$this->version = $version;

		return $this;
	}

	/**
	 * Get the value of assignmentType
	 */
	public function getAssignmentType()
	{
		return $this->assignmentType;
	}

	/**
	 * Set the value of assignmentType
	 *
	 * @return  self
	 */
	private function setAssignmentType($assignmentType)
	{
		$this->assignmentType = $assignmentType;

		return $this;
	}

	/**
	 * Get the value of caption
	 */
	public function getCaption()
	{
		return $this->caption;
	}

	/**
	 * Set the value of caption
	 *
	 * @return  self
	 */
	private function setCaption($caption)
	{
		$this->caption = $caption;

		return $this;
	}

	/**
	 * Get the value of description
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set the value of description
	 *
	 * @return  self
	 */
	private function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get the value of identifyingNumber
	 */
	public function getIdentifyingNumber()
	{
		return $this->identifyingNumber;
	}

	/**
	 * Set the value of identifyingNumber
	 *
	 * @return  self
	 */
	private function setIdentifyingNumber($identifyingNumber)
	{
		$this->identifyingNumber = $identifyingNumber;

		return $this;
	}

	/**
	 * Get the value of installDate2
	 */
	public function getInstallDate2()
	{
		return $this->installDate2;
	}

	/**
	 * Set the value of installDate2
	 *
	 * @return  self
	 */
	private function setInstallDate2($installDate2)
	{
		$this->installDate2 = $installDate2;

		return $this;
	}

	/**
	 * Get the value of installState
	 */
	public function getInstallState()
	{
		return $this->installState;
	}

	/**
	 * Set the value of installState
	 *
	 * @return  self
	 */
	private function setInstallState($installState)
	{
		$this->installState = $installState;

		return $this;
	}

	/**
	 * Get the value of helpLink
	 */
	public function getHelpLink()
	{
		return $this->helpLink;
	}

	/**
	 * Set the value of helpLink
	 *
	 * @return  self
	 */
	private function setHelpLink($helpLink)
	{
		$this->helpLink = $helpLink;

		return $this;
	}

	/**
	 * Get the value of helpTelephone
	 */
	public function getHelpTelephone()
	{
		return $this->helpTelephone;
	}

	/**
	 * Set the value of helpTelephone
	 *
	 * @return  self
	 */
	private function setHelpTelephone($helpTelephone)
	{
		$this->helpTelephone = $helpTelephone;

		return $this;
	}

	/**
	 * Get the value of installSource
	 */
	public function getInstallSource()
	{
		return $this->installSource;
	}

	/**
	 * Set the value of installSource
	 *
	 * @return  self
	 */
	private function setInstallSource($installSource)
	{
		$this->installSource = $installSource;

		return $this;
	}

	/**
	 * Get the value of localPackage
	 */
	public function getLocalPackage()
	{
		return $this->localPackage;
	}

	/**
	 * Set the value of localPackage
	 *
	 * @return  self
	 */
	private function setLocalPackage($localPackage)
	{
		$this->localPackage = $localPackage;

		return $this;
	}

	/**
	 * Get the value of packageCache
	 */
	public function getPackageCache()
	{
		return $this->packageCache;
	}

	/**
	 * Set the value of packageCache
	 *
	 * @return  self
	 */
	private function setPackageCache($packageCache)
	{
		$this->packageCache = $packageCache;

		return $this;
	}

	/**
	 * Get the value of packageCode
	 */
	public function getPackageCode()
	{
		return $this->packageCode;
	}

	/**
	 * Set the value of packageCode
	 *
	 * @return  self
	 */
	private function setPackageCode($packageCode)
	{
		$this->packageCode = $packageCode;

		return $this;
	}

	/**
	 * Get the value of packageName
	 */
	public function getPackageName()
	{
		return $this->packageName;
	}

	/**
	 * Set the value of packageName
	 *
	 * @return  self
	 */
	private function setPackageName($packageName)
	{
		$this->packageName = $packageName;

		return $this;
	}

	/**
	 * Get the value of productID
	 */
	public function getProductID()
	{
		return $this->productID;
	}

	/**
	 * Set the value of productID
	 *
	 * @return  self
	 */
	private function setProductID($productID)
	{
		$this->productID = $productID;

		return $this;
	}

	/**
	 * Get the value of regOwner
	 */
	public function getRegOwner()
	{
		return $this->regOwner;
	}

	/**
	 * Set the value of regOwner
	 *
	 * @return  self
	 */
	private function setRegOwner($regOwner)
	{
		$this->regOwner = $regOwner;

		return $this;
	}

	/**
	 * Get the value of regCompany
	 */
	public function getRegCompany()
	{
		return $this->regCompany;
	}

	/**
	 * Set the value of regCompany
	 *
	 * @return  self
	 */
	private function setRegCompany($regCompany)
	{
		$this->regCompany = $regCompany;

		return $this;
	}

	/**
	 * Get the value of SKUNumber
	 */
	public function getSKUNumber()
	{
		return $this->SKUNumber;
	}

	/**
	 * Set the value of SKUNumber
	 *
	 * @return  self
	 */
	private function setSKUNumber($SKUNumber)
	{
		$this->SKUNumber = $SKUNumber;

		return $this;
	}

	/**
	 * Get the value of transforms
	 */
	public function getTransforms()
	{
		return $this->transforms;
	}

	/**
	 * Set the value of transforms
	 *
	 * @return  self
	 */
	private function setTransforms($transforms)
	{
		$this->transforms = $transforms;

		return $this;
	}

	/**
	 * Get the value of URLInfoAbout
	 */
	public function getURLInfoAbout()
	{
		return $this->URLInfoAbout;
	}

	/**
	 * Set the value of URLInfoAbout
	 *
	 * @return  self
	 */
	private function setURLInfoAbout($URLInfoAbout)
	{
		$this->URLInfoAbout = $URLInfoAbout;

		return $this;
	}

	/**
	 * Get the value of URLUpdateInfo
	 */
	public function getURLUpdateInfo()
	{
		return $this->URLUpdateInfo;
	}

	/**
	 * Set the value of URLUpdateInfo
	 *
	 * @return  self
	 */
	private function setURLUpdateInfo($URLUpdateInfo)
	{
		$this->URLUpdateInfo = $URLUpdateInfo;

		return $this;
	}

	/**
	 * Get the value of wordCount
	 */
	public function getWordCount()
	{
		return $this->wordCount;
	}

	/**
	 * Set the value of wordCount
	 *
	 * @return  self
	 */
	private function setWordCount($wordCount)
	{
		$this->wordCount = $wordCount;

		return $this;
	}
}
