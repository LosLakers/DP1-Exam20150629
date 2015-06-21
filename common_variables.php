<?php
class Common {
    // maximum number of children per reservation
    private static $MAX_CHILDREN = 3;

    public static function get_max_children() {
        return self::$MAX_CHILDREN;
    }

    // database variables
    private static $HOST = "localhost";
    private static $USER = "root";
    private static $PASSWORD = "";
    private static $DATABASE = "dp1_exam";

    public static function get_db_connection() {
        return new mysqli(self::$HOST, self::$USER, self::$PASSWORD, self::$DATABASE);
    }
}

// database variables
$HOST = 'localhost';
$USER = 'root';
$PASSWORD = '';
$DATABASE = 'dp1_exam';

?>