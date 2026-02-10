<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Menu Digital' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8B5CF6',    // Roxo azulado
                        secondary: '#A855F7',  // Roxo claro  
                        accent: '#EC4899',     // Rosa
                        success: '#10B981',    // Verde
                        danger: '#EF4444',     // Vermelho
                        dark: '#1E293B',       // Azul escuro
                    },
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-blue-50 min-h-screen font-sans">
    <div class="container mx-auto px-4 py-6">
        @yield('content')
    </div>
</body>
</html>
