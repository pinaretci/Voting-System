<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");

// Veritabanı Bağlantısı (PDO Güvenli Yöntem)
$host = "localhost";
$db_name = "oy_sistemi";
$username = "root";
$password = "";

try {
    $db = new PDO("mysql:host={$host};dbname={$db_name};charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception) {
    echo json_encode(["status" => "error", "message" => "Bağlantı hatası: " . $exception->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// 1. READ (Listeleme - GET)
if ($method === 'GET' && $action === 'listele') {
    $query = "SELECT * FROM adaylar ORDER BY oy_sayisi DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $adaylar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($adaylar); // JSON formatında çıktı
    exit();
}

// 2. CREATE (Ekleme - POST)
if ($method === 'POST' && $action === 'ekle') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($data['aday_adi'])) {
        // GÜVENLİK ÖNLEMİ 1: Input Sanitization (XSS Temizliği)
        $aday_adi = htmlspecialchars(strip_tags(trim($data['aday_adi'])));
        
        // GÜVENLİK ÖNLEMİ 2: Prepared Statement (SQL Injection Engelleme)
        $query = "INSERT INTO adaylar (aday_adi, oy_sayisi) VALUES (:aday_adi, 0)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':aday_adi', $aday_adi);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Aday başarıyla eklendi."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Aday eklenemedi."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Lütfen boş bırakmayın."]);
    }
    exit();
}

// 3. UPDATE (Oy Verme / Güncelleme - POST)
if ($method === 'POST' && $action === 'oyver') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($data['id'])) {
        $id = (int)$data['id']; // Tür dönüşümü ile güvenlik sağlama
        
        $query = "UPDATE adaylar SET oy_sayisi = oy_sayisi + 1 WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Oy kaydedildi."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Oy verilemedi."]);
        }
    }
    exit();
}

// 4. DELETE (Silme - POST)
if ($method === 'POST' && $action === 'sil') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($data['id'])) {
        $id = (int)$data['id'];
        
        $query = "DELETE FROM adaylar WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Aday silindi."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Aday silinemedi."]);
        }
    }
    exit();
}
?>