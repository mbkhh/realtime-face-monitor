<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orange Pi Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Custom scrollbar for a better look */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1a202c; /* bg-gray-900 */
        }
        ::-webkit-scrollbar-thumb {
            background: #4a5568; /* bg-gray-700 */
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #718096; /* bg-gray-600 */
        }
        /* Custom styles for progress bars */
        .progress-bar-bg {
            background-color: #2d3748; /* bg-gray-800 */
        }
        .progress-bar-fill {
            transition: width 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-900">
