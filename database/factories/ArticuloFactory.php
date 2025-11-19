<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Articulo;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Articulo>
 */
class ArticuloFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * El nombre del modelo correspondiente a la fábrica.
     *
     * @var string
     */
    protected $model = Articulo::class;

    /**
     * Define the model's default state.
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Asigna un user_id aleatorio de la base de datos
            'user_id' => User::factory(),
            'titulo' => $this->faker->sentence(5),
            'contenido' => $this->faker->paragraphs(3, true),
            'estado' => $this->faker->randomElement(['publicado', 'borrador']),
        ];
    }
}
