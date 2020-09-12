<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ReplyMail;
use Illuminate\Support\Facades\Mail;
use App\User;
use Illuminate\Support\Facades\Auth;

class MailsendController extends Controller
{
    public function mailsend(Request $request)
    {
        foreach ($request->get('group_id') as $group) {
            $tttt = User::whereHas('group', function ($query) use ($group) {
                $query->where([['group_id', '=', $group]]);
            })->get();
        }

        $details = [];

        foreach ($tttt as $ttt) {
            if ($ttt->email != Auth::user()->email) {
                Mail::to($ttt->email)->send(new ReplyMail($details));
            }
        }
        return response()->json(['sucessful' => true]);
    }
}
