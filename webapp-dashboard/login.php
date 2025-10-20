<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Orange Pi Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-900 flex items-center justify-center h-screen">
    <div class="w-full max-w-md p-8 space-y-6 bg-gray-800 rounded-xl shadow-lg">
        <div class="text-center">
             <i class="fas fa-microchip text-5xl text-orange-500"></i>
            <h1 class="mt-4 text-3xl font-bold text-white">Orange Pi Control Panel</h1>
            <p class="text-gray-400">Please sign in to continue</p>
        </div>

        <?php if (isset($login_error)): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-center">
                <?php echo $login_error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php" class="space-y-6">
            <div>
                <label for="username" class="text-sm font-medium text-gray-300">Username</label>
                <input type="text" name="username" id="username" required class="mt-1 block w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="admin">
            </div>
            <div>
                <label for="password" class="text-sm font-medium text-gray-300">Password</label>
                <input type="password" name="password" id="password" required class="mt-1 block w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full py-3 px-4 bg-orange-600 hover:bg-orange-700 rounded-lg text-white font-semibold transition-colors duration-300">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>
