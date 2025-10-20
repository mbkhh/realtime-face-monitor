<?php
// Start the session to keep track of logged-in users.
session_start();

// --- VERY IMPORTANT SECURITY NOTE ---
// This uses a hardcoded password. For any real-world use,
// you should replace this with a secure method, like a database lookup with hashed passwords.
define('USERNAME', 'admin');
define('PASSWORD', 'orangepi'); // CHANGE THIS PASSWORD!

// --- Login Handling ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    if ($_POST['username'] === USERNAME && $_POST['password'] === PASSWORD) {
        $_SESSION['loggedin'] = true;
        header('Location: index.php?page=dashboard'); // Redirect to dashboard after login
        exit;
    } else {
        $login_error = "Invalid username or password!";
    }
}

// --- Logout Handling ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// --- Page Routing ---
// If the user is not logged in, show the login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    include('login.php');
    exit;
}

// --- Main Application Layout ---
// Determine which page to show. Default to the dashboard.
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$page_file = 'pages/' . basename($page) . '.php';

// Include the main layout components
include('templates/header.php');

echo '<div class="flex h-screen bg-gray-900 text-white">';
include('templates/sidebar.php');

echo '<main class="flex-1 p-6 sm:p-8 md:p-10 overflow-y-auto">';
if (file_exists($page_file)) {
    include($page_file);
} else {
    // If the page doesn't exist, show a 404 error.
    echo '<h1 class="text-2xl font-bold text-red-500">Error 404</h1>';
    echo '<p>Page not found.</p>';
}
echo '</main>';
echo '</div>';

include('templates/footer.php');

?>
