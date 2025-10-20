<?php
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

function nav_item($page, $icon, $text, $current_page) {
    $active_class = ($page === $current_page)
        ? 'bg-orange-600/30 text-orange-400 border-l-4 border-orange-500'
        : 'text-gray-400 hover:bg-gray-700 hover:text-white';
    
    echo "<a href='index.php?page={$page}' class='flex items-center px-4 py-3 transition-colors duration-200 {$active_class}'>";
    echo "<i class='{$icon} w-6 text-center'></i>";
    echo "<span class='mx-4 font-medium'>{$text}</span>";
    echo "</a>";
}
?>

<aside class="flex-shrink-0 w-64 bg-gray-800 border-r border-gray-700">
    <div class="flex items-center justify-center h-20 border-b border-gray-700">
        <i class="fas fa-microchip text-3xl text-orange-500"></i>
        <span class="ml-3 text-xl font-bold text-white">O-Pi Panel</span>
    </div>
    <nav class="mt-6">
        <?php
        nav_item('dashboard', 'fas fa-tachometer-alt', 'Dashboard', $current_page);
        nav_item('camera', 'fas fa-video', 'Camera Stream', $current_page);
        nav_item('detections', 'fas fa-history', 'Detection Logs', $current_page);
        nav_item('settings', 'fas fa-cog', 'Settings', $current_page);
        ?>
    </nav>
    <div class="absolute bottom-0 w-64 p-4 border-t border-gray-700">
        <a href="index.php?action=logout" class="flex items-center px-4 py-3 text-gray-400 hover:bg-red-600/30 hover:text-white rounded-lg transition-colors duration-200">
            <i class="fas fa-sign-out-alt w-6 text-center"></i>
            <span class="mx-4 font-medium">Logout</span>
        </a>
    </div>
</aside>
