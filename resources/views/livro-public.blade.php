<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $livro->titulo }} - Biblioteca Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Biblioteca Digital</h1>
                <p class="text-gray-600">Informações do Livro</p>
            </div>

            <!-- Card do Livro -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="md:flex">
                    <!-- Capa -->
                    <div class="md:flex-shrink-0 bg-gray-100 flex items-center justify-center p-8">
                        @if($livro->capa_url)
                            <img class="h-96 w-64 object-cover rounded-lg shadow-md" 
                                 src="{{ $livro->capa_url }}" 
                                 alt="{{ $livro->titulo }}">
                        @else
                            <div class="h-96 w-64 bg-gray-300 rounded-lg flex items-center justify-center">
                                <svg class="h-32 w-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Informações -->
                    <div class="p-8 flex-1">
                        <div class="mb-6">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $livro->titulo }}</h2>
                            <p class="text-xl text-gray-600 mb-4">{{ $livro->autor }}</p>
                            
                            @if($livro->categoria)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $livro->categoria->nome }}
                                </span>
                            @endif
                        </div>

                        <!-- Detalhes -->
                        <div class="space-y-3 mb-6">
                            @if($livro->isbn)
                                <div class="flex items-center text-gray-700">
                                    <span class="font-semibold w-32">ISBN:</span>
                                    <span>{{ $livro->isbn }}</span>
                                </div>
                            @endif

                            @if($livro->editora)
                                <div class="flex items-center text-gray-700">
                                    <span class="font-semibold w-32">Editora:</span>
                                    <span>{{ $livro->editora }}</span>
                                </div>
                            @endif

                            @if($livro->ano_publicacao)
                                <div class="flex items-center text-gray-700">
                                    <span class="font-semibold w-32">Ano:</span>
                                    <span>{{ $livro->ano_publicacao }}</span>
                                </div>
                            @endif

                            @if($livro->numero_paginas)
                                <div class="flex items-center text-gray-700">
                                    <span class="font-semibold w-32">Páginas:</span>
                                    <span>{{ $livro->numero_paginas }}</span>
                                </div>
                            @endif

                            <div class="flex items-center text-gray-700">
                                <span class="font-semibold w-32">Idioma:</span>
                                <span>{{ $livro->idioma }}</span>
                            </div>

                            <div class="flex items-center text-gray-700">
                                <span class="font-semibold w-32">Disponível:</span>
                                <span class="font-bold {{ $livro->quantidade_disponivel > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $livro->quantidade_disponivel }} de {{ $livro->quantidade_total }}
                                </span>
                            </div>

                            <div class="flex items-center">
                                <span class="font-semibold w-32 text-gray-700">Status:</span>
                                @if($livro->status === 'disponivel')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        ✓ Disponível
                                    </span>
                                @elseif($livro->status === 'indisponivel')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        ✗ Indisponível
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        ⚠ Em Manutenção
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Sinopse -->
                        @if($livro->sinopse)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Sinopse</h3>
                                <div class="text-gray-700 leading-relaxed">
                                    {!! nl2br(e($livro->sinopse)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Ações -->
                        <div class="flex gap-3">
                            <a href="/admin" class="flex-1 bg-blue-600 text-white text-center py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                                Fazer Reserva
                            </a>
                            
                            @if($livro->arquivo_pdf_url)
                                <a href="{{ $livro->arquivo_pdf_url }}" 
                                   target="_blank"
                                   class="flex-1 bg-gray-200 text-gray-800 text-center py-3 px-6 rounded-lg font-semibold hover:bg-gray-300 transition">
                                    Ver PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-600">
                <p>&copy; {{ date('Y') }} Biblioteca Digital. Todos os direitos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
