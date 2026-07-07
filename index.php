<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo PROJECT_NAME; ?> Dashboard</title>
    <style>
        :root {
            --bg: #f0f2f5; --card-bg: #ffffff; --primary: #1a73e8;
            --success: #2ecc71; --danger: #e74c3c; --text-main: #1c1e21; --text-sub: #65676b;
        }
        body { font-family: -apple-system, system-ui, sans-serif; background: var(--bg); margin: 0; padding: 80px 20px 40px 20px; color: var(--text-main); }
        .container { max-width: 1100px; margin: 0 auto; }
        header { text-align: center; margin-bottom: 50px; }
        h1 { margin: 0; color: var(--primary); font-size: 2rem; font-weight: 900; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; }
        .card { background: var(--card-bg); border-radius: 16px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-top: 6px solid #ccc; transition: transform 0.2s; }
        .card.online { border-top-color: var(--success); }
        .card.offline { border-top-color: var(--danger); }
        .dev-name { font-size: 1.4rem; font-weight: 800; margin-bottom: 20px; display: block; color: var(--primary); }
        .stat-group { display: flex; flex-direction: column; gap: 10px; }
        .stat-item { display: flex; justify-content: space-between; border-bottom: 1px solid #f5f6f7; padding-bottom: 8px; align-items: center; }
        .label { color: var(--text-sub); font-size: 0.9rem; }
        .value { font-weight: 600; font-size: 0.95rem; }
        .badge { padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: bold; }
        .badge-online { background: #e8f5e9; color: var(--success); text-transform: uppercase; }
        .badge-offline { background: #ffebee; color: var(--danger); text-transform: uppercase; }
        footer { margin-top: 60px; text-align: center; font-size: 0.85rem; color: var(--text-sub); border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <header><h1>📡 <?php echo PROJECT_NAME; ?> Dashboard</h1></header>
    <div class="grid">
        <?php
        $files = glob(DATA_DIR . "/*.json");
        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            $device = basename($file, ".json");
            $is_online = (time() - $data['time'] < TIMEOUT_SECONDS);
            
            // Simplified network logic for GitHub
            $net_raw = strtolower($data['net'] ?? 'n/a');
            $display_net = (strpos($net_raw, 'wireless') !== false || strpos($net_raw, 'wifi') !== false) ? 'Wireless' : 'Ethernet';

            echo "<div class='card " . ($is_online ? 'online' : 'offline') . "'>";
            echo "  <span class='dev-name'>$device</span>";
            echo "  <div class='stat-group'>";
            echo "    <div class='stat-item'><span class='label'>Status</span><span class='badge ".($is_online ? 'badge-online' : 'badge-offline')."'>".($is_online ? 'online' : 'offline')."</span></div>";
            echo "    <div class='stat-item'><span class='label'>Connection</span><span class='value'>$display_net</span></div>";
            echo "    <div class='stat-item'><span class='label'>Uptime</span><span class='value'>{$data['uptime']}</span></div>";
            echo "    <div class='stat-item'><span class='label'>RAM / CPU</span><span class='value'>{$data['load']}</span></div>";
            echo "    <div class='stat-item'><span class='label'>CPU Temp</span><span class='value'>{$data['temp']}°C</span></div>";
            echo "    <div class='stat-item'><span class='label'>Disk Usage</span><span class='value'>{$data['disk']}%</span></div>";
            echo "  </div>";
            echo "  <div style='margin-top:20px; font-size:0.75rem; color:var(--text-sub); text-align:right;'>Last Update: ".date("H:i", $data['time'])."</div>";
            echo "</div>";
        }
        ?>
    </div>
    <footer>&copy; <?php echo date("Y"); ?> &bull; <?php echo PROJECT_NAME; ?></footer>
</div>
</body>
</html>