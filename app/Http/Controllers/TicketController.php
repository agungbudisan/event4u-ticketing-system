<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
        $this->middleware('admin')->except(['show']);
    }

    public function index(Event $event)
    {
        $tickets = $event->tickets;
        return view('admin.tickets.index', compact('tickets', 'event'));
    }

    public function eventSelector()
    {
        $events = Event::with(['category', 'tickets'])
            ->orderBy('start_event')
            ->get();

        return view('admin.tickets.events', compact('events'));
    }

    public function create(Event $event)
    {
        return view('admin.tickets.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'ticket_class' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quota_avail' => 'required|integer',
        ]);

        $event->tickets()->create([
            'ticket_class' => $request->ticket_class,
            'description' => $request->description,
            'price' => $request->price,
            'quota_avail' => $request->quota_avail,
        ]);

        return redirect()->route('admin.tickets.index', $event)->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        return view('admin.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'ticket_class' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quota_avail' => 'required|integer',
        ]);

        $ticket->update([
            'ticket_class' => $request->ticket_class,
            'description' => $request->description,
            'price' => $request->price,
            'quota_avail' => $request->quota_avail,
        ]);

        return redirect()->route('admin.tickets.index', $ticket->event)->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        $event = $ticket->event;
        $ticket->delete();

        return redirect()->route('events.tickets.index', $event)->with('success', 'Ticket deleted successfully.');
    }
}
