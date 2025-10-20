<h1 class="text-3xl font-bold text-white mb-6">System Logs</h1>

<div class="bg-gray-800 rounded-lg p-4 shadow-lg">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-300">Last 100 lines of syslog</h2>
        <button onclick="location.reload();" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 rounded-lg text-white font-semibold transition-colors duration-300">
            <i class="fas fa-sync-alt mr-2"></i>Refresh
        </button>
    </div>
    <div class="bg-black rounded-md p-4 h-96 overflow-y-scroll font-mono text-sm text-gray-300">
        <pre><?php
            // Use journalctl for modern systems, fallback to syslog. --no-pager is important.
            $logs = shell_exec('journalctl -n 100 --no-pager 2>/dev/null || tail -n 100 /var/log/syslog');
            echo htmlspecialchars($logs ?: 'Could not read logs. Check permissions.');
        ?></pre>
    </div>
</div>
