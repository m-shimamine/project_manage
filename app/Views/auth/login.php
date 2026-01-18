<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - プロジェクト管理システム</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .login-card {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- ロゴ -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 shadow-lg mb-4">
                    <i class="fas fa-project-diagram text-blue-400 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">PM System</h1>
                <p class="text-sm text-slate-500 mt-1">プロジェクト管理システム</p>
            </div>

            <!-- ログインカード -->
            <div class="bg-white rounded-2xl login-card overflow-hidden">
                <!-- ヘッダー -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                    <h2 class="text-xl font-bold text-white">ログイン</h2>
                    <p class="text-blue-100 text-sm mt-1">アカウント情報を入力してください</p>
                </div>

                <!-- フォーム -->
                <form method="POST" action="<?= base_url('login') ?>" class="p-8">
                    <?= csrf_field() ?>

                    <!-- エラーメッセージ -->
                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <span class="text-sm text-red-700"><?= esc(session()->getFlashdata('error')) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2 mt-0.5"></i>
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- メールアドレス -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i class="fas fa-envelope text-slate-400 mr-1"></i>メールアドレス
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= old('email') ?>"
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus transition-all"
                            placeholder="example@company.com"
                            required
                            autofocus
                        >
                    </div>

                    <!-- パスワード -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i class="fas fa-lock text-slate-400 mr-1"></i>パスワード
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus transition-all"
                                placeholder="パスワードを入力"
                                required
                            >
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ログイン状態を保持 -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-slate-600">ログイン状態を保持</span>
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">パスワードを忘れた方</a>
                    </div>

                    <!-- ログインボタン -->
                    <button type="submit" class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>ログイン
                    </button>
                </form>
            </div>

            <!-- フッター -->
            <p class="text-center text-xs text-slate-400 mt-6">
                &copy; <?= date('Y') ?> PM System. All rights reserved.
            </p>
        </div>
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('password-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>
