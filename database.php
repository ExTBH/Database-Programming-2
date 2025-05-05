<?php
require_once __DIR__ . '/config.php';

class Database
{
    /**
     * @var Database
     */
    protected static $_dbInstance = null;

    /**
     * @var PDO
     */
    protected $_dbHandle;

    /**
     * @return Database
     */
    public static function getInstance()
    {

        if (self::$_dbInstance === null) { //checks if the PDO exists
            // creates new instance if not, sending in connection info
            self::$_dbInstance = new self(DB_USER, DB_PASS, DB_HOST, DB_NAME);
        }

        return self::$_dbInstance;
    }

    private function __construct($username, $password, $host, $database)
    {
        try {
    $this->_dbHandle = new PDO(
        "mysql:host=$host;dbname=$database;port=3306", 
        $username, 
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        return $this->_dbHandle; // returns the PDO handle to be used                                        elsewhere
    }

    public function __destruct()
    {
        $this->_dbHandle = null; // destroys the PDO handle when nolonger needed                                        longer needed
    }
}
