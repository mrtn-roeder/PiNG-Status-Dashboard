<?php
/**
 * PiNG Status - Heartbeat Receiver
 */
require_once 'config.php';

if (!isset($_GET['token']) || $_GET['token'] !== PING_TOKEN) {
    header('HTTP/1.0 403 Forbidden');
    die('Invalid token.');
}

// Sanitize device name (allows a-z, A-Z, 0-9, -, _)
$device = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['device']);

if ($device) {
    $data = [
        'time'   => time(),
        'temp'   => preg_replace('/[^0-9.]/', '', $_GET['temp']),
        'disk'   => preg_replace('/[^0-9]/', '', $_GET['disk']),
        // Allows %, /, spaces, and text for "x% RAM / x% CPU"
        'load'   => htmlspecialchars($_GET['load']), 
        'uptime' => htmlspecialchars($_GET['uptime']),
        'net'    => htmlspecialchars($_GET['net'] ?? 'n/a')
    ];

    if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0777, true);
    file_put_contents(DATA_DIR . "/{$device}.json", json_encode($data));
    echo "OK: $device";
}
?>