<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admins = User::query()
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($request->input('email'), function ($query, $email) {
                return $query->where('email', 'like', '%' . $email . '%');
            })
            ->when($request->input('status') !== null, function ($query) use ($request) {
                $status = (int) $request->input('status');
                return $query->where('is_active', $status);
            })
            ->get();


        return view('admin.admins.list', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.admins.create', ['admin' => new User()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $admin = User::create(
            $request->validate(
             [
                 'name' => 'required|string|min:4',
                 'email' => 'required|email|unique:users,email',
                 'password' => 'required|min:4',
                 'avatar' => 'nullable|string',
             ]
            )
        );

        $admin->avatar = $request->input('avatar');
        $admin->is_active =$request->input('status');
        $admin->save();


        $request->session()->flash('success', 'Admin created successfully');
        return redirect()->route('admins.index', compact('admin'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $admin)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $admin)
    {
        return view('admin.admins.update', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $admin)
    {

        $request->validate([
            'name' => 'required|string|min:4',
            'email' => 'required|email',
            'avatar' => 'nullable|string',
        ]);

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->is_active = $request->input('status');

        if ($request->has('avatar')) {
            $admin->avatar = $request->input('avatar');
        }
        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Admin updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $admin)
    {

        if ($admin->id === auth()->id()) {
            return redirect()->route('admins.index')->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()->route('admins.index');
    }
}
