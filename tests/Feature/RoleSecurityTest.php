<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Articulo;
use Laravel\Sanctum\Sanctum;

class RoleSecurityTest extends TestCase
{
    // Usa RefreshDatabase para asegurar que las migraciones se ejecuten 
    // y la base de datos se limpie entre cada test.
    use RefreshDatabase;

    /**
     * Configuración de la prueba. Se ejecuta antes de cada test.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Crea un usuario de prueba con el perfil especificado.
     */
    protected function createUser(string $perfil = 'usuario común'): User
    {
        // Se asegura que se use el perfil correcto al crear el usuario.
        return User::factory()->create([
            'email' => "test_{$perfil}@example.com",
            'perfil' => $perfil,
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * Prueba que solo el administrador puede eliminar un artículo (DELETE).
     */
    public function test_solo_administrador_puede_eliminar_articulo()
    {
        // Creamos un artículo de prueba.
        $admin = $this->createUser('administrador');
        $articulo = Articulo::factory()->create(['user_id' => $admin->id]);

        // 1. Prueba con Usuario Común (Debe fallar con 403)
        $user = $this->createUser('usuario común');
        Sanctum::actingAs($user, ['usuario común']);

        $response = $this->deleteJson("/api/articulos/{$articulo->id}");
        $response->assertStatus(403)
            ->assertJson(['message' => 'Acceso Denegado. Se requiere uno de los siguientes perfiles: administrador']);


        // 2. Prueba con Administrador (Debe ser exitoso)
        Sanctum::actingAs($admin, ['administrador']);

        // Creamos un nuevo artículo para que el Admin lo borre 
        $articuloABorrar = Articulo::factory()->create(['user_id' => $admin->id]);

        $response = $this->deleteJson("/api/articulos/{$articuloABorrar->id}");
        $response->assertStatus(200)
            ->assertJson(['message' => 'Artículo eliminado exitosamente.']);

        // Verificamos que el artículo fue eliminado
        $this->assertDatabaseMissing('articulos', ['id' => $articuloABorrar->id]);
    }

    /**
     * Prueba que solo admin/editor pueden crear y editar artículos (POST/PUT).
     */
    public function test_solo_admin_y_editor_pueden_crear_articulo()
    {
        // Datos para crear
        $data = [
            'titulo' => 'Nuevo Post de Prueba',
            'contenido' => 'Contenido de prueba para store.',
            'estado' => 'publicado'
        ];

        // 1. Prueba con Usuario Común (Debe fallar con 403)
        $user = $this->createUser('usuario común');
        Sanctum::actingAs($user, ['usuario común']);

        $response = $this->postJson('/api/articulos', $data);
        $response->assertStatus(403)
            ->assertJson(['message' => 'Acceso Denegado. Se requiere uno de los siguientes perfiles: administrador, editor']);

        // 2. Prueba con Editor (Debe ser exitoso)
        $editor = $this->createUser('editor');
        Sanctum::actingAs($editor, ['editor']);

        $response = $this->postJson('/api/articulos', $data);
        $response->assertStatus(201)
            ->assertJson(['message' => 'Artículo creado exitosamente. Estado inicial: publicado']);

        // 3. Prueba con Administrador (Debe ser exitoso)
        $admin = $this->createUser('administrador');
        Sanctum::actingAs($admin, ['administrador']);

        $response = $this->postJson('/api/articulos', $data);
        $response->assertStatus(201);
    }

    /**
     * Prueba que solo el administrador puede acceder a la ruta de administración.
     */
    public function test_solo_administrador_puede_acceder_a_ruta_admin()
    {
        $adminRoute = '/api/admin-route';

        // 1. Prueba con Usuario Común (Debe fallar con 403)
        $user = $this->createUser('usuario común');
        Sanctum::actingAs($user, ['usuario común']);

        $this->getJson($adminRoute)
            ->assertStatus(403)
            ->assertJson(['message' => 'Acceso Denegado. Se requiere uno de los siguientes perfiles: administrador']);

        // 2. Prueba con Editor (Debe fallar con 403)
        $editor = $this->createUser('editor');
        Sanctum::actingAs($editor, ['editor']);

        $this->getJson($adminRoute)
            ->assertStatus(403)
            ->assertJson(['message' => 'Acceso Denegado. Se requiere uno de los siguientes perfiles: administrador']);

        // 3. Prueba con Administrador (Debe ser exitoso)
        $admin = $this->createUser('administrador');
        Sanctum::actingAs($admin, ['administrador']);

        $this->getJson($adminRoute)
            ->assertStatus(200)
            ->assertJson(['message' => 'Acceso Autorizado. Perfil de Administrador verificado por Middleware.']);

        // 4. Prueba sin autenticación (Invitado) (Debe fallar con 401)

        // CORRECCIÓN FINAL: Usamos forgetGuards() para limpiar el estado del Guard de Auth
        // de forma segura, evitando el TypeError. Esto simula un estado de no autenticado.
        $this->app['auth']->forgetGuards();

        $this->getJson($adminRoute)
            ->assertStatus(401); // Espera un 401 (Unauthorized) correctamente
    }
}
