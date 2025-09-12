<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cadastro' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-green-600 to-blue-600 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
        <div class="text-center mb-6">
            <i class="fas fa-store text-4xl text-green-600 mb-2"></i>
            <h1 class="text-2xl font-bold text-gray-800">Cadastre sua Loja</h1>
            <p class="text-gray-600">Crie seu cardápio digital</p>
        </div>

        @if ($error)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $error }}
            </div>
        @endif

        @if ($success)
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i>
                {{ $success }}
            </div>
        @endif

        <form method="POST" action="/admin/register" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user mr-1"></i>
                        Seu Nome
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="João Silva"
                    >
                </div>

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
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="seu@email.com"
                    >
                </div>

                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-store mr-1"></i>
                        Nome da Loja
                    </label>
                    <input 
                        type="text" 
                        id="store_name" 
                        name="store_name" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Açaíteria do João"
                    >
                </div>

                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fab fa-whatsapp mr-1"></i>
                        WhatsApp
                    </label>
                    <input 
                        type="tel" 
                        id="whatsapp" 
                        name="whatsapp" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="(11) 99999-9999"
                    >
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        Endereço (opcional)
                    </label>
                    <textarea 
                        id="address" 
                        name="address" 
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Rua das Frutas, 123 - Centro"
                    ></textarea>
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
                        minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="••••••••"
                    >
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-lock mr-1"></i>
                        Confirmar Senha
                    </label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required 
                        minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="••••••••"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition duration-200 flex items-center justify-center"
            >
                <i class="fas fa-user-plus mr-2"></i>
                Criar Conta
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Já tem uma conta? 
                <a href="/admin/login" class="text-green-600 hover:text-green-800 font-medium">
                    Fazer login
                </a>
            </p>
        </div>
    </div>

    <script>
        // Validação de senhas iguais
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });

        // Formatação do WhatsApp
        document.getElementById('whatsapp').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 11) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 7) {
                value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
            }
            this.value = value;
        });
    </script>
</body>
</html>
