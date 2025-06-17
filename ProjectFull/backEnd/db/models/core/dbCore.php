<?php


class DataBaseCore{

    
    private $host = 'localhost';
    private $user = 'user123';
    private $pass = 'password123';
    private $db   = 'bitByte';
    private $port = 3306;


    protected $isConnectedToDb = false;
    protected $conn = null;

    function setPatameters($host, $port, $user, $passwd, $db) {
        $this->host = $host;
        $this->user = $user;
        $this->passwd = $passwd;
        $this->port = $port;
    }

    function connectToDatabase() {
        // Crea connessione usando le proprietà della classe
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db, $this->port);

        // Controlla la connessione
        if ($this->conn->connect_error) {
            $this->isConnectedToDb = false;
            return 1;  // Connessione fallita
        } else {
            $this->isConnectedToDb = true;
            return 0;  // Connessione riuscita
        }
    }

    // Metodo per verificare lo stato della connessione
    function isConnected() {
        return $this->isConnectedToDb;
    }

    function closeConnectionToDatabase() {

        $this -> conn -> close();

    }

}
?>
