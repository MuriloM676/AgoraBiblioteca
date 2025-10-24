<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Livro;
use App\Models\Emprestimo;
use App\Models\Reserva;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Criar permissões básicas
        $permissions = [
            'view_livros',
            'create_livros',
            'edit_livros',
            'delete_livros',
            'view_emprestimos',
            'create_emprestimos',
            'edit_emprestimos',
            'delete_emprestimos',
            'view_reservas',
            'create_reservas',
            'edit_reservas',
            'delete_reservas',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_dashboard',
            'manage_system',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Atribuir todas as permissões ao admin
        $adminRole->givePermissionTo(Permission::all());

        // Atribuir permissões limitadas ao user
        $userRole->givePermissionTo([
            'view_livros',
            'view_reservas',
            'create_reservas',
            'delete_reservas',
        ]);

        // Criar usuário administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@biblioteca.com',
            'password' => Hash::make('admin123'),
            'cpf' => '123.456.789-00',
            'telefone' => '(11) 98765-4321',
            'endereco' => 'Rua da Biblioteca, 100',
            'data_nascimento' => '1990-01-01',
            'matricula' => 'ADM001',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Criar usuário comum de teste
        $user = User::create([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => Hash::make('senha123'),
            'cpf' => '987.654.321-00',
            'telefone' => '(11) 91234-5678',
            'endereco' => 'Rua dos Leitores, 200',
            'data_nascimento' => '1995-05-15',
            'matricula' => 'USR001',
            'is_active' => true,
        ]);
        $user->assignRole('user');

        // Criar mais 10 usuários aleatórios
        User::factory(10)->create()->each(function ($user) use ($userRole) {
            $user->assignRole('user');
        });

        // Criar categorias
        $categorias = [
            ['nome' => 'Ficção Científica', 'slug' => 'ficcao-cientifica', 'descricao' => 'Livros de ficção científica e futurismo'],
            ['nome' => 'Romance', 'slug' => 'romance', 'descricao' => 'Histórias de amor e relacionamentos'],
            ['nome' => 'Suspense', 'slug' => 'suspense', 'descricao' => 'Livros de mistério e suspense'],
            ['nome' => 'Terror', 'slug' => 'terror', 'descricao' => 'Histórias de terror e horror'],
            ['nome' => 'Fantasia', 'slug' => 'fantasia', 'descricao' => 'Mundos mágicos e fantásticos'],
            ['nome' => 'Biografia', 'slug' => 'biografia', 'descricao' => 'Histórias de vida reais'],
            ['nome' => 'História', 'slug' => 'historia', 'descricao' => 'Livros sobre eventos históricos'],
            ['nome' => 'Filosofia', 'slug' => 'filosofia', 'descricao' => 'Pensamento e reflexões filosóficas'],
            ['nome' => 'Autoajuda', 'slug' => 'autoajuda', 'descricao' => 'Desenvolvimento pessoal e motivação'],
            ['nome' => 'Tecnologia', 'slug' => 'tecnologia', 'descricao' => 'Livros sobre tecnologia e inovação'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria + ['is_active' => true]);
        }

        // Criar livros famosos
        $livrosFamosos = [
            [
                'titulo' => '1984',
                'autor' => 'George Orwell',
                'categoria_id' => Categoria::where('slug', 'ficcao-cientifica')->first()->id,
                'isbn' => '9780451524935',
                'sinopse' => 'Uma distopia sobre um regime totalitário que controla todos os aspectos da vida.',
                'ano_publicacao' => 1949,
                'editora' => 'Secker & Warburg',
                'numero_paginas' => 328,
                'idioma' => 'Português',
                'quantidade_total' => 3,
                'quantidade_disponivel' => 2,
            ],
            [
                'titulo' => 'Dom Casmurro',
                'autor' => 'Machado de Assis',
                'categoria_id' => Categoria::where('slug', 'romance')->first()->id,
                'isbn' => '9788535911664',
                'sinopse' => 'A história de Bentinho e Capitu, um dos maiores clássicos da literatura brasileira.',
                'ano_publicacao' => 1899,
                'editora' => 'Companhia das Letras',
                'numero_paginas' => 256,
                'idioma' => 'Português',
                'quantidade_total' => 5,
                'quantidade_disponivel' => 4,
            ],
            [
                'titulo' => 'O Senhor dos Anéis',
                'autor' => 'J.R.R. Tolkien',
                'categoria_id' => Categoria::where('slug', 'fantasia')->first()->id,
                'isbn' => '9788533613379',
                'sinopse' => 'A épica jornada de Frodo para destruir o Um Anel.',
                'ano_publicacao' => 1954,
                'editora' => 'HarperCollins',
                'numero_paginas' => 1178,
                'idioma' => 'Português',
                'quantidade_total' => 4,
                'quantidade_disponivel' => 3,
            ],
            [
                'titulo' => 'Harry Potter e a Pedra Filosofal',
                'autor' => 'J.K. Rowling',
                'categoria_id' => Categoria::where('slug', 'fantasia')->first()->id,
                'isbn' => '9788532530787',
                'sinopse' => 'A história de um jovem bruxo e suas aventuras em Hogwarts.',
                'ano_publicacao' => 1997,
                'editora' => 'Rocco',
                'numero_paginas' => 264,
                'idioma' => 'Português',
                'quantidade_total' => 6,
                'quantidade_disponivel' => 5,
            ],
        ];

        foreach ($livrosFamosos as $livroData) {
            Livro::create($livroData);
        }

        // Criar mais 30 livros aleatórios
        Livro::factory(30)->create();

        // Criar empréstimos
        Emprestimo::factory(15)->ativo()->create();
        Emprestimo::factory(5)->atrasado()->create();
        Emprestimo::factory(20)->devolvido()->create();

        // Criar reservas
        Reserva::factory(10)->pendente()->create();
        Reserva::factory(5)->confirmada()->create();

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@biblioteca.com / admin123');
        $this->command->info('User: joao@example.com / senha123');
    }
}
