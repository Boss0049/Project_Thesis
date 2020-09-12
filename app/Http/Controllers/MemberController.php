<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Classroom;
use App\Group;
use App\User;
use Auth;

class MemberController extends Controller
{
    public function index()
    {
        $users = User::get()->load('role');

        return response()->json($users);
    }

    public function show($classroom_id)
    {
        $members = Classroom::findOrFail($classroom_id)->members->load('role');

        if (!$members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }
        $results = $members->sortBy('student_id')->values()->all();
        return response()->json($results);
    }

    public function showstu($classroom_id)
    {
        $members = Classroom::findOrFail($classroom_id)->members->where('role_id', '=', 2)->load('role');

        if (!$members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }

        return response()->json($members);
    }

    public function showtu($classroom_id)
    {
        $members = Classroom::findOrFail($classroom_id)->members->where('role_id', '=', 1)->load('role');

        if (!$members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }

        return response()->json($members);
    }

    public function add(Request $request)
    {
        $classroom = Classroom::findOrFail($request->classroom_id);
        $user_id2 = array();
        $users = User::where('grade_id', "=", $request->user_id)->get();
        for ($i = 0; $i < count($users); $i++) {
            array_push($user_id2, $users[$i]->id);
        }
        if (!$classroom->members->contains($user_id2)) {
            $classroom->members()->attach($user_id2);

            return response()->json($classroom);
        }
        return response()->json(['error' => 'this user already member of this Group'], 403);
    }

    public function addmember(Request $request)
    {
        $classroom = Group::findOrFail($request->group_id);

        if (!$classroom->members->contains($request->user_id)) {
            $classroom->members()->attach($request->user_id);

            return response()->json($classroom);
        }

        return response()->json(['error' => 'this user already member of this Group'], 403);
    }

    public function remove(Request $request)
    {
        $classroom = Classroom::findOrFail($request->classroom_id);
        if ($classroom->members->contains($request->user_id)) {
            $classroom->members()->detach($request->user_id);
            return response()->json($classroom);
        }

        return response()->json(['error' => "this user isn't member of this classroom"], 403);
    }
}
