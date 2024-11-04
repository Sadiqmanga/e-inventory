<?php
    
    // define('DB_SERVER', 'marigold');
    // define('DB_USERNAME', 'sitsngco_bash');
    // define('DB_PASSWORD', 'bash2022*#*#');
    // define('DB_NAME', 'sitsngco_amugrv');

    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'amu_v12');
    
    /* Attempt to connect to MySQL database in Mysqli*/
    $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    date_default_timezone_set('Africa/Lagos');
    
?>