<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function unauthorized() {
        return setErrorResponse('Não autorizado', 401);
    }

    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|digits:11|unique:users,cpf',
            'password' => 'required|confirmed'
        ]);

        $newUser = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'cpf' => $fields['cpf'],
            'password' => Hash::make($fields['password']),
        ]);

        $token = Auth::attempt([
            'cpf' => $fields['cpf'],
            'password' => $fields['password'],
        ]);

        if (!$token) {
            return setErrorResponse(
                'Ocorreu um erro desconhecido. Por favor, tente novamente mais tarde'
            );
        }

        return setSuccessResponse('Usuário cadastrado com sucesso!', [
            'user' => $newUser,
            'token' => $token
        ], 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'cpf' => 'required|digits:11',
            'password' => 'required'
        ]);

        $token = Auth::attempt([
            'cpf' => $fields['cpf'],
            'password' => $fields['password'],
        ]);

        if (!$token) {
            return setErrorResponse('CPF e/ou senhas estão incorretos', 401);
        }

        return setSuccessResponse('', [
            'token' => $token
        ]);
    }

    public function validateToken() {
        return setSuccessResponse('', [
            'user' => Auth::user()->with('units')->first()
        ]);
    }

    public function logout() {
        Auth::logout();

        return setSuccessResponse('');
    }
}
