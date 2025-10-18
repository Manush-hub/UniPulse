<?php

trait Database
{

    protected function connect()
    {
        try {
            $string = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $conn = new PDO($string, DBUSER, DBPASS, $options);
            return $conn;
        } catch (PDOException $e) {
            if (DEBUG) {
                die("Database connection failed: " . $e->getMessage() .
                    "<br>Host: " . DBHOST .
                    "<br>Port: " . DBPORT .
                    "<br>Database: " . DBNAME .
                    "<br>User: " . DBUSER);
            } else {
                die("Database connection failed. Please contact the administrator.");
            }
        }
    }

    public function query($query, $data = [])
    {

        $conn = $this->connect();
        $stm = $conn->prepare($query);

        $check = $stm->execute($data);
        if ($check) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);
            if (is_array($result) && count($result)) {
                return $result;
            }
        }

        return false;
    }

    public function getRow($query, $data = [])
    {

        $conn = $this->connect();
        $stm = $conn->prepare($query);

        $check = $stm->execute($data);
        if ($check) {
            $result = $stm->fetchAll(PDO::FETCH_OBJ);
            if (is_array($result) && count($result)) {
                return $result[0];
            }
        }

        return false;
    }
}



// $string = "mysql:hostname=localhost;dbname=my_db";
// $con = new PDO($string,'root','');

// show($conn);
