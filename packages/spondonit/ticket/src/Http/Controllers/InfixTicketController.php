<?php

namespace Spondonit\Ticket\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Category;

class InfixTicketController extends Controller
{
    // public function index(){
    // 	return view('ticket::tickets');
    // }
public function __construct()
{
    $this->middleware('auth');
}

public function index()
{
    $tickets = InfixTicket::paginate(10);
    $categories = InfixCategory::all();

    return view('ticket::create', compact('tickets', 'categories'));
} 

    public function create()
    {
        $categories = Category::all();

        return view('tickets.create', compact('categories'));
    }


    public function store(Request $request, AppMailer $mailer)
    {
        $this->validate($request, [
                'title'     => 'required',
                'category'  => 'required',
                'priority'  => 'required',
                'message'   => 'required'
            ]);

            $ticket = new Ticket([
                'title'     => $request->input('title'),
                'user_id'   => Auth::user()->id,
                'ticket_id' => strtoupper(str_random(10)),
                'category_id'  => $request->input('category'),
                'priority'  => $request->input('priority'),
                'message'   => $request->input('message'),
                'status'    => "Open",
            ]);
            $ticket->save();

            $mailer->sendTicketInformation(Auth::user(), $ticket);

            return redirect()->back()->with("status", "A ticket with ID: #$ticket->ticket_id has been opened.");
    }

    public function userTickets()
    {
        $tickets = Ticket::where('user_id', Auth::user()->id)->paginate(10);
        $categories = Category::all();

        return view('tickets.user_tickets', compact('tickets', 'categories'));
    }

    public function show($ticket_id)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

        $category = $ticket->category;

        return view('tickets.show', compact('ticket', 'category'));
    }




    public function postComment(Request $request, AppMailer $mailer)
{
    $this->validate($request, [
        'comment'   => 'required'
    ]);

        $comment = Comment::create([
            'ticket_id' => $request->input('ticket_id'),
            'user_id'    => Auth::user()->id,
            'comment'    => $request->input('comment'),
        ]);

        // send mail if the user commenting is not the ticket owner
        if ($comment->ticket->user->id !== Auth::user()->id) {
            $mailer->sendTicketComments($comment->ticket->user, Auth::user(), $comment->ticket, $comment);
        }

        return redirect()->back()->with("status", "Your comment has be submitted.");
}



/* public function show($ticket_id)
{
    $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

    $comments = $ticket->comments;

    $category = $ticket->category;

    return view('tickets.show', compact('ticket', 'category', 'comments'));
} */




public function close($ticket_id, AppMailer $mailer)
{
    $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

    $ticket->status = 'Closed';

    $ticket->save();

    $ticketOwner = $ticket->user;

    $mailer->sendTicketStatusNotification($ticketOwner, $ticket);

    return redirect()->back()->with("status", "The ticket has been closed.");
}




}
