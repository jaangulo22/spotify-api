<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $isArtist = $request->input('account_type') === 'artist' || $request->filled('stage_name');

        $request->validate([
            'username'   => 'required|string|max:255|unique:users,username',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'password'   => 'required|string|min:8',
            // Permitir stage_name si ya existe pero su user_id es NULL (nombre reclamable tras rechazo)
            'stage_name' => $isArtist
                ? ['required', 'string', 'max:255',
                    Rule::unique('artists', 'stage_name')->whereNotNull('user_id')]
                : 'nullable|string|max:255',
        ], [
            'stage_name.required' => 'El nombre artístico es obligatorio para registrarse como artista.',
            'stage_name.unique'   => 'Este nombre artístico ya se encuentra registrado por otro usuario activo.',
            'email.unique'        => 'Este correo electrónico ya está registrado.',
            'username.unique'     => 'Este nombre de usuario ya está en uso.',
        ]);

        $user = DB::transaction(function () use ($request, $isArtist) {
            $user = User::create([
                'role_id'  => 1,
                'username' => $request->username,
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            if ($isArtist) {
                $stageName = trim($request->stage_name);

                // Si el nombre artístico existe pero sin user_id (fue rechazado), lo reclamamos
                $existing = Artist::where('stage_name', $stageName)->whereNull('user_id')->first();

                if ($existing) {
                    $existing->update([
                        'user_id'             => $user->id,
                        'verification_status' => 'pending',
                    ]);
                } else {
                    Artist::create([
                        'user_id'             => $user->id,
                        'stage_name'          => $stageName,
                        'verification_status' => 'pending',
                    ]);
                }
            }

            return $user;
        });

        $token = $user->createToken('web')->plainTextToken;

        // Cargar relación solo si es artista para evitar errores en oyentes
        if ($isArtist) {
            $user->load('artist');
        }

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 201);
    }
}
