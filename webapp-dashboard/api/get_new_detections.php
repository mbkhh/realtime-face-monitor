<?php
session_start();
header('Content-Type: application/json');

// Security check: Only allow logged-in users.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the ID of the last log the user's browser already has.
// Sanitize it to an integer to prevent SQL injection.
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// --- Database Configuration ---
// Make sure this matches your Flask app and setup script.
$db_config = [
    'user' => 'appuser',
    'password' => 'appuser', // <-- USE THE SAME PASSWORD
    'host' => 'localhost',
    'database' => 'monitoring_system'
];

$new_detections = [];

try {
    $conn = new PDO("mysql:host={$db_config['host']};dbname={$db_config['database']}", $db_config['user'], $db_config['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare a statement to select only rows with an ID greater than the last one seen.
    $stmt = $conn->prepare("SELECT id, filename, timestamp FROM camera_detections WHERE id > :last_id ORDER BY id ASC");
    $stmt->bindParam(':last_id', $last_id, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data for easy use in JavaScript
    foreach ($results as $row) {
        $new_detections[] = [
            'id' => $row['id'],
            'filename' => $row['filename'],
            // Format the timestamp into a more readable string
            'timestamp_formatted' => date("F j, Y, g:i:s A", strtotime($row['timestamp']))
        ];
    }

} catch(PDOException $e) {
    // In case of a database error, return an error message
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}

// Return the new detections as a JSON array
echo json_encode($new_detections);
?>
