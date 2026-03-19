<?php
// Server.php
// API 路徑以 /api/... 判斷，回傳 JSON；否則顯示 UI 表格

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
        // 🔍 驗證型：RFID 登入
        case strpos($path, "/api/login/rfid") !== false:
            $stmt = $pdo->prepare("SELECT * FROM rfid_cards WHERE card_uid=?");
            $stmt->execute([$input["card_uid"]]);
            $card = $stmt->fetch();
            if ($card) {
                $response = [
                    "user_id" => $card["user_id"],
                    "name" => $input["name"] ?? "會員",
                    "card_uid" => $card["card_uid"],
                    "device_id" => $input["device_id"],
                    "status" => $card["status"],
                    "token" => uniqid("rfid-")
                ];
            } else {
                $response = [
                    "user_id" => null,
                    "name" => "Guest",
                    "card_uid" => $input["card_uid"],
                    "device_id" => $input["device_id"],
                    "status" => "guest",
                    "token" => uniqid("guest-")
                ];
            }
            break;

        // 🔍 驗證型：QR Code 登入會員
        case strpos($path, "/api/login/qrcode/member") !== false:
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND password=?");
            $stmt->execute([$input["email"], $input["password"]]);
            $user = $stmt->fetch();
            if ($user) {
                $response = [
                    "user_id" => $user["id"],
                    "name" => $user["name"],
                    "device_id" => $input["device_id"],
                    "token" => uniqid("qr-")
                ];
            } else {
                $response = ["error" => "Invalid login"];
            }
            break;

        // ✅ 寫入型：建立或更新站點
        case strpos($path, "/api/stations") !== false:
            $stmt = $pdo->prepare("INSERT INTO stations (code, name, address, city, district, latitude, longitude, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input["code"], $input["name"], $input["address"],
                $input["city"], $input["district"],
                $input["location"]["latitude"], $input["location"]["longitude"],
                $input["status"]
            ]);
            $response = ["station_id" => $pdo->lastInsertId(), "status" => "success"];
            break;

        // ✅ 寫入型：建立或更新裝置
        case strpos($path, "/api/devices") !== false:
            $stmt = $pdo->prepare("INSERT INTO devices (device_id, station_id, type, firmware_version, status, last_online_at) VALUES (?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE station_id=?, type=?, firmware_version=?, status=?, last_online_at=NOW()");
            $stmt->execute([
                $input["device_id"], $input["station_id"], $input["type"], $input["firmware_version"], $input["status"],
                $input["station_id"], $input["type"], $input["firmware_version"], $input["status"]
            ]);
            $response = ["device_id" => $input["device_id"], "status" => "updated"];
            break;

        // ✅ 寫入型：充電樁狀態
        case preg_match("#/api/devices/([0-9]+)/status$#", $path, $m):
            $stmt = $pdo->prepare("INSERT INTO device_status_history (device_id, timestamp, status, current_a, voltage_v, temperature_c, power_kw) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $m[1], $input["device"]["timestamp"], $input["device"]["status"],
                $input["device"]["telemetry"]["current_a"],
                $input["device"]["telemetry"]["voltage_v"],
                $input["device"]["telemetry"]["temperature_c"],
                $input["device"]["telemetry"]["power_kw"]
            ]);
            $response = ["device_id" => $m[1], "status" => "logged"];
            break;

        // ✅ 寫入型：充電會話
        case strpos($path, "/api/sessions") !== false:
            $stmt = $pdo->prepare("INSERT INTO charging_sessions (session_id, device_id, user_id, start_at, end_at, energy_kwh, co2_reduction_kg, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input["session"]["session_id"], $input["session"]["device_id"], $input["session"]["user_id"],
                $input["session"]["start_time"], $input["session"]["end_time"],
                $input["session"]["energy_end"], $input["session"]["co2_reduction_kg"], $input["session"]["status"]
            ]);
            $response = ["session_id" => $input["session"]["session_id"], "status" => "success"];
            break;

        // ✅ 寫入型：付款紀錄
        case strpos($path, "/api/payments") !== false:
            $stmt = $pdo->prepare("INSERT INTO payments (session_id, amount, method, transaction_time) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$input["session_id"], $input["amount"], $input["method"]]);
            $response = ["payment_id" => $pdo->lastInsertId(), "status" => "success"];
            break;

        // ✅ 寫入型：警示
        case strpos($path, "/api/alerts") !== false:
            $stmt = $pdo->prepare("INSERT INTO alerts (device_id, station_id, alert_type, severity, occurred_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$input["device_id"], $input["station_id"], $input["alert_type"], $input["severity"]]);
            $response = ["alert_id" => $pdo->lastInsertId(), "status" => "logged"];
            break;

        // 🔍 驗證型：韌體檢查
        case strpos($path, "/api/firmware/check") !== false:
            $stmt = $pdo->prepare("SELECT * FROM firmware_catalog WHERE device_type=? ORDER BY released_at DESC LIMIT 1");
            $stmt->execute([$input["device_type"]]);
            $fw = $stmt->fetch();
            $response = [
                "update_required" => $fw["version"] !== $input["firmware_version"],
                "latest_version" => $fw["version"],
                "url" => $fw["url"],
                "checksum" => $fw["checksum"],
                "release_notes" => $fw["release_notes"],
                "is_latest" => $fw["is_latest"]
            ];
            break;

        // ✅ 寫入型：OTA 任務
        case preg_match("#/api/devices/([0-9]+)/actions$#", $path, $m):
            $stmt = $pdo->prepare("INSERT INTO device_actions (device_id, action, parameters, status, triggered_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $m[1], $input["action"], json_encode($input["parameters"]), $input["status"], $input["triggered_at"]
            ]);
            $response = ["action_id" => $pdo->lastInsertId(), "status" => "created"];
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
<style>
body{font-family:sans-serif;padding:20px;}
table{border-collapse:collapse;width:100%;}
th,td{border:1px solid #ccc;padding:8px;}
th{background:#eee;}
</style>
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
showTable($pdo, "SELECT id, device_id, timestamp, status, current_a, voltage_v, temperature_c, power_kw FROM device_status_history ORDER BY id DESC LIMIT 10", "Device Status History");
showTable($pdo, "SELECT id, session_id, device_id, start_at, end_at, status FROM charging_sessions ORDER BY id DESC LIMIT 10", "Charging Sessions");
showTable($pdo, "SELECT id, session_id, amount, method, transaction_time FROM payments ORDER BY id DESC LIMIT 10", "Payments");
showTable($pdo, "SELECT id, device_id, station_id, alert_type, severity, occurred_at FROM alerts ORDER BY id DESC LIMIT 10", "Alerts");
showTable($pdo, "SELECT id, device_id, action, parameters, status, triggered_at FROM device_actions ORDER BY id DESC LIMIT 10", "Device Actions");
showTable($pdo, "SELECT id, version, device_type, url, released_at, is_latest FROM firmware_catalog ORDER BY id DESC LIMIT 10", "Firmware Catalog");

echo "</body></html>";
