<?php
$connection = env('DB_CONNECTION', 'mysql');
$host = env('DB_HOST', 'localhost');
$port = env('DB_PORT', '3306');
$username = env('DB_USERNAME', 'root');
$password = env('DB_PASSWORD', '');
$dbname = env('DB_DATABASE', 'shop');
return [
    'class' => 'yii\db\Connection',
    'dsn' => "$connection:host=$host;port=$port;dbname=$dbname",
    'username' => "$username",
    'password' => "$password",
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
