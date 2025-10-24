<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/livros/{id}', function ($id) {
    $livro = \App\Models\Livro::findOrFail($id);
    return view('livro-public', compact('livro'));
})->name('livros.show');
