<h1 class="text-3xl font-bold text-white mb-6">System Dashboard</h1>

<div id="dashboard-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- CPU Usage Card -->
    <div class="bg-gray-800 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-300">CPU</h2>
            <i class="fas fa-microchip text-2xl text-orange-500"></i>
        </div>
        <div class="flex items-end mt-4 space-x-4">
            <div>
                <p class="text-xs text-gray-400 uppercase">Usage</p>
                <p id="cpu-usage" class="text-4xl font-bold leading-none">--%</p>
            </div>
            <div class="border-l border-gray-700 pl-4">
                <p class="text-xs text-gray-400 uppercase">Temp</p>
                <p id="cpu-temp" class="text-4xl font-bold leading-none">--°C</p>
            </div>
        </div>
        <div class="w-full bg-gray-700 rounded-full h-2.5 mt-4 progress-bar-bg">
            <div id="cpu-progress" class="bg-orange-600 h-2.5 rounded-full progress-bar-fill" style="width: 0%"></div>
        </div>
    </div>
    <!-- Memory Usage Card -->
    <div class="bg-gray-800 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-300">Memory (RAM)</h2>
            <i class="fas fa-memory text-2xl text-blue-500"></i>
        </div>
        <p id="mem-usage" class="text-xl font-bold mt-4">-- MB / -- MB</p>
        <p id="mem-percent" class="text-2xl font-bold text-gray-400">--%</p>
        <div class="w-full bg-gray-700 rounded-full h-2.5 mt-2 progress-bar-bg">
            <div id="mem-progress" class="bg-blue-600 h-2.5 rounded-full progress-bar-fill" style="width: 0%"></div>
        </div>
    </div>

    <!-- Storage Usage Card -->
    <div class="bg-gray-800 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-300">Storage</h2>
            <i class="fas fa-hdd text-2xl text-green-500"></i>
        </div>
        <p id="disk-usage" class="text-xl font-bold mt-4">-- / --</p>
        <p id="disk-percent" class="text-2xl font-bold text-gray-400">--%</p>
        <div class="w-full bg-gray-700 rounded-full h-2.5 mt-2 progress-bar-bg">
            <div id="disk-progress" class="bg-green-600 h-2.5 rounded-full progress-bar-fill" style="width: 0%"></div>
        </div>
    </div>
    
    <!-- System Information Card -->
    <div class="bg-gray-800 rounded-lg p-6 md:col-span-2">
        <h2 class="text-lg font-semibold text-gray-300 mb-4">System Information</h2>
        <ul class="space-y-3 text-gray-400">
            <li class="flex justify-between"><span>Operating System:</span> <strong id="info-os" class="text-white">--</strong></li>
            <li class="flex justify-between"><span>Hostname:</span> <strong id="info-hostname" class="text-white">--</strong></li>
            <li class="flex justify-between"><span>Kernel Version:</span> <strong id="info-kernel" class="text-white">--</strong></li>
            <li class="flex justify-between"><span>IP Address:</span> <strong id="info-ip" class="text-white">--</strong></li>
            <li class="flex justify-between"><span>System Uptime:</span> <strong id="info-uptime" class="text-white">--</strong></li>
        </ul>
    </div>
    
    <!-- Service Status Card -->
    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-300 mb-4">Service Status</h2>
        <ul class="space-y-3 text-gray-400">
            <li class="flex justify-between items-center"><span>Apache2:</span> <span id="status-apache">--</span></li>
            <li class="flex justify-between items-center"><span>MySQL:</span> <span id="status-mysql">--</span></li>
            <li class="flex justify-between items-center"><span>PHP Version:</span> <strong id="status-php" class="text-white">--</strong></li>
        	<li class="flex justify-between items-center"><span>Camera Detection:</span> <span id="status-camera">--</span></li>
	</ul>
    </div>

</div>
