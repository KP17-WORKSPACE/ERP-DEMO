<?php

namespace App\Http\Controllers;

use App\User;
use App\Ticket;
use App\Comment;
use App\Category;
use App\Priority;
use App\SmNotification;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class SmTicketController extends Controller
{


    //category
    function category()
    {
        try{
            $itemCategories = Category::all();
            return view('backEnd.tickets.category', compact('itemCategories'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function category_store(Request $r)
    {
        $this->validate($r, [
            'name' => 'required|string|unique:categories'
        ]);
        try{
            Category::create($r->all());
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('ticket.category');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function category_edit($id)
    {
        try{
            $editData = Category::findOrFail($id);
            $itemCategories = Category::all();
            return view('backEnd.tickets.category', compact('itemCategories', 'editData'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function category_update(Request $r, $id)
    {
        $this->validate($r, [
            'name' => 'required|string|unique:categories,name,' . $id
        ]);
        try{
            Category::find($id)->update($r->all());
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('ticket.category');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function category_delete_view($id)
    {
        try{
            $url = route('ticket.category_delete', $id);
            return view('backEnd.tickets.modal', compact('url'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function category_delete($id)
    {
        try{
            Category::find($id)->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('ticket.category');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    // priority
    function priority()
    {
        try{
            $itemCategories = Priority::all();
            return view('backEnd.tickets.priority', compact('itemCategories'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function priority_store(Request $r)
    {
        $this->validate($r, [
            'name' => 'required|string|unique:priorities'
        ]);
        
        try{
            Priority::create($r->all());
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('ticket.priority');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function priority_edit($id)
    {
        
        try{
            $editData = Priority::findOrFail($id);
            $itemCategories = Priority::all();
            return view('backEnd.tickets.priority', compact('itemCategories', 'editData'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function priority_update(Request $r, $id)
    {
        $this->validate($r, [
            'name' => 'required|string|unique:priorities,name,' . $id
        ]);
        
        try{
            Priority::find($id)->update($r->all());
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('ticket.priority');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function priority_delete_view($id)
    {
       
        try{
            $url = route('ticket.priority_delete', $id);
            return view('backEnd.tickets.modal', compact('url'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function priority_delete($id)
    {
        
        try{
            Priority::find($id)->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('ticket.priority');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    // ticket

    function index()
    {
        
        try{
            $category = Category::latest()->get();
            $priority = Priority::latest()->get();
            $ticket = Ticket::latest()->get();
            return view('backEnd.tickets.ticket', compact('category', 'priority', 'ticket'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    function add_ticket()
    {
        
        try{
            $category = Category::latest()->get();
            $priority = Priority::latest()->get();
            $user = \App\User::where('role_id', 2)->get();
            $user_agent = \App\User::where('role_id', '=', 3)->get();
            return view('backEnd.user.add_ticket', compact('category', 'priority', 'user', 'user_agent'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_store(Request $r)
    {
        $this->validate($r, [
            'subject' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|integer',
            'priority' => 'required|integer',
            'user' => 'required|integer',
            'active_status' => 'required|integer',
            'user_agent' => 'required|integer'
        ]);
        
        try{
            Ticket::create([
                'user_id' => $r->user,
                'assign_user' => $r->user_agent,
                'subject'   => $r->subject,
                'description'   => $r->description,
                'category_id'   => $r->category,
                'priority_id'   => $r->priority,
                'active_status'   => $r->active_status,
            ]);
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('admin.ticket_list');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_view($id)
    {
        
        try{
            $data = Ticket::findOrFail($id);
            $comment = Comment::where('ticket_id', $data->id)->get();
            $allcom = Comment::all();
            // return  $data;
            return view('backEnd.user.ticket-view', compact('data', 'comment', 'allcom'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function comment_store(Request $r)
    {
        $this->validate($r, [
            'comment' => 'required|string',
            'file'       => 'sometimes|required|mimes:doc,pdf,docx,jpg,jpeg,png',
        ]);
        
        try{
            $ticket = Ticket::findOrFail($r->id);
            if ($ticket) {
                $data = Comment::create([
                    'user_id' => Auth::user()->id,
                    'client_id' => $ticket->user_id,
                    'ticket_id'   => $ticket->id,
                    'comment'   => $r->comment,
                    'file'  => null
                ]);
                $fileName = "";
                if ($r->file('file') != "") {
                    $file = $r->file('file');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/comment/', $fileName);
                    $data->file = 'public/uploads/comment/' . $fileName;
                    $data->save();
                }
    
                $message = 'Comment on your ticket';
                $userdata = new SmNotification();
                $userdata->user_id = Auth::user()->id;
                $userdata->ticket_id = $ticket->id;
                $userdata->role_id = Auth::user()->role_id;
                $userdata->message = $message;
                $userdata->link = route('user.ticket_view', $ticket->id);
                $userdata->received_id = $ticket->user_id;
                $userdata->save();
                return redirect()->back();
            } else {
                Toastr::error('Comment not send !', 'Failed');
                return redirect()->back(); 
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    function ticket_edit($id)
    {
        
        try{
            $editData = Ticket::findOrFail($id);
            $category = Category::latest()->get();
            $priority = Priority::latest()->get();
            $user = \App\User::where('role_id', 2)->get();
            $user_agent = \App\User::where('role_id', '=', 3)->get();
            return view('backEnd.user.add_ticket', compact('category', 'priority', 'editData', 'user', 'user_agent'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    function ticket_update(Request $r, $id)
    {
        $this->validate($r, [
            'subject' => 'required|string',
            'description' => 'required|string',
            'user_agent' => 'required|integer',
            'user' => 'sometimes|nullable|integer',
            'category' => 'required|integer',
            'active_status' => 'required|integer',
            'priority' => 'required|integer'
        ]);
        try{
            $ticket = Ticket::findOrFail($id);
            if (Auth::user()->role_id == 1) {
                if (isset($r->user_agent) && $ticket->active_status == 0) {
                    $data = new SmNotification();
                    $data->user_id = Auth::user()->id;
                    $data->ticket_id = $ticket->id;
                    $data->role_id = Auth::user()->role_id;
                    $data->message = 'Check this ticket';
                    $data->link = route('admin.ticket_view', $ticket->id);
                    $data->received_id = $r->user_agent;
                    $data->save();
                }
                if (!isset($r->user_agent)) {
                    if ($r->active_status == 3) {
                        $message = 'Close this ticket';
                    } elseif ($r->active_status == 2) {
                        $message = 'Complete this ticket';
                    } else {
                        $message = 'Ongoing this ticket';
                    }
                    $data = new SmNotification();
                    $data->user_id = Auth::user()->id;
                    $data->ticket_id = $ticket->id;
                    $data->role_id = $ticket->user->role_id;
                    $data->message = $message;
                    $data->link = route('user.ticket_view', $ticket->id);
                    $data->received_id = $r->user_agent;
                    $data->save();
                }
    
                $ticket->update([
                    'subject'   => $r->subject,
                    'description'   => $r->description,
                    'assign_user'   => $r->user_agent,
                    'category_id'   => $r->category,
                    'priority_id'   => $r->priority,
                    'active_status'   => $r->active_status,
                ]);
            } else {
                $ticket->update([
                    'subject'   => $r->subject,
                    'description'   => $r->description,
                    'category_id'   => $r->category,
                    'priority_id'   => $r->priority,
                    'active_status'   => $r->active_status,
                ]);
                if ($r->active_status == 2) {
    
                    $data = new SmNotification();
                    $data->user_id = Auth::user()->id;
                    $data->ticket_id = $ticket->id;
                    $data->role_id = Auth::user()->role_id;
                    $data->message = 'Complete this ticket';
                    $data->link = route('admin.ticket_view', $ticket->id);
                    $data->received_id = 1;
                    $data->save();
    
                    $message = 'Complete this ticket';
                    $userdata = new SmNotification();
                    $userdata->user_id = Auth::user()->id;
                    $userdata->ticket_id = $ticket->id;
                    $userdata->role_id = Auth::user()->role_id;
                    $userdata->message = $message;
                    $userdata->link = route('user.ticket_view', $ticket->id);
                    $userdata->received_id = $ticket->user_id;
                    $userdata->save();
                }
            }
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('admin.ticket_list');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


    function ticket_search(Request $res)
    {
        
        try{
            $app = Ticket::select('id', 'subject', 'user_id', 'category_id', 'assign_user', 'description', 'priority_id', 'active_status')
            ->orderBy('id', 'desc');
            if ($res->category != null) {
                $app->where('category_id', 'like', '%' . $res->category . '%');
            }
            if ($res->priority != null) {
                $app->where('priority_id', 'like', '%' . $res->priority . '%');
            }
            if ($res->active_status != null) {
                $app->where('active_status', 'like', '%' . $res->active_status . '%');
            }
            $ticket = $app->get();
            $category = Category::latest()->get();
            $priority = Priority::latest()->get();
            return view('backEnd.tickets.ticket', compact('category', 'priority', 'ticket'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_delete_view($id)
    {
        try{
            $url = route('admin.ticket_delete', $id);
            return view('backEnd.tickets.modal', compact('url'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_delete($id)
    {
        try{
            Ticket::findOrFail($id)->delete();
            Toastr::success('Operation successful', 'Success');
            return redirect()->route('admin.ticket_list');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function comment_reply(Request $r)
    {
        $this->validate($r, [
            'comment' => 'required|string',
            'file'       => 'sometimes|required|mimes:doc,pdf,docx,jpg,jpeg,png',
        ]);
        try{
            $comment = Comment::find($r->comment_id);
            $data = Comment::create([
                'user_id' => Auth::user()->id,
                'client_id' => $comment->client_id,
                'ticket_id'   => $comment->ticket_id,
                'comment'   => $r->comment,
                'comment_id'   => $r->comment_id,
                'file'  => null
            ]);
            $fileName = "";
            if ($r->file('file') != "") {
                $file = $r->file('file');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/comment/', $fileName);
                $data->file = 'public/uploads/comment/' . $fileName;
                $data->save();
            }
    
            $message = 'Rpply your comment';
            $userdata = new SmNotification();
            $userdata->user_id = Auth::user()->id;
            $userdata->ticket_id = $comment->ticket_id;
            $userdata->role_id = Auth::user()->role_id;
            $userdata->message = $message;
            $userdata->link = route('user.ticket_view', $comment->ticket_id);
            $userdata->received_id = $comment->client_id;
            $userdata->save();
    
            return redirect()->back();
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
}
