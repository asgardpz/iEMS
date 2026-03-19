<?php
// Server.php
// 注意：API 路徑以 /api/... 判斷，回傳 JSON；否則顯示 UI 表格

$host = "127.0.0.1";
$db   = "EMS";
$user = "root";
$pass = "iBASE@iEMS";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

$input = json_decode(file_get_contents("php://input"), true);
$path = $_SERVER["REQUEST_URI"];
$response = [];

if ($input) {
    switch (true) {
        case strpos($path, "/api/login/rfid") !== false:
            $stmt = $pdo->prepare("INSERT INTO rfid_cards (card_uid, user_id, status, last_used_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$input["card_uid"], $input["user_id"] ?? null, $input["status"] ?? 'active']);
            $response = [
                "user_id" => $input["user_id"] ?? null,
                "name" => $input["name"] ?? "未知",
                "card_uid" => $input["card_uid"],
                "device_id" => $input["device_id"],
                "status" => $input["status"] ?? "active",
                "token" => uniqid("rfid-")
            ];
            break;

        case strpos($path, "/api/login/qrcode/member") !== false:
            $response = [
                "user_id" => rand(100,999),
                "name" => $input["name"] ?? "會員",
                "device_id" => $input["device_id"],
                "token" => uniqid("qr-")
            ];
            break;

        case strpos($path, "/api/stations") !== false:
            $stmt = $pdo->query("SELECT * FROM stations");
            $response = $stmt->fetchAll();
            break;

        case strpos($path, "/api/devices") !== false:
            $stmt = $pdo->prepare("SELECT * FROM devices WHERE device_id = ?");
            $stmt->execute([$input["device_id"]]);
            $response = $stmt->fetch();
            break;

        case strpos($path, "/api/sessions/start") !== false:
            $stmt = $pdo->prepare("INSERT INTO charging_sessions (session_id, device_id, start_at, status) VALUES (?, ?, NOW(), 'active')");
            $stmt->execute([$input["session_id"], $input["device_id"]]);
            $response = ["session_id" => $input["session_id"], "status" => "started"];
            break;

        case strpos($path, "/api/sessions/end") !== false:
            $stmt = $pdo->prepare("UPDATE charging_sessions SET end_at = NOW(), status = 'finished' WHERE session_id = ?");
            $stmt->execute([$input["session_id"]]);
            $response = ["session_id" => $input["session_id"], "status" => "finished"];
            break;

        case strpos($path, "/api/payments") !== false:
            $stmt = $pdo->prepare("INSERT INTO payments (session_id, amount, method) VALUES (?, ?, ?)");
            $stmt->execute([$input["session_id"], $input["amount"], $input["method"]]);
            $response = ["payment_id" => $pdo->lastInsertId(), "status" => "success"];
            break;

        case strpos($path, "/api/alerts") !== false:
            $stmt = $pdo->prepare("INSERT INTO alerts (device_id, station_id, alert_type, severity) VALUES (?, ?, ?, ?)");
            $stmt->execute([$input["device_id"], $input["station_id"], $input["alert_type"], $input["severity"]]);
            $response = ["alert_id" => $pdo->lastInsertId(), "status" => "logged"];
            break;

        case strpos($path, "/api/firmware/catalog") !== false:
            $stmt = $pdo->query("SELECT * FROM firmware_catalog");
            $response = $stmt->fetchAll();
            break;

        default:
            $response = ["error" => "Unknown API endpoint"];
    }

    header("Content-Type: application/json");
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// === UI 顯示區 ===
header("Content-Type: text/html; charset=utf-8");
echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Server UI</title>
<style>body{font-family:sans-serif;padding:20px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ccc;padding:8px;}th{background:#eee;}</style>
</head><body>";

echo "<h1>Server API UI</h1>";

function showTable($pdo, $sql, $title) {
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();
    echo "<h2>$title</h2><table>";
    if ($rows) {
        echo "<tr>";
        foreach (array_keys($rows[0]) as $col) {
            echo "<th>$col</th>";
        }
        echo "</tr>";
        foreach ($rows as $row) {
            echo "<tr>";
            foreach ($row as $val) {
                echo "<td>".htmlspecialchars($val)."</td>";
            }
            echo "</tr>";
        }
    }
    echo "</table>";
}

showTable($pdo, "SELECT id, code, name, city, status FROM stations ORDER BY id DESC LIMIT 10", "Stations 資料表");
showTable($pdo, "SELECT id, device_id, type, status, last_online_at FROM devices ORDER BY id DESC LIMIT 10", "Devices 資料表");
showTable($pdo, "SELECT id, session_id, device_id, start_at, end_at, status FROM charging_sessions ORDER BY id DESC LIMIT 10", "Charging Sessions");
showTable($pdo, "SELECT id, session_id, amount, method, transaction_time FROM payments ORDER BY id DESC LIMIT 10", "Payments");
showTable($pdo, "SELECT id, device_id, station_id, alert_type, severity, occurred_at FROM alerts ORDER BY id DESC LIMIT 10", "Alerts");
showTable($pdo, "SELECT id, version, device_type, url, released_at, is_latest FROM firmware_catalog ORDER BY id DESC LIMIT 10", "Firmware Catalog");

echo "</body></html>";
