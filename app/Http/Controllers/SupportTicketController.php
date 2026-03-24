<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = DB::table('support_tickets')
            ->orderBy('created_at', 'desc')
            ->get();
        $userType = Session::get('user_type');
        return view('support.index', compact('tickets', 'userType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'attachments.*' => 'file|max:10240',
        ]);

        $attachmentPaths = [];

        if ($request->hasFile('attachments')) 
        {
            foreach ($request->file('attachments') as $file) 
            {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/attachments', $filename);
                $attachmentPaths[] = 'storage/attachments/' . $filename;
            }
        }

        DB::table('support_tickets')->insert([
            'ticket_id' => strtoupper(Str::random(8)),
            'software_name' => Session::get('software_name'),
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'Open',
            'attachments' => json_encode($attachmentPaths),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('support.index')->with('success', 'Support ticket created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'status' => 'required|in:Open,In Progress,Resolved,Closed',
            'attachments.*' => 'file|max:10240',
        ]);

        $ticket = DB::table('support_tickets')->where('id', $id)->first();
        $attachmentPaths = $ticket->attachments ? json_decode($ticket->attachments, true) : [];

        if ($request->hasFile('attachments')) 
        {
            foreach ($request->file('attachments') as $file) 
            {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/attachments', $filename);
                $attachmentPaths[] = 'storage/attachments/' . $filename;
            }
        }

        DB::table('support_tickets')->where('id', $id)->update([
            'software_name' => Session::get('software_name'),
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'attachments' => json_encode($attachmentPaths),
            'updated_at' => now(),
        ]);

        return redirect()->route('support.index')->with('success', 'Ticket updated successfully.');
    }

    public function toggleStatus($id)
    {
        $ticket = DB::table('support_tickets')->where('id', $id)->first();

        if (!$ticket) 
        {
            return redirect()->route('support.index')->with('error', 'Ticket not found.');
        }

        $newStatus = $ticket->status === 'Open' ? 'Resolved' : 'Open';

        DB::table('support_tickets')->where('id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        return redirect()->route('support.index')->with('success', $newStatus === 'Resolved' ? 'Ticket resolved.' : 'Ticket reopened.');
    }
}
