<?php
// Define the path to the settings file (relative to the root index.php)
$settingsFile = 'settings.json';
$successMessage = '';
$errorMessage = '';
$defaultBorderColor = '#22c55e'; // A nice green from Tailwind

// --- Handle Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['borderColor'])) {
        // Sanitize the input to ensure it's a valid hex color format
        $newColor = preg_match('/^#[a-f0-9]{6}$/i', $_POST['borderColor']) ? $_POST['borderColor'] : $defaultBorderColor;

        $settingsData = ['borderColor' => $newColor];

        // Write the new settings to the file
        if (file_put_contents($settingsFile, json_encode($settingsData, JSON_PRETTY_PRINT))) {
            $successMessage = "Settings saved successfully!";
        } else {
            $errorMessage = "Error: Could not write to settings file. Please check permissions.";
        }
    }
}

// --- Load Current Settings for Display ---
$currentBorderColor = $defaultBorderColor;
if (file_exists($settingsFile)) {
    $json = file_get_contents($settingsFile);
    $data = json_decode($json, true);
    if (isset($data['borderColor'])) {
        $currentBorderColor = htmlspecialchars($data['borderColor']);
    }
}
?>

<div class="p-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-200">Application Settings</h2>
    
    <?php if ($successMessage): ?>
        <div class="bg-green-900 border border-green-600 text-green-200 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline"><?= $successMessage ?></span>
        </div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <div class="bg-red-900 border border-red-600 text-red-200 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline"><?= $errorMessage ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <form method="POST" action="index.php?page=settings">
            <div class="mb-6">
                <label for="borderColor" class="block text-lg font-medium text-gray-300 mb-2">Camera Border Color</label>
                <p class="text-sm text-gray-400 mb-3">Choose a color for the border and glow around the live camera stream.</p>
                <div class="flex items-center gap-4">
                    <input 
                        type="color" 
                        id="borderColor" 
                        name="borderColor"
                        value="<?= $currentBorderColor ?>"
                        class="w-16 h-10 p-1 bg-gray-700 border border-gray-600 rounded-lg cursor-pointer">
                    <span class="text-gray-300 font-mono text-lg" id="colorValue"><?= $currentBorderColor ?></span>
                </div>
            </div>
            
            <div>
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50 transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Simple script to update the hex value text when the color picker changes
    const colorInput = document.getElementById('borderColor');
    const colorValueDisplay = document.getElementById('colorValue');

    colorInput.addEventListener('input', (event) => {
        colorValueDisplay.textContent = event.target.value;
    });
</script>

