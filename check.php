<?php
/**
 * PiNG Status - Watchdog
 * Run this file via cronjob on your web server to check for offline devices.
 */
require_once 'config.php';

$headers = [
    "From: " . SENDER_EMAIL,
    "MIME-Version: 1.0",
    "Content-Type: text/plain; charset=UTF-8"
];

foreach (glob(DATA_DIR . "/*.json") as $file) {
    $device = basename($file, ".json");
    $data = json_decode(file_get_contents($file), true);
    $diff = time() - $data['time'];
    $lock_file = DATA_DIR . "/{$device}.lock";

    if ($diff > TIMEOUT_SECONDS) {
        // Device is offline
        if (!file_exists($lock_file)) {
            $subject = "[" . PROJECT_NAME . "] ALERT: $device is OFFLINE";
            $msg = "The device '$device' is no longer reporting in!\n";
            $msg .= "Last contact: " . date("Y-m-d H:i", $data['time']) . "\n";
            $msg .= "Connection type was: " . strtoupper($data['net']);
            
            mail(ADMIN_EMAIL, $subject, $msg, implode("\r\n", $headers));
            file_put_contents($lock_file, time());
        }
    } else {
        // Device is online
        if (file_exists($lock_file)) {
            $subject = "[" . PROJECT_NAME . "] RECOVERY: $device is ONLINE again";
            $msg = "The device '$device' is reachable again.";
            
            mail(ADMIN_EMAIL, $subject, $msg, implode("\r\n", $headers));
            unlink($lock_file);
        }
    }
}
?>