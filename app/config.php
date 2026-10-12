<?php
// Configuración de la base de datos.
// Se lee de variables de entorno (Docker) y, si no existen, usa los valores de XAMPP.
$host     = getenv('DB_HOST')     ?: 'localhost';
$dbname   = getenv('DB_NAME')     ?: 'inscripciones';
$user     = getenv('DB_USER')     ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('<h2>❌ Error de conexión a la base de datos</h2><p>' . htmlspecialchars($e->getMessage()) . '</p>');
}
