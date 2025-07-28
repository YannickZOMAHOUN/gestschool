<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Affiche le formulaire de création d’un utilisateur.
     */
    public function create()
    {
        // On exclut le rôle 'Parent'
        $roles = Role::where('name', '!=', 'Parent')->get();

        return view('dashboard.users.create', compact('roles'));
    }
   public function index()
{
    try {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'parent');
        })->get();

        return view('dashboard.users.index', compact('users'));
    } catch (\Exception $e) {
        Log::info($e->getMessage());
        abort(404);
    }
}

    /**
     * Enregistre un nouvel utilisateur avec un rôle.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'surname'  => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'role_id'  => 'required|exists:roles,id',
        ]);

        try {
            // Création de l'utilisateur
            $user = User::create([
                'name'     => $validated['name'],
                'surname'  => $validated['surname'],
                'email'    => $validated['email'],
                'phone'    => $validated['phone'],
                'password' => Hash::make($validated['phone']), // Mot de passe initial = téléphone
            ]);

            // Association d’un rôle
            RoleUser::create([
                'user_id' => $user->id,
                'role_id' => $validated['role_id'],
            ]);

            return back() // tu peux modifier la redirection selon le besoin
                ->with('success', 'Utilisateur ajouté avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement d\'un utilisateur : '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'ajout de l\'utilisateur.');
        }
    }

     public function destroy (User $user){
        try {
            $user->delete();
        return redirect()->back();
        }catch (\Exception $e){
            Log::info($e->getMessage());
            abort(404);
        }
    }
}
