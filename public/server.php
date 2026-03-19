<?php
// server.php — 完整修正版

//-0 關閉錯誤輸出到瀏覽器，只留 JSON
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ERROR | E_PARSE);

//-1 DB 連線設定
$host = "127.0.0.1";
$db   = "EMS";
$user = "root";
$pass = "";
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

function out($ok, $msg = "") {
    return ["result" => $ok ? "OK" : "NG", "message" => $msg];
}

//-2 API 處理區
$input = json_decode(file_get_contents("php://input"), true);
$path = $_SERVER["REQUEST_URI"];
$response = [];

if ($input) {
    try {
        switch (true) {

            // /api/devices/{id}/status — 純 Log
case preg_match("#/api/devices/([^/]+)/status$#", $path, $m):
    $device_id = $m[1];
    $d = $input["device"];
    $t = $d["telemetry"];

    // 1) Insert log
    $stmt = $pdo->prepare("INSERT INTO device_status_history (device_id, timestamp, status, current_a, voltage_v, temperature_c, power_kw) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $device_id,
        $d["timestamp"],
        $d["status"],
        $t["current_a"],
        $t["voltage_v"],
        $t["temperature_c"],
        $t["power_kw"]
    ]);

    // 2) 同步更新 devices 表
    $stmt2 = $pdo->prepare("UPDATE devices SET status = ?, last_online_at = ? WHERE id = ?");
    $stmt2->execute([
        $d["status"],
        $d["timestamp"],
        $device_id
    ]);

    $response = out(true);
    break;

            // /api/firmware_catalog — Insert 資料
            case preg_match("#/api/firmware_catalog$#", $path):
                $stmt = $pdo->prepare("INSERT INTO firmware_catalog 
                    (version, device_type, url, release_notes, released_at, is_latest) 
                    VALUES (?, ?, ?, ?, NOW(), ?)");

                $stmt->execute([
                    $input["version"],
                    $input["device_type"],
                    $input["url"],
                    $input["release_notes"],
                    $input["is_latest"]
                ]);

                $response = out(true);
                break;


            
            // /api/devices/{id}/actions — 純 Log
            case preg_match("#/api/devices/([^/]+)/actions$#", $path, $m):
                $device_id = $m[1];
                $stmt = $pdo->prepare("INSERT INTO device_actions (device_id, action, parameters, status, triggered_at, completed_at) VALUES (?, ?, ?, ?, NOW(), NULL)");
                $stmt->execute([
                    $device_id,
                    $input["action"],
                    json_encode($input["parameters"], JSON_UNESCAPED_UNICODE),
                    $input["status"]
                ]);
                $response = out(true);
                break;

            // /api/firmware/check — 驗證型
            case strpos($path, "/api/firmware/check") !== false:
                    $stmt = $pdo->prepare("
                        SELECT is_latest FROM firmware_catalog
                        WHERE device_type = ? AND version = ?
                        LIMIT 1
                    ");
                    $stmt->execute([$input["device_type"], $input["firmware_version"]]);
                    $fw = $stmt->fetch();

                    if (!$fw || $fw["is_latest"] == 0) {
                        // 沒找到或不是最新版本 → 需要更新
                        $stmt2 = $pdo->prepare("
                            SELECT version FROM firmware_catalog
                            WHERE device_type = ? AND is_latest = 1
                            LIMIT 1
                        ");
                        $stmt2->execute([$input["device_type"]]);
                        $latest = $stmt2->fetch();
                        $msg = $latest ? "需要更新至 " . $latest["version"] : "需要更新，但找不到最新版本";
                    } else {
                        // 是最新版本
                        $msg = "已是最新版本";
                    }

                    $response = [
                        "result" => "OK",
                        "device_id" => $input["device_id"],
                        "firmware_version" => $input["firmware_version"],
                        "message" => $msg
                    ];
                    break;

            // /api/device/id — 驗證型
            case strpos($path, "/api/device/id") !== false:
                $stmt = $pdo->prepare("SELECT id FROM devices WHERE device_id = ?");
                $stmt->execute([$input["device_id"]]);
                $device = $stmt->fetch();
                $response = $device ? ["result" => "OK", "message" => $device["id"]] : out(false, "Device 不存在");
                break;

            // /api/login/rfid — 驗證型
            case strpos($path, "/api/login/rfid") !== false:
                $stmt = $pdo->prepare("SELECT users.name,users.id FROM rfid_cards JOIN users ON rfid_cards.user_id = users.id WHERE rfid_cards.card_uid = ?");
                $stmt->execute([$input["card_uid"]]);
                $user = $stmt->fetch();
                $response = $user ? ["result" => "OK", "message" => "name:{$user['name']}, id:" . (string)$user['id']] : out(false, "RFID 卡不存在");
                break;

            // /api/login/qrcode/member — 驗證型
            case strpos($path, "/api/login/qrcode/member") !== false:
                $stmt = $pdo->prepare("SELECT name FROM users WHERE email = ?");
                $stmt->execute([$input["email"]]);
                $user = $stmt->fetch();
                $response = $user ? ["result" => "OK", "message" => $user["name"]] : out(false, "Email 不存在");
                break;

            // 其他 API 保持原本 INSERT 行為
            case strpos($path, "/api/payments") !== false:
                $stmt = $pdo->prepare("INSERT INTO payments (session_id, amount, method, transaction_time) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$input["session_id"], $input["amount"], $input["method"]]);

                // 判斷 amount 是否為 0，回傳 NG，但資料已寫入
                if (floatval($input["amount"]) == 0.0) {
                    $response = out(false, "付款失敗");
                } else {
                    $response = out(true);
                }
                break;
    
            case strpos($path, "/api/stations") !== false:
                $loc = $input["location"];
                $stmt = $pdo->prepare("INSERT INTO stations (code, name, address, city, district, latitude, longitude, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input["code"], $input["name"], $input["address"],
                    $input["city"], $input["district"],
                    $loc["latitude"], $loc["longitude"],
                    $input["status"]
                ]);
                $response = out(true);
                break;

            case strpos($path, "/api/devices") !== false:
                $stmt = $pdo->prepare("INSERT INTO devices (device_id, station_id, type, firmware_version, status, last_online_at) VALUES (?, ?, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE station_id=?, type=?, firmware_version=?, status=?, last_online_at=NOW()");
                $stmt->execute([
                    $input["device_id"], $input["station_id"], $input["type"], $input["firmware_version"], $input["status"],
                    $input["station_id"], $input["type"], $input["firmware_version"], $input["status"]
                ]);
                $response = out(true);
                break;

        case strpos($path, "/api/sessions") !== false:
            $s = $input["session"];

            // 取 end_time 的日期部分
            $datePart = date("Ymd", strtotime($s["end_time"]));

            // 查詢目前這一天已經有多少筆 session_id
            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS cnt 
                FROM charging_sessions 
                WHERE session_id LIKE :prefix
            ");
            $stmt->execute([":prefix" => $datePart . "-%"]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // 流水號 = 當天已有筆數 + 1
            $seq = $row["cnt"] + 1;

            // 補足 4 位數 (00001, 00002...)
            $serial = str_pad($seq, 5, "0", STR_PAD_LEFT);

            // 組合新的 session_id
            $newSessionId = $datePart . "-" . $serial;

            // 寫入 charging_sessions
            $stmt = $pdo->prepare("
                INSERT INTO charging_sessions 
                (session_id, device_id, user_id, start_at, end_at, meter_start, meter_stop, co2_reduction_kg, status, UUID) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $newSessionId, $s["device_id"], $s["user_id"],
                $s["start_time"], $s["end_time"], $s["energy_start"], $s["energy_end"],
                $s["co2_reduction_kg"], $s["status"],$s["session_id"]
            ]);

            $response = out(true);
            break;


                    case strpos($path, "/api/alerts") !== false:
                        $stmt = $pdo->prepare("INSERT INTO alerts (device_id, station_id, alert_type, severity, occurred_at) VALUES (?, ?, ?, ?, NOW())");
                        $stmt->execute([$input["device_id"], $input["station_id"], $input["alert_type"], $input["severity"]]);
                        $response = out(true);
                        break;

                    default:
                        $response = out(false, "Unknown API endpoint");
                }
            } catch (Exception $e) {
                $response = out(false, $e->getMessage());
            }

            header("Content-Type: application/json");
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }

//-3 UI 顯示區
header("Content-Type: text/html; charset=utf-8");
echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Server UI</title>
<style>body{font-family:sans-serif;padding:20px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ccc;padding:8px;}th{background:#eee;}</style>
</head><body><h1>Server API UI</h1>";

function showTable($pdo, $sql, $title) {
    echo "<h2>" . htmlspecialchars($title) . "</h2>";
    try {
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();
        echo "<table>";
        if ($rows && count($rows) > 0) {
            echo "<tr>";
            foreach (array_keys($rows[0]) as $col) {
                echo "<th>" . htmlspecialchars($col) . "</th>";
            }
            echo "</tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $val) {
                    echo "<td>" . htmlspecialchars((string)$val) . "</td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='99'>沒有資料</td></tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<div style='color:red;'>UI 查詢錯誤: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// 顯示各資料表最新 10 筆紀錄
showTable($pdo, "SELECT * FROM device_status_history ORDER BY id DESC LIMIT 10", "Device Status History");
showTable($pdo, "SELECT * FROM device_actions ORDER BY id DESC LIMIT 10", "Device Actions");
showTable($pdo, "SELECT * FROM payments ORDER BY id DESC LIMIT 10", "Payments");
showTable($pdo, "SELECT * FROM stations ORDER BY id DESC LIMIT 10", "Stations");
showTable($pdo, "SELECT * FROM devices ORDER BY id DESC LIMIT 10", "Devices");
showTable($pdo, "SELECT * FROM charging_sessions ORDER BY id DESC LIMIT 10", "Charging Sessions");
showTable($pdo, "SELECT * FROM alerts ORDER BY id DESC LIMIT 10", "Alerts");
showTable($pdo, "SELECT * FROM firmware_catalog ORDER BY id DESC LIMIT 10", "Firmware Catalog");
showTable($pdo, "SELECT * FROM rfid_cards ORDER BY id DESC LIMIT 10", "RFID Cards");
showTable($pdo, "SELECT * FROM users ORDER BY id DESC LIMIT 10", "Users");

echo "</body></html>";
