<?php
Class DbConnect {
	private $conn;

	public function __construct(){
	
	}	
	function Connect(){

		require_once(PHP_INCLUDES.'config.php');
		try {
		    $this->conn = new PDO(DBMS.":host=".DB_HOST."; dbname=".DB_NAME,DB_USER,DB_PASS);
		} catch (PDOException $e) {
		    print "Error!: " . $e->getMessage() . "<br/>";
		    die();
		}
		return $this->conn;
	}

	public function __destruct(){
		$this->conn = null;
	}
}
?>