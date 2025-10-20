<?php
// --- Database Configuration ---
$db_config = [
    'user' => 'appuser',
    'password' => 'appuser', // <-- USE THE SAME PASSWORD
    'host' => 'localhost',
    'database' => 'monitoring_system'
];

$logs = [];
$latest_id = 0;

try {
    $conn = new PDO("mysql:host={$db_config['host']};dbname={$db_config['database']}", $db_config['user'], $db_config['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT id, filename, timestamp FROM camera_detections ORDER BY timestamp DESC LIMIT 50");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get the ID of the most recent log to initialize the auto-updater
    if (!empty($logs)) {
        $latest_id = $logs[0]['id'];
    }

} catch(PDOException $e) {
    $db_error = "Database Connection Error: " . $e->getMessage();
}
?>

<h1 class="text-3xl font-bold text-white mb-2">Detection Logs</h1>
<p class="text-gray-400 mb-6">This page automatically updates with new detections.</p>

<?php if (isset($db_error)): ?>
    <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg">
        <?php echo $db_error; ?>
    </div>
<?php endif; ?>

<!--
This container holds the logs. The `data-latest-id` attribute is crucial;
it tells our JavaScript which logs are already on the page.
-->
<div id="log-container" data-latest-id="<?php echo $latest_id; ?>" class="space-y-4">

    <?php if (empty($logs) && !isset($db_error)): ?>
        <div class="text-center py-10 bg-gray-800 rounded-lg">
            <i class="fas fa-camera-viewfinder text-4xl text-gray-500"></i>
            <p class="mt-4 text-gray-400">No detections have been logged yet.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($logs as $log): ?>
        <div class="bg-gray-800 rounded-lg p-4 flex items-center space-x-4 shadow-md transition-transform transform hover:scale-101">
            <a href="/detection_logs/<?php echo htmlspecialchars($log['filename']); ?>" target="_blank">
                <img src="/detection_logs/<?php echo htmlspecialchars($log['filename']); ?>" alt="Detection Image" class="w-32 h-24 object-cover rounded-md bg-gray-700">
            </a>
            <div class="flex-grow">
                <p class="font-mono text-sm text-orange-400"><?php echo htmlspecialchars($log['filename']); ?></p>
                <p class="text-gray-400"><i class="fas fa-clock mr-2"></i><?php echo date("F j, Y, g:i:s A", strtotime($log['timestamp'])); ?></p>
            </div>
        </div>
    <?php endforeach; ?>

</div>
