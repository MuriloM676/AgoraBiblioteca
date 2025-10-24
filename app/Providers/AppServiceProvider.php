<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Livro;
use App\Models\Emprestimo;
use App\Models\Reserva;
use App\Policies\LivroPolicy;
use App\Policies\EmprestimoPolicy;
use App\Policies\ReservaPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar policies
        Gate::policy(Livro::class, LivroPolicy::class);
        Gate::policy(Emprestimo::class, EmprestimoPolicy::class);
        Gate::policy(Reserva::class, ReservaPolicy::class);
    }
}
