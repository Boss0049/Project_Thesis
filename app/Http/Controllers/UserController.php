<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Rules\PhoneNumber;
use App\User;
use App\Role;

class UserController extends Controller
{
    public function index()
    {
        $me = User::findOrFail(Auth::id())->load('role');
        $me->role->makeVisible('actions');

        return response()->json($me);
    }

    public function indexme()
    {
        $me = Auth::user();

        return response()->json($me);
    }

    public function show()
    {
        $users = User::get()->load('role');
        return response()->json($users);
    }
    public function show2()
    {
        $users = User::where('role_id', '=', 1)->get()->load('role');
        return response()->json($users);
    }

    public function destroy($user_id)
    {
        if (Auth::id() == $user_id) {
            return response()->json(['success' => false]);
        }
        User::findOrFail($user_id)->delete();
        return response()->json(['success' => true]);
    }

    public function getUser($user_id)
    {
        $user = User::findOrFail($user_id)->load('role');
        $user->role->makeVisible('id');
        return response()->json($user);
    }

    public function edit(Request $request, $user_id)
    {
        $request->validate([
            'name' => 'required|max:191',
            'username' => 'required',
            'email' => 'required|email|max:191',
            'role_id' => 'required',
            'password' => 'nullable|confirmed'
        ]);

        $user = User::findOrFail($user_id);

        if (
            User::where('username', $request->username)->count() &&
            $user->username != $request->username
        ) {
            $errors = [
                'errors' => [
                    "username" => ['Username is already exist.']
                ]
            ];
            return response()->json($errors, 403);
        }

        if (
            User::where('email', $request->email)->count() &&
            $user->email != $request->email
        ) {
            $errors = [
                'errors' => [
                    "email" => ['Email is already exist.']
                ]
            ];
            return response()->json($errors, 403);
        }

        $filename = null;
        if (strlen($request->avatar_url) > 0) {
            $filename = basename($request->avatar_url);
        }

        if ($filename) {
            Storage::move('avatar/cache/' . $filename, 'avatar/' . $user->id . '/' . $filename);
            $user->avatar = $filename;
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        if (strlen($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json(['success' => true]);
    }

    public function update(Request $request)
    {
        // $request->validate([
        //     // 'name' => 'required',
        //     'facebook' => 'required',
        //     'phone' => ['required', new PhoneNumber],
        // ]);

        $me = Auth::user();
        $me->facebook = $request->facebook;
        $me->phone = $request->phone;
        // $me->email = $request->email;
        $me->save();

        return response()->json(['success' => true]);
    }

    public function updatestatus(Request $request)
    {
        if ($request->role_id == 2) {
            $request->validate([
                'role_id' => 'required',
                'grade_id' => 'required',
                'student_id' => 'required'
            ]);

            $me = Auth::user();
            $me->role_id = $request->role_id;
            $me->grade_id = $request->grade_id;
            $me->student_id = $request->student_id;
        } else {
            $request->validate([
                'role_id' => 'required'
            ]);

            $me = Auth::user();
            $me->role_id = $request->role_id;
            $me->grade_id = null;
            $me->student_id = null;
        }
        // $me->email = $request->email;
        $me->save();

        return response()->json(['success' => true]);
    }

    public function accept(Request $request)
    {
        $me = User::findOrFail($request->user_id);
        $me->role_id = $request->role_id;
        // $me->email = $request->email;
        $me->save();

        return response()->json(['success' => true]);
    }

    public function setrole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|numeric',
            'grade_id' => 'required',
            'student_id' => 'student_id|max:12'
        ]);

        $me = Auth::user();
        $me->role_id = $request->role_id;
        $me->grade_id = $request->grade_id;
        $me->student_id = $request->student_id;
        $me->save();

        return response()->json(['success' => true]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            $errors = [
                'errors' => [
                    "password" => ['Incorrect password']
                ]
            ];
            return response()->json($errors, 403);
        }

        $me = Auth::user();
        $me->password = bcrypt($request->new_password);
        $me->save();

        return response()->json(['success' => true]);
    }

    public function uploadProfile(Request $request)
    {
        $request->validate([
            'file' => 'image|required'
        ]);

        $user = Auth::user();
        $file = $request->file('file')->store('avatar/' . $user->id);

        $uploaded_name = trim(basename($file));
        $user->avatar = $uploaded_name;
        $user->save();

        return response()->json(['success' => true, 'avatar_url' => url('avatar/' . $user->id . '/' . $uploaded_name)]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:60',
            'surname' => 'required|max:60',
            'username' => 'required|max:191|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'role_id' => 'required|numeric',
        ]);

        $filename = null;
        if (strlen($request->avatar_url) > 0) {
            $filename = basename($request->avatar_url);
        }

        if (!Role::find($request->role_id)) {
            return response()->json(['success' => 'false'], 403);
        }

        $user = User::create([
            'name' => $request->name . " " . $request->surname,
            'username' => $request->username,
            'email' => $request->email,
            'avatar' => $filename,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
        ]);

        if ($filename) {
            Storage::move('avatar/cache/' . $filename, 'avatar/' . $user->id . '/' . $filename);
        }

        return response()->json(['success' => true]);
    }

    public function uploadTempProfile(Request $request)
    {
        $request->validate([
            'file' => 'image|required'
        ]);

        $file = $request->file('file')->store('avatar/cache');
        $uploaded_name = trim(basename($file));
        return response()->json([
            'success' => true,
            'avatar_file' => $uploaded_name,
            'avatar_url' => url('avatar/cache/' . $uploaded_name),
        ]);
    }
}
