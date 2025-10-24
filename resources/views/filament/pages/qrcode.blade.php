<div class="p-6 text-center">
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $livro->titulo }}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ $livro->autor }}
        </p>
    </div>

    @if($livro->qr_code_url)
        <div class="flex justify-center mb-4">
            <img src="{{ $livro->qr_code_url }}" alt="QR Code" class="border-4 border-gray-200 rounded-lg shadow-lg">
        </div>
    @else
        <div class="p-8 bg-gray-100 dark:bg-gray-800 rounded-lg">
            <p class="text-gray-600 dark:text-gray-400">
                QR Code será gerado automaticamente
            </p>
        </div>
    @endif

    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
        <p>Código do Livro: <strong>#{{ $livro->id }}</strong></p>
        <p>ISBN: <strong>{{ $livro->isbn ?? 'N/A' }}</strong></p>
    </div>

    <div class="mt-6 flex justify-center gap-2">
        @if($livro->qr_code_url)
                <a href="{{ $livro->qr_code_url }}" 
                    download="qrcode-livro-{{ $livro->id }}.svg"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Baixar QR Code
            </a>
        @endif
    </div>
</div>
