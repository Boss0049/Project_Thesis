<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classroom;
use App\User;
use Auth;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::whereHas('members', function ($query) {
            $query->where('user_id', '=', Auth::id());
        })->get()->load('group');
        return response()->json($classrooms);
    }

    public function show($classroom_id)
    {
        $classroom = Classroom::findOrFail($classroom_id)->load('members', 'members.role');

        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }
        if (Auth::user()->role->actions == "is_student") {
            $classroom = $classroom->makeHidden('join_code');
        }
        $posts = $classroom->posts->load('user', 'comments', 'attachments', 'comments.user', 'assignment.assignmentCheck', 'assignment.group.members');
        $posts = $posts->each(function ($item, $key) {
            if (!$item->assignment) {
                $item->makeHidden('assignment');
            }
            if (!$item->attachments->count()) {
                $item->makeHidden('attachments');
            }
        });

        $posts = $posts->sortByDesc('created_at');
        $posts = array_values($posts->toArray());

        return response()->json(['classroom' => $classroom, 'posts' => $posts]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:191'
        ]);

        $join_code = str_random(6);
        $user_id2 = array();
        $offers = collect();
        for ($i = 0; $i < count($request->graed); $i++) {
            $offers->push(User::where('grade_id', "=", $request->graed[$i])->get());
        }
        $offers = $offers->flatten();

        $classroom = Classroom::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'year' => $request->year,
            'grade' => $request->graeddb,
            'semester' => $request->semester,
            'join_code' => $join_code
        ]);
        $classroom->members()->attach($request->tu_id);

        for ($i = 0; $i < count($offers); $i++) {
            array_push($user_id2, $offers[$i]->id);
        }
        $classroom->members()->attach($user_id2);

        return response()->json($classroom);
    }

    public function join(Request $request)
    {
        $classroom = Classroom::where('join_code', $request->code)->first();
        if ($classroom->exists()) {
            if (!$classroom->members()->find(Auth::id())) {
                $classroom->members()->attach(Auth::id());
            }
            return response()->json(['success' => true, 'classroom_id' => $classroom->id]);
        } else {
            return response()->json(['success' => false], 404);
        }
    }

    public function destroy($classroom_id)
    {
        if (Auth::user()->classroom()->findOrFail($classroom_id)) {
            // && Auth::user()->role->actions == 'teacher'
            Classroom::findOrFail($classroom_id)->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 403);
        }
    }

    public function update(Request $request, $classroom_id)
    {
        if (Auth::user()->classroom()->findOrFail($classroom_id)) {
            // && Auth::user()->role->actions != 'student'
            $classroom = Classroom::findOrFail($classroom_id);
            $classroom->name = $request->name;
            $classroom->year = $request->year;
            $classroom->semester = $request->semester;
            $classroom->save();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 403);
        }
    }

    public function getClassroom($classroom_id)
    {
        $classroom = Auth::user()->classroom()->findOrFail($classroom_id);
        return response()->json($classroom);
    }
}
