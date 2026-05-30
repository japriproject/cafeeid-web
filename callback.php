<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

$env_file = __DIR__ . '/.env';
if (is_file($env_file)) {
    foreach (file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

$log_dir = __DIR__ . '/application/logs';
$log_file = $log_dir . '/callback-' . date('Y-m-d') . '.log';

function callback_log($message)
{
    global $log_file;
    @file_put_contents($log_file, '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n", FILE_APPEND);
}

function json_out($status, $message, $http_code = 200, $extra = array())
{
    http_response_code($http_code);
    echo json_encode(array_merge(array(
        'status' => (int)$status,
        'message' => $message,
    ), $extra));
    exit;
}

function rupiah($nominal)
{
    return 'Rp ' . number_format((int)$nominal, 0, ',', '.');
}

function read_message($raw)
{
    $payload = json_decode($raw, true);
    if (is_array($payload)) {
        foreach (array('message', 'text', 'body', 'notification_text', 'content', 'title') as $key) {
            if (!empty($payload[$key])) {
                return (string)$payload[$key];
            }
        }
    }

    foreach (array('message', 'text', 'body', 'notification_text', 'content', 'title') as $key) {
        if (isset($_POST[$key]) && $_POST[$key] !== '') {
            return (string)$_POST[$key];
        }
        if (isset($_GET[$key]) && $_GET[$key] !== '') {
            return (string)$_GET[$key];
        }
    }

    $raw = trim((string)$raw);
    if ($raw !== '' && strpos($raw, '=') !== false) {
        parse_str($raw, $form);
        foreach (array('message', 'text', 'body', 'notification_text', 'content', 'title') as $key) {
            if (!empty($form[$key])) {
                return (string)$form[$key];
            }
        }
    }

    return $raw;
}

function extract_nominal($message)
{
    if (preg_match('/(?:Rp\.?|IDR)\s*([\d\.,]+)/i', $message, $matches)) {
        return (int)str_replace(array('.', ','), '', $matches[1]);
    }

    if (preg_match('/(?:masuk|transfer|sebesar|total)\s+([\d\.,]+)/i', $message, $matches)) {
        return (int)str_replace(array('.', ','), '', $matches[1]);
    }

    return 0;
}

function send_telegram($chat_id, $message)
{
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    if (stripos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        return false;
    }

    $token = getenv('CAFEEID_TELEGRAM_BOT_TOKEN');
    if (empty($token) || empty($chat_id) || empty($message) || !function_exists('curl_init')) {
        return false;
    }

    $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendMessage');
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(array(
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        )),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_SSL_VERIFYPEER => false,
    ));
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

$prefix = isset($_GET['prefix']) ? strtoupper(trim((string)$_GET['prefix'])) : '';
callback_log('Callback standalone masuk. Prefix: ' . ($prefix !== '' ? $prefix : 'KOSONG'));

if ($prefix === '') {
    json_out(0, 'Parameter query ?prefix=KODE_KAFE wajib disertakan.', 400);
}

$secret = trim((string)getenv('CAFEEID_CALLBACK_SECRET'));
if ($secret !== '') {
    $provided = isset($_GET['key']) ? (string)$_GET['key'] : '';
    if ($provided === '' && isset($_SERVER['HTTP_X_CALLBACK_KEY'])) {
        $provided = (string)$_SERVER['HTTP_X_CALLBACK_KEY'];
    }
    if (!hash_equals($secret, $provided)) {
        callback_log('Callback ditolak: key tidak valid.');
        json_out(0, 'Callback key tidak valid.', 403);
    }
}

$conn = @new mysqli(
    getenv('CAFEEID_DB_HOST') ?: 'localhost',
    getenv('CAFEEID_DB_USER') ?: 'root',
    getenv('CAFEEID_DB_PASS') ?: '',
    getenv('CAFEEID_DB_NAME') ?: 'cafee'
);

if ($conn->connect_error) {
    callback_log('DB error: ' . $conn->connect_error);
    json_out(0, 'Koneksi database gagal.', 500);
}

$conn->set_charset('utf8');

$stmt = $conn->prepare('SELECT * FROM cafes WHERE UPPER(prefix_invoice) = ? LIMIT 1');
$stmt->bind_param('s', $prefix);
$stmt->execute();
$cafe = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cafe) {
    callback_log('Kafe tidak ditemukan untuk prefix: ' . $prefix);
    json_out(0, "Kafe dengan prefix '{$prefix}' tidak terdaftar.", 404);
}

$raw = file_get_contents('php://input');
$message = read_message($raw);
callback_log('Method: ' . ($_SERVER['REQUEST_METHOD'] ?? '-') . ', Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? ''));
callback_log('Payload mentah: ' . $raw);
callback_log('Message terbaca: ' . ($message !== '' ? $message : 'KOSONG'));

if ($message === '') {
    json_out(0, 'Payload mutasi kosong.', 400);
}

$nominal = extract_nominal($message);
callback_log('Nominal terbaca: ' . $nominal);

if ($nominal <= 0) {
    json_out(0, 'Gagal mendeteksi nominal angka transfer.', 400);
}

$id_cafe = (int)$cafe['id_cafe'];
$invoice_hint = isset($_GET['invoice']) ? trim((string)$_GET['invoice']) : '';

if ($invoice_hint !== '') {
    $stmt = $conn->prepare('SELECT * FROM transaksi WHERE invoice = ? AND price = ? AND id_cafe = ? AND status = 0 LIMIT 1');
    $stmt->bind_param('sii', $invoice_hint, $nominal, $id_cafe);
} else {
    $stmt = $conn->prepare('SELECT * FROM transaksi WHERE price = ? AND id_cafe = ? AND status = 0 ORDER BY created_at ASC LIMIT 1');
    $stmt->bind_param('ii', $nominal, $id_cafe);
}

$stmt->execute();
$trx = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$trx) {
    callback_log('Transaksi pending tidak ditemukan. Cafe: ' . $id_cafe . ', nominal: ' . $nominal);
    $pending = $conn->prepare('SELECT invoice, price, created_at FROM transaksi WHERE id_cafe = ? AND status = 0 ORDER BY created_at DESC LIMIT 5');
    $pending->bind_param('i', $id_cafe);
    $pending->execute();
    $rows = $pending->get_result();
    while ($row = $rows->fetch_assoc()) {
        callback_log('Pending kandidat: ' . $row['invoice'] . ' / ' . $row['price'] . ' / ' . $row['created_at']);
    }
    $pending->close();

    json_out(0, 'Invoice pending nominal ' . rupiah($nominal) . ' tidak ditemukan.', 404, array(
        'nominal' => $nominal,
        'id_cafe' => $id_cafe,
    ));
}

$invoice = $trx['invoice'];
$upd = $conn->prepare('UPDATE transaksi SET status = 1, status_update = 1 WHERE invoice = ? AND status = 0');
$upd->bind_param('s', $invoice);
$upd->execute();
$affected = $upd->affected_rows;
$upd->close();

if ($affected < 1) {
    callback_log('Invoice sudah tidak pending atau gagal update: ' . $invoice);
    json_out(0, 'Invoice sudah tidak pending atau gagal update.', 409, array('invoice' => $invoice));
}

callback_log('Callback sukses. Invoice: ' . $invoice);

ignore_user_abort(true);
if (function_exists('fastcgi_finish_request')) {
    echo json_encode(array(
        'status' => 1,
        'message' => 'Status terupdate paid.',
        'invoice' => $invoice,
        'nominal' => $nominal,
    ));
    fastcgi_finish_request();
}

$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'cafeeid.japrime.id';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'https';
$invoice_url = $scheme . '://' . $host . '/invoice/' . rawurlencode($invoice);

$telegram_message = "<b>PEMBAYARAN LUNAS (AUTOMATIC)</b>\n";
$telegram_message .= "=============================\n";
$telegram_message .= "<b>Kafe:</b> " . htmlspecialchars($cafe['cafe_name'], ENT_QUOTES, 'UTF-8') . "\n";
$telegram_message .= "<b>Invoice:</b> <code>" . htmlspecialchars($invoice, ENT_QUOTES, 'UTF-8') . "</code>\n";
$telegram_message .= "=============================\n";
$telegram_message .= "<b>Rincian Pesanan:</b>\n" . htmlspecialchars($trx['desc'], ENT_QUOTES, 'UTF-8') . "\n";
$telegram_message .= "<b>Total Bayar:</b> " . rupiah($nominal) . "\n";
$telegram_message .= "=============================\n\n";
$telegram_message .= "<b>Tautan Struk Pembeli:</b>\n" . $invoice_url;

foreach (array('id_telegram_owner', 'id_telegram_kasir', 'id_telegram_dapur') as $field) {
    if (!empty($cafe[$field])) {
        send_telegram($cafe[$field], $telegram_message);
    }
}

$member_reff = isset($trx['members']) ? trim((string)$trx['members']) : '';
if ($member_reff !== '') {
    $member_stmt = $conn->prepare('SELECT upline FROM members WHERE reff = ? LIMIT 1');
    $member_stmt->bind_param('s', $member_reff);
    $member_stmt->execute();
    $member = $member_stmt->get_result()->fetch_assoc();
    $member_stmt->close();

    if ($member && !empty($member['upline'])) {
        $is_reservation = isset($trx['order_type']) && $trx['order_type'] === 'reservation';
        $bonus_l1 = $is_reservation ? 1000 : 100;
        $bonus_l2 = $is_reservation ? 500 : 50;
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $bonus_inv_l1 = 'BONUS1-' . $invoice;
        $desc_l1 = 'Bonus Lvl 1 dari belanja downline ' . $member_reff;
        $ins = $conn->prepare("INSERT IGNORE INTO transaksi (invoice, id_cafe, members, product, sale, price, admin, status, status_update, type, `desc`, created_at, date) VALUES (?, ?, ?, 'Bonus Affiliate Lvl 1', 0, ?, 0, 1, 0, 2, ?, ?, ?)");
        $ins->bind_param('sisisss', $bonus_inv_l1, $id_cafe, $member['upline'], $bonus_l1, $desc_l1, $now, $today);
        $ins->execute();
        $ins->close();

        $l2_stmt = $conn->prepare('SELECT upline FROM members WHERE reff = ? LIMIT 1');
        $l2_stmt->bind_param('s', $member['upline']);
        $l2_stmt->execute();
        $l2 = $l2_stmt->get_result()->fetch_assoc();
        $l2_stmt->close();

        if ($l2 && !empty($l2['upline'])) {
            $bonus_inv_l2 = 'BONUS2-' . $invoice;
            $desc_l2 = 'Bonus Lvl 2 dari cucu downline ' . $member_reff;
            $ins = $conn->prepare("INSERT IGNORE INTO transaksi (invoice, id_cafe, members, product, sale, price, admin, status, status_update, type, `desc`, created_at, date) VALUES (?, ?, ?, 'Bonus Affiliate Lvl 2', 0, ?, 0, 1, 0, 2, ?, ?, ?)");
            $ins->bind_param('sisisss', $bonus_inv_l2, $id_cafe, $l2['upline'], $bonus_l2, $desc_l2, $now, $today);
            $ins->execute();
            $ins->close();
        }
    }
}

json_out(1, 'Status terupdate paid dan komisi referral selesai diproses.', 200, array(
    'invoice' => $invoice,
    'nominal' => $nominal,
));
