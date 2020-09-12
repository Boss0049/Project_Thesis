<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\ReplyMail;
use Illuminate\Support\Facades\Mail;
use Auth;
use App\Classroom;
use App\Group;
use App\Assignment;
use App\AssignmentFile;
use App\AssignmentCheck;
use App\FilesAttachment;
use App\Post;
use App\Reply;
use App\User;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    public function show($classroom_id, $id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }
        $assignment = Assignment::findOrFail($id)->load('user', 'group', 'group.members');
        $attachment = $assignment->post->attachments;
        // $replies = Reply::findOrFail($attachment->$id)->get();
        $files = AssignmentFile::where([
            ['user_id', Auth::id()],
            ['assignment_id', $id]
        ])->get();
        // $group = Group::where([
        //     ['user_id', Auth::id()]
        // ])->get();
        $replies = Reply::where([
            ['assignment_id', $id],
        ])->get();
        $status = AssignmentCheck::where([
            ['group_id', $assignment->group->id],
            ['assignment_id', $id]
        ]);
        $is_done = $status->exists();

        return response()->json([
            'assignment' => $assignment,
            'attachment' => $attachment,
            'replies' => $replies,
            // 'group' => $group,
            'uploaded_files' => $files,
            'is_done' => $is_done,
            'status' => $status->first()
        ]);
    }

    public function store(Request $request, $classroom_id)
    {
        $request->validate([
            'title' => 'required|max:191',
            'group_id' => 'required',
        ]);

        $classroom = Classroom::findOrFail($classroom_id);
        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }
        foreach ($request->get('group_id') as $group) {
            $post = Post::create([
                'user_id' => Auth::id(),
                'classroom_id' => $classroom_id,
                'type' => 'assignment',
                'group_id' => $group,
                'role_id' => Auth::user()->role->id,
            ]);

            $test = Assignment::create([
                'user_id' => Auth::id(),
                'classroom_id' => $classroom_id,
                'role_id' => Auth::user()->role->id,
                'post_id' => $post->id,
                'title' => $request->title,
                'detail' => $request->detail,
                'group_id' => $group,
                'due_time' => Carbon::parse($request->duetime),
            ]);
            $test->groupconnect()->attach($group);
        }

        foreach ($request->get('files') as $file) {
            FilesAttachment::where('filepath', $file['filename'])->first()->posts()->attach($post->id);
        }

        $last = Assignment::latest()->first();

        return response()->json($last);
    }

    public function storereply(Request $request, $classroom_id, $id)
    {
        $request->validate([
            'detail' => 'required'
        ]);

        $classroom = Classroom::findOrFail($classroom_id);
        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }

        $replies = Reply::create([
            'user_id' => Auth::id(),
            'assignment_id' => $id,
            'detail' => $request->detail,
        ]);

        $replies->save();
        return response()->json($replies);
    }

    public function upload(Request $request, $classroom_id, $id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }

        // $assignment = Assignment::findOrFail($id);
        // if ($assignment->due_time) {
        //     $now = Carbon::now();
        //     $due = Carbon::createFromFormat("Y-m-d H:i:s", $assignment->due_time);
        //     if ($due->lte($now)) {
        //         return response()->json(['error' => 'This assignment time up!'], 403);
        //     }
        // }

        $original_name = $request->file->getClientOriginalName();
        $file = $request->file('file')->store('file');

        $file = AssignmentFile::create([
            'user_id' => Auth::id(),
            'assignment_id' => $id,
            'name' => $original_name,
            'location' => $file,
        ]);

        return response()->json($file);

        // $original_name = $request->file->getClientOriginalName();
        // $file = $request->file('file')->store('file');

        // $file = AssignmentFile::create([
        //     'user_id' => Auth::id(),
        //     'assignment_id' => $id,
        //     'name' => $original_name,
        //     'location' => $file
        // ]);

        // return response()->json($file);
    }

    public function removeFile(Request $request, $classroom_id, $id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }

        // $assignment = Assignment::findOrFail($id);
        // $now = Carbon::now();
        // $due = Carbon::createFromFormat("Y-m-d H:i:s", $assignment->due_time);
        // if ($due->lte($now)) {
        //     return response()->json(['error' => 'This assignment time up!'], 403);
        // }

        $file = AssignmentFile::findOrFail($request->file_id);
        if ($file->user_id == Auth::id()) {
            Storage::delete($file->location);
            $file->delete();
        } else {
            return response()->json(['sucessful' => false], 403);
        }

        return response()->json(['sucessful' => true]);
    }

    public function confirm(Request $request, $classroom_id, $id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }

        $assignment = Assignment::findOrFail($id);
        // if ($assignment->due_time) {
        //     $now = Carbon::now();
        //     $due = Carbon::createFromFormat("Y-m-d H:i:s", $assignment->due_time);
        //     if ($due->lte($now)) {
        //         return response()->json(['error' => 'This assignment time up!'], 403);
        //     }
        // }

        if (AssignmentCheck::where([
            ['user_id', Auth::id()],
            ['assignment_id', $id]
        ])->exists()) {
            return response()->json(['error' => 'this student already sent.'], 403);
        }

        AssignmentCheck::create([
            'user_id' => Auth::id(),
            'assignment_id' => $id,
            'group_id' => $assignment->group->id,
            'returned' => false
        ]);

        return response()->json(['successful' => true]);
    }

    public function alldone($classroom_id, $id)
    {
        Classroom::findOrFail($classroom_id);
        $all = AssignmentCheck::where('assignment_id', $id)->get()->load('user');
        // $all = AssignmentCheck::where('assignment_id', $id)->get()->load('files', 'user');
        foreach ($all as $assignment) {
            $assignment->files = AssignmentFile::where([
                ['assignment_id', $assignment->assignment_id],
                ['user_id', $assignment->user_id]
            ])->get();
        }

        return response()->json($all);
    }

    //for teacher give them score
    public function update(Request $request, $classroom_id, $id)
    {
        Classroom::findOrFail($classroom_id);
        $assignment = AssignmentCheck::where([
            ['assignment_id', $id],
            ['user_id', $request->user_id]
        ])->first();
        $assignment->returned = true;
        $assignment->comment = $request->comment;
        $assignment->save();

        return response()->json(['successful' => true]);
    }

    //for edit assignment post. only teacher can use
    public function editPost(Request $request, $classroom_id, $id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }

        $post = Assignment::findOrFail($id);
        if (Auth::user()->classroom->find($classroom_id) && Auth::user()->role->actions != 'is_student') {
            $post->post->attachments()->detach();

            foreach ($request->get('files') as $file) {
                FilesAttachment::where('filepath', $file['filename'])->first()->posts()->attach($post->post->id);
            }

            $post->title = $request->title;
            $post->detail = $request->detail;
            $post->due_time = $request->due_time;
            $post->save();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 403);
        }
    }

    public function getPost($classroom_id, $id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }

        if (Auth::user()->classroom->find($classroom_id)) {
            $post = Assignment::findOrFail($id);
            $attachments = $post->post->attachments;
            return response()->json(['post' => $post, 'attachments' => $attachments]);
        } else {
            return response()->json(['success' => false], 403);
        }
    }

    public function destroy($classroom_id, $id)
    {
        $classroom = Classroom::findOrFail($classroom_id);
        if (!$classroom->members->find(Auth::id())) {
            return response()->json(['error' => "You don't have permission to access this."], 403);
        }

        if (Auth::user()->assignments->find($id)) {
            $post = Assignment::findOrFail($id);
            $post->post->delete();
            $post->delete();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 403);
        }
    }
}
