<?php 

class Dbconnection {

    function connect(){
      $servername = "localhost:3308";
			$username = "root";
			$password = "InterActive2323";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=inact_devicedata", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected successfully";
            return $conn;
        } catch(PDOException $e) {
            // echo "Connection failed: " . $e->getMessage();
        }
    }
}