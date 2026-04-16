<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\roles;

class RoleController extends Controller
{
    public function create()
    {
        return view('pernistion');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = roles::create([
            'name' => $request->name,
            'permissions' => json_encode($request->permissions ?? []),
        ]);

        return redirect()->route('roles.create')->with('success', 'تم إنشاء الدور بنجاح.');
    }
}
