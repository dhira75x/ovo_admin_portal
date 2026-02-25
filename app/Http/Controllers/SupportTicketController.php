<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with('user');

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->paginate(10);

        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'messages.user', 'messages.admin'])->findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Ticket status updated successfully.');
    }

    /**
     * Update the ticket priority.
     */
    public function updatePriority(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        
        $request->validate([
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket->update([
            'priority' => $request->priority
        ]);

        return back()->with('success', 'Ticket priority updated successfully.');
    }

    /**
     * Reply to a ticket
     */
    public function reply(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'message' => 'required|string',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'admin_id' => Auth::id(), // Assuming admin guard
            'message' => $request->message,
        ]);

        // Optionally update status to in_progress if it was open
        if ($ticket->status == 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Reply sent successfully.');
    }
}
