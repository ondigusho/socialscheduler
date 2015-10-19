<?php
/*
 * Data Base connection class with MySql
 * Will handle database connections.
 * 
 * @copyright Ondi Gusho.  
*/
class DbConn{
    //Set connection variables. 
    var $DBServer = 'localhost';
    var $DBUser   = 'root';
    var $DBPass   = 'password';
    var $DBName   = 'ebkw';
    var $conn=NULL;
    // Instance variable.
    private static $instance = NULL;
    // Constructor . Connection to DB.
    private function __construct(){
        // Connections
        $conn = new mysqli($this->DBServer, $this->DBUser, $this->DBPass, $this->DBName);
       // check connection
        if ($conn->connect_error) {
          trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
        }
        //Set connection variable.
        $this->conn = $conn;
    }
    
    //Set connection.
    //Return reference
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
        }
        // Return instance of class.
        return self::$instance;
    }
    // Close connection
    function close(){
        $this->conn->close();
    }
    
}
?>