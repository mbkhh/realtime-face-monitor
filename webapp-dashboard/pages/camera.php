<h1 class="text-3xl font-bold text-white mb-6">Camera Stream</h1>

<div class="bg-gray-800 rounded-lg p-4 shadow-lg">
    <div class=" rounded-md overflow-hidden">
        <!-- The image source points to the video stream from your Python service -->
        <img src="http://<?php echo $_SERVER['SERVER_ADDR']; ?>:5000/video" 
             alt="Live Camera Feed" 
             
		class="h-auto"
             onerror="this.onerror=null; this.alt='Error: Could not load camera stream. Is the streaming service running?'; this.style.display='block';">
    </div>
    <div class="mt-4 text-gray-400 text-sm">
        <p><i class="fas fa-info-circle mr-2"></i>This is a live stream from the camera attached to the Orange Pi. Ensure the streaming service is active on port 5000.</p>
    </div>
</div>
