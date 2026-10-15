<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

    // este metodo devuelve el perfil del usuario
    public function profile(Request $request)
    {
        return new UserResource($request->user());
    }


    // este metodo actualiza el perfil del usuario
    public function update(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'username' => 'sometimes|string|max:255',
            // aqui se valida que el email sea unico
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar_url' => 'nullable|string|url|max:255',
        ]);

        
        // se actualzia el usuario con los datis validos
         $user->update($validatedData);

        // $user->forceFill($validatedData)->save();

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'user' => new UserResource($user),
        ]);
    }

    // este metodo elimina la cuneta del usuario(despues veremos en el equipo si solo desactivamos cuenta, pero se vera despues que queremos)
    public function destroy(Request $request)
    {
        $user = $request->user();

        // como se usa cascadeOnDelete en la bd, esto borra todo lo relacionado con el usuario, (playlist, favoritos, y asi)
        $user->delete();

        // etso elimina los tokens del usuario
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Cuenta eliminada permanentemente.',
        ], 200);
    }

}
