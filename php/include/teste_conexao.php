<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=vpn_db;charset=utf8mb4", "vpn_user", "123456");
    echo "✅ Conectado com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao conectar: " . $e->getMessage();
}
