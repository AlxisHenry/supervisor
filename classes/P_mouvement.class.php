<?php

class P_mouvement
{

	private int $id;
	private int $matos;
	private int $localisation;
	private int $user;
	private string $date;
	private string $remarque;

	public static function get(int|string $id): P_mouvement
	{
		$query = "SELECT * FROM p_mouvements WHERE id = ?";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$id]);
		$statement->setFetchMode(PDO::FETCH_CLASS, P_mouvement::class);
		$movement = $statement->fetch();
		$statement->closeCursor();
		return $movement;
	}

	public static function new(int $matos, int $localisation, int $user, string $date, string $remarque = ""): void
	{
		$movement = new P_mouvement();
		$movement->setMatos($matos)
			->setLocalisation($localisation)
			->setUser($user)
			->setDate($date)
			->setRemarque($remarque)
			->create();
	}

	public static function update(string $id, string $remarque): void
	{
		$query = "UPDATE p_mouvements SET remarque = ? WHERE id = ?";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$remarque, $id]);
		$statement->closeCursor();
	}

	public static function delete(string $id): void
	{
		$query = "DELETE FROM p_mouvements WHERE id = ?";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$id]);
		$statement->closeCursor();
	}

	public function __construct()
	{
	}

	public function create(): P_mouvement
	{
		$query = "INSERT INTO p_mouvements (matos, localisation, user, date, remarque) VALUES (?, ?, ?, ?, ?)";
		$statement = connectPdo()->prepare($query);
		$statement->execute([$this->matos, $this->localisation, $this->user, $this->date, $this->remarque]);
		$statement->closeCursor();
		return P_mouvement::get(connectPdo()->lastInsertId());
	}

	private function genereateComment(): string
	{
		return "Mouvement créé le " . date('d/m/Y');
	}


	/**
	 * Get the value of remarque
	 */
	public function getRemarque()
	{
		return $this->remarque;
	}

	/**
	 * Set the value of remarque
	 *
	 * @return  self
	 */
	public function setRemarque($remarque)
	{
		if ($remarque === "") {
			$this->remarque = $this->genereateComment();
		} else {
			$this->remarque = $remarque;
		}
		return $this;
	}

	/**
	 * Get the value of date
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * Set the value of date
	 *
	 * @return  self
	 */
	public function setDate($date)
	{
		$this->date = $date;

		return $this;
	}

	/**
	 * Get the value of user
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set the value of user
	 *
	 * @return  self
	 */
	public function setUser($user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get the value of localisation
	 */
	public function getLocalisation()
	{
		return $this->localisation;
	}

	/**
	 * Set the value of localisation
	 *
	 * @return  self
	 */
	public function setLocalisation($localisation)
	{
		$this->localisation = $localisation;

		return $this;
	}

	/**
	 * Get the value of matos
	 */
	public function getMatos()
	{
		return $this->matos;
	}

	/**
	 * Set the value of matos
	 *
	 * @return  self
	 */
	public function setMatos($matos)
	{
		$this->matos = $matos;

		return $this;
	}
}
