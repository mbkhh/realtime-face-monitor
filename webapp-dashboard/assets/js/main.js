document.addEventListener('DOMContentLoaded', function () {

    const dashboardGrid = document.getElementById('dashboard-grid');
    if (dashboardGrid) {
        const fetchStats = () => {
            fetch('/api/system_stats.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Could not fetch stats. Are you logged in?');
                    }
                    return response.json();
                })
                .then(data => {
                    // Update CPU
                    document.getElementById('cpu-usage').innerText = data.cpu.usage + '%';
                    document.getElementById('cpu-progress').style.width = data.cpu.usage + '%';
                    document.getElementById('cpu-temp').innerText = data.cpu.temp === 'N/A' ? 'N/A' : data.cpu.temp + 'C';

                    // Update Memory
                    document.getElementById('mem-usage').innerText = `${data.memory.used} MB / ${data.memory.total} MB`;
                    document.getElementById('mem-percent').innerText = data.memory.percent + '%';
                    document.getElementById('mem-progress').style.width = data.memory.percent + '%';

                    // Update Storage
                    document.getElementById('disk-usage').innerText = `${data.storage.used} / ${data.storage.total}`;
                    document.getElementById('disk-percent').innerText = data.storage.percent;
                    document.getElementById('disk-progress').style.width = data.storage.percent;

                    // Update System Info
                    document.getElementById('info-os').innerText = data.info.os;
                    document.getElementById('info-hostname').innerText = data.info.hostname;
                    document.getElementById('info-kernel').innerText = data.info.kernel;
                    document.getElementById('info-uptime').innerText = data.info.uptime;
                    document.getElementById('info-ip').innerText = data.info.ip;

                    // Update Service Status
                    updateStatus('status-apache', data.services.apache);
                    updateStatus('status-camera', data.services.camera);
                    updateStatus('status-mysql', data.services.mysql);
                    document.getElementById('status-php').innerText = data.services.php;
                })
                .catch(error => {
                    console.error('Error fetching system stats:', error);
                    clearInterval(statsInterval);
                });
        };

        const updateStatus = (elementId, isActive) => {
            const el = document.getElementById(elementId);
            if (isActive) {
                el.innerHTML = '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Running</span>';
            } else {
                el.innerHTML = '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Stopped</span>';
            }
        };

        // Fetch stats immediately on page load, then every 3 seconds.
        fetchStats();
        const statsInterval = setInterval(fetchStats, 3000);
    }


    // --- SECTION 2: DETECTIONS PAGE LOGIC ---
    // This code only runs if it finds the 'log-container' element.
    const logContainer = document.getElementById('log-container');
    if (logContainer) {
        
        const fetchNewLogs = () => {
            // Get the ID of the newest log entry currently displayed on the page.
            let latestId = logContainer.dataset.latestId || 0;
            
            // Ask the server for any entries newer than the one we have.
            fetch(`/api/get_new_detections.php?last_id=${latestId}`)
                .then(response => response.json())
                .then(newLogs => {
                    if (newLogs && newLogs.length > 0) {
                        // Reverse the array to prepend them in the correct chronological order (1, 2, 3...)
                        newLogs.reverse().forEach(log => {
                            const logHtml = `
                                <div class="bg-gray-800 rounded-lg p-4 flex items-center space-x-4 shadow-md" style="animation: fadeIn 0.5s ease-out;">
                                    <a href="/detection_logs/${log.filename}" target="_blank">
                                        <img src="/detection_logs/${log.filename}" alt="Detection Image" class="w-32 h-24 object-cover rounded-md bg-gray-700">
                                    </a>
                                    <div class="flex-grow">
                                        <p class="font-mono text-sm text-orange-400">${log.filename}</p>
                                        <p class="text-gray-400"><i class="fas fa-clock mr-2"></i>${log.timestamp_formatted}</p>
                                    </div>
                                </div>
                            `;
                            // Insert the new log at the very top of the container
                            logContainer.insertAdjacentHTML('afterbegin', logHtml);
                        });
                        
                        // Update the latest ID to the newest one we received from the server.
                        // We use the last item in the original array (newest chronological ID).
                        logContainer.dataset.latestId = newLogs[newLogs.length - 1].id;

                        // If the "No detections" message exists, remove it.
                        const emptyMessage = logContainer.querySelector('.text-center');
                        if(emptyMessage) emptyMessage.remove();
                    }
                })
                .catch(error => console.error('Error fetching new detections:', error));
        };

        // Inject a simple CSS animation into the page for new entries.
        const style = document.createElement('style');
        style.innerHTML = `@keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }`;
        document.head.appendChild(style);

        // Poll for new logs every 5 seconds.
        setInterval(fetchNewLogs, 5000);
    }
});

