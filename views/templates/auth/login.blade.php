<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-purple-600 to-blue-600 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
        <div class="text-center mb-6">
            <i class="fas fa-user-shield text-4xl text-purple-600 mb-2"></i>
            <h1 class="text-2xl font-bold text-gray-800">Login Admin</h1>
            <p class="text-gray-600">Acesse seu painel administrativo</p>
        </div>

        @if ($error)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $error }}
            </div>
        @endif

        <form method="POST" action="/admin/login" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-envelope mr-1"></i>
                    Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="seu@email.com"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-lock mr-1"></i>
                    Senha
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="••••••••"
                >
            </div>

            <button 
                type="submit" 
                class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition duration-200 flex items-center justify-center"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Entrar
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Não tem uma conta? 
                <a href="/admin/register" class="text-purple-600 hover:text-purple-800 font-medium">
                    Cadastre sua loja
                </a>
            </p>
        </div>
    </div>
</body>
</html>
