<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Crea un rol si no existe.
     *
     * @param string $roleName
     * @return Role
     */
    public function createRoleIfNotExists($roleName)
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        if ($role->wasRecentlyCreated) {
            Log::info("Rol '{$roleName}' creado.");
        }
        return $role;
    }
}
