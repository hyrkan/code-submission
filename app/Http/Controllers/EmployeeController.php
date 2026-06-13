<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = \App\Models\Employee::with('user')->get();
        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'role' => 'required|string|in:employee,super admin',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        $user->employee()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'department' => $request->department,
            'position' => $request->position,
            'role' => $request->role,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('employees.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = \App\Models\Employee::with('user')->findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $user = $employee->user;

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'role' => 'required|string|in:employee,super admin',
        ]);

        $userData = [
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($userData);

        $employee->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'department' => $request->department,
            'position' => $request->position,
            'role' => $request->role,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $user = $employee->user;
        
        $user->delete(); // This deletes both user and employee due to cascade on delete.

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
