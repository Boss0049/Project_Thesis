<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Group;
use App\Classroom;
use Storage;
use Auth;

class GroupController extends Controller
{
    public function index()
    {
        $classrooms = Group::whereHas('members', function ($query) {
            $query->where([['user_id', '=', Auth::id()]]);
        })->get();
        return response()->json($classrooms);
    }

    public function show($classroom_id)

    {
        // $groups = Group::get()->load('members', 'members.role');
        $classrooms = Group::whereHas('members', function ($query) use ($classroom_id) {
            $query->where([['user_id', '=', Auth::id()], ['classroom_id', $classroom_id]]);
        })->get()->load('members.role');
        return response()->json($classrooms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:191',
            'type' => 'required'
        ]);
        $filename = 'Project_' . str_random(6);;

        $dir = '/';
        $recursive = false; // Get subdirectories also?
        $contents = collect(Storage::cloud()->listContents($dir, $recursive));

        $dir = $contents->where('type', '=', 'dir')
            ->where('filename', '=', 'Project_all')
            ->first(); // There could be duplicate directory names!

        // Store a demo file
        // Storage::cloud()->put($filename, 'Hello World');
        Storage::cloud()->makeDirectory($dir['path'] . "/" . $filename);

        // Get the file to find the ID
        $dir = $dir['path'];
        $recursive = false; // Get subdirectories also?
        $contents = collect(Storage::cloud()->listContents($dir, $recursive));
        $file = $contents
            ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
            ->first(); // there can be duplicate file names!

        $service = Storage::cloud()->getAdapter()->getService();
        $permission = new \Google_Service_Drive_Permission();
        $permission->setRole('writer', 'reader');
        $permission->setType('anyone');
        $permission->setAllowFileDiscovery(false);
        $permissions = $service->permissions->create($file['basename'], $permission);

        $classroom = Group::create([
            'classroom_id' => $request->classroom_id,
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'path' => Storage::cloud()->url($file['path'])
        ]);
        $classroom->members()->attach(Auth::id());
        $classroom->members()->attach($request->user_idstu);
        $classroom->members()->attach($request->user_idta);

        return response()->json($classroom);
    }

    public function destroy($classroom_id)
    {
        Group::findOrFail($classroom_id)->delete();
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $classroom_id)
    {
        if (Auth::user()->classroom()->findOrFail($classroom_id) && Auth::user()->role->actions != 'student') {
            $classroom = Group::findOrFail($classroom_id);
            $classroom->name = $request->name;
            $classroom->description = $request->description;
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
