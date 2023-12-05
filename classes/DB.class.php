<?php

/**
 * Cette classe contient les méthodes liées à la base de données
 */
final class DB {

	/**
     * Retourne la valeur d'un ou plusieurs champs d'une table.
     * Si plusieurs champs, les valeurs sont séparées par des espaces dans la chaine.
     * Si pas de résultat : retourne une chaine vide
     *
     * @param String $table : table où se trouvent les champs recherchés
     * @param String $champ : 1 ou plusieurs champs séparés par un espace
     * @param String $where_col : champ de $table où se fait la recherche
     * @param String $where_val : val recherchée
     * @return String $result
     */
    static function findValueInTable($table, $champ, $where_col, $where_val): string
    {
        $result = "0";
        // champs recherchés
        $tab_champs = explode(" ", $champ);
        if (!empty($tab_champs)) {
            // plusieurs champs : $champ = chaine de car de tous les champs séparée par ','
            $champ = implode(",", $tab_champs);
        }
        $requete = "SELECT " . $champ . " FROM " . $table . " WHERE " . $where_col . " =:key";
        $sql = connectPdo()->prepare($requete);
        $sql->bindValue(':key', $where_val, PDO::PARAM_STR);
        $sql->execute();
        $data = $sql->fetch(PDO::FETCH_NUM); //tab data indexé par num de la colonne
        $sql->closeCursor();
        if ($data) {
            if (count($data) > 1) {
                $result = implode(" ", $data);
            } else {
                $result = $data[0];
            }
        }
        return $result;
    }

    /**
     * Retourne le nombre d'occurences d'une valeur dans une table
     * 
     * @param string $table
     * @param string $column
     * @return int
     */
    static function count(string $table, string $column, string $id): int
    {
        $query = "SELECT COUNT(*) as nb FROM $table WHERE $column = $id";
        $statement = connectPdo()->prepare($query);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $nb = $statement->fetch()['nb'];
        $statement->closeCursor();
        return $nb;
    }


}