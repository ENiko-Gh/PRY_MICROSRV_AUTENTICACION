<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ArticuloController extends Controller
{
    /**
     * Muestra todos los artículos públicos.
     */
    public function index()
    {
        // Solo muestra artículos publicados
        $articulos = Articulo::where('estado', 'publicado')->get();
        return response()->json($articulos);
    }

    /**
     * Muestra un artículo específico.
     */
    public function show(Articulo $articulo)
    {
        // Simplemente devuelve el artículo encontrado por el Route Model Binding
        return response()->json($articulo);
    }

    /**
     * Crea un nuevo artículo. Requiere rol: administrador o editor.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'contenido' => 'required|string',
                'estado' => 'required|in:borrador,publicado',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $articulo = Articulo::create([
            // Usamos Auth::id() para obtener el ID del usuario autenticado por Sanctum
            'user_id' => Auth::id(),
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'estado' => $request->estado,
        ]);

        return response()->json([
            'message' => 'Artículo creado exitosamente. Estado inicial: ' . $articulo->estado,
            'articulo' => $articulo
        ], 201);
    }

    /**
     * Actualiza un artículo existente. Requiere rol: administrador o editor.
     */
    public function update(Request $request, Articulo $articulo)
    {
        // Lógica de validación y actualización (Simplificado para el propósito de la prueba)
        $articulo->titulo = $request->input('titulo', $articulo->titulo);
        $articulo->contenido = $request->input('contenido', $articulo->contenido);
        $articulo->estado = $request->input('estado', $articulo->estado);
        $articulo->save();

        return response()->json([
            'message' => 'Artículo actualizado exitosamente.',
            'articulo' => $articulo
        ], 200);
    }

    /**
     * Elimina un artículo. Requiere rol: administrador.
     */
    public function destroy(Articulo $articulo)
    {
        $articulo->delete();

        return response()->json([
            'message' => 'Artículo eliminado exitosamente.'
        ], 200);
    }
}
