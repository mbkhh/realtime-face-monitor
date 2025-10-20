<?php
session_start();
header('Content-Type: application/json');

// Security check: Only allow logged-in users to access this API.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Function to safely execute shell commands and return the output.
function run_command($command) {
    return trim(shell_exec($command . ' 2>/dev/null')) ?: 'N/A';
}

// --- GATHER SYSTEM INFORMATION ---

// CPU Usage
// This is a simplified calculation. It gets the idle percentage and subtracts from 100.
// CPU Usage
// This is a more robust calculation using awk to grab the idle percentage.
// It also handles both '.' and ',' as decimal separators.
$cpu_idle_str = run_command("top -bn1 | grep '%Cpu(s)' | awk '{print $8}'");
$cpu_idle = (float)str_replace(',', '.', $cpu_idle_str);
$cpu_usage = 100 - $cpu_idle;

// CPU Temperature
$cpu_temp_raw = run_command("cat /sys/class/thermal/thermal_zone0/temp");
$cpu_temp = 'N/A';
if (is_numeric($cpu_temp_raw)) {
    // The value is in millidegrees Celsius, divide by 1000
    $cpu_temp = round($cpu_temp_raw / 1000, 1);
}

// Memory Usage
$mem_info = run_command("free -m | grep Mem");
$mem_parts = preg_split('/\s+/', $mem_info);
$mem_total = $mem_parts[1];
$mem_used = $mem_parts[2];
$mem_usage_percent = ($mem_used / $mem_total) * 100;

// Storage Usage
$disk_info = run_command("df -h / | tail -n 1");
$disk_parts = preg_split('/\s+/', $disk_info);
$disk_total = $disk_parts[1];
$disk_used = $disk_parts[2];
$disk_percent = $disk_parts[4];

// System Information
$os_info = run_command("lsb_release -d | cut -d: -f2");
$kernel = run_command("uname -r");
$uptime = run_command("uptime -p");
$hostname = run_command("hostname");
$ip_address = run_command("hostname -I | cut -d' ' -f1");

// Service Status
$apache_status = strpos(run_command("systemctl is-active apache2"), "active") !== false;
$camera_status =  strpos(run_command("systemctl is-active camera_monitor.service"), "active") !== false;
$mysql_status_raw = run_command("systemctl is-active mysql");
$mysql_status = strpos($mysql_status_raw, "active") !== false;
$mysql_error = $mysql_status ? '' : $mysql_status_raw; // Show error if not active

// PHP Version
$php_version = phpversion();


// --- COMPILE DATA INTO AN ARRAY ---
$data = [
    'cpu' => [
        'usage' => round($cpu_usage, 2),
    	'temp' => $cpu_temp
    ],
    'memory' => [
        'used' => $mem_used,
        'total' => $mem_total,
        'percent' => round($mem_usage_percent, 2)
    ],
    'storage' => [
        'used' => $disk_used,
        'total' => $disk_total,
        'percent' => $disk_percent
    ],
    'info' => [
        'os' => trim($os_info),
        'hostname' => $hostname,
        'kernel' => $kernel,
        'uptime' => str_replace('up ', '', $uptime),
        'ip' => $ip_address,
    ],
    'services' => [
        'apache' => $apache_status,
        'mysql' => $mysql_status,
        'mysql_error' => $mysql_error,
        'php' => $php_version,
	'camera' => $camera_status
    ]
];

// --- RETURN DATA AS JSON ---
echo json_encode($data);

?>
