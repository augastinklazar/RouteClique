

<?php

class DB{

    private static function connect(){
        $pdo = new PDO('mysql:host=127.0.0.1; dbname=Routeclique; charset=utf8mb4', 'root', '');
        // PDO-php data objects... provides a data-access abstraction layer. i.e. regardless of the database used.... u use same fns to issue queries and fetch data.

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }//can only be accessed from this class

    public static function query($query, $params = array()){
        $statement = self::connect()->prepare($query);
        $statement->execute($params);

        if(explode(' ', $query)[0] == 'SELECT'){
            $data = $statement->fetchAll();
            return $data;
        }
    }
}
?>
