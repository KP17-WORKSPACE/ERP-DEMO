<?php
namespace App\Http\Controllers\Ticket;
use App\Role;
use App\SmToDo;
use App\Ticket;
use App\Comment;
use App\SmStaff;
use App\Category;
use App\Priority;
use App\SmParent;
use App\SmTender;
use App\SmHoliday;
use App\SmStudent;
use App\SmItemSell;
use App\SmAddIncome;
use App\SmQuotation;
use App\InfixInvoice;
use App\SmAddExpense;
use App\SmBankAccount;
use App\SmFeesPayment;
use App\SmItemReceive;
use App\SmNoticeBoard;
use App\SmLeaveRequest;
use App\SmNotification;
use App\SmUpcomingTender;
use App\SmHrPayrollGenerate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\Project\Entities\InfixProject;

class SmUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    function index(){
        
        try{  
            $user_id = Auth()->user()->id;
            $toDoLists = SmToDo::where('complete_status', 'P')->where('created_by', $user_id)->orderBy('date', 'DESC')->take(5)->get();
            $toDoListsCompleteds = SmToDo::where('complete_status', 'C')->where('created_by', $user_id)->get();
            $notices = SmNoticeBoard::select('*')->where('active_status', 1)->get();
            $apply_leaves = SmLeaveRequest::where('active_status', 1)->take(5)->get();
            $holidays = SmHoliday::where('active_status', 1)->get();
            return redirect('/crm-dashboard');
            
            return view('backEnd.user.index',compact('toDoLists','toDoListsCompleteds','notices','apply_leaves','holidays'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }




    function tickets(){
        
        try{
            $ticket=Ticket::where('user_id',Auth::user()->id)->orWhere('assign_user',Auth::user()->id)->orderBy('id','desc')->get();
            return view('backEnd.user.ticket-list',compact('ticket'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function add_ticket(){
        try{
            $category=Category::latest()->get();
            $priority=Priority::latest()->get();
            return view('backEnd.user.add_ticket', compact('category','priority') );
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_store(Request $r){
        $this->validate($r,[
            'subject' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|integer',
            'priority' => 'required|integer',
            'active_status' => 'required|integer'
        ]);
        
        try{
            $ticket=Ticket::create([
                'user_id' => Auth::user()->id,
                'subject'   => $r->subject,
                'description'   => $r->description,
                'category_id'   => $r->category,
                'priority_id'   => $r->priority,
                'active_status'   => $r->active_status,
            ]);
    
            $data=new SmNotification();
            $data->user_id = $ticket->user_id;
            $data->ticket_id = $ticket->id;
            $data->role_id = $ticket->user->role_id;
            $data->message = $ticket->user->username.' created a ticket';
            $data->link = route('admin.ticket_view',$ticket->id);
            $data->received_id =1;
            $data->save();
            Toastr::success('Ticket added !', 'Success');
            return redirect()->route('user.ticket');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_edit($id){
        
        try{
            $editData=Ticket::findOrFail($id);
            $category=Category::latest()->get();
            $priority=Priority::latest()->get();
            return view('backEnd.user.add_ticket', compact('category','priority','editData') );
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_update(Request $r,$id){
        $this->validate($r,[
            'subject' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|integer',
            'priority' => 'required|integer',
            'active_status' => 'required|integer'
        ]);
        
        try{
            Ticket::findOrFail($id)->update([
                'user_id' => Auth::user()->id,
                'subject'   => $r->subject,
                'description'   => $r->description,
                'category_id'   => $r->category,
                'priority_id'   => $r->priority,
                'active_status'   => $r->active_status,
            ]);
            Toastr::success('Ticket updated !', 'Success');
            return redirect()->route('user.ticket');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_delete_view($id){
        try{
            $url=route('user.ticket_delete',$id);
            return view('backEnd.tickets.modal', compact('url'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_delete($id){
        try{
            Ticket::findOrFail($id)->delete();
            return redirect()->route('user.ticket')->with('message-danger','Ticket deleted !');
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function ticket_view($id){

        try{
            $data=Ticket::findOrFail($id);
            $comment=Comment::where('client_id',Auth::user()->id)->where('ticket_id',$data->id)->get();
            return view('backEnd.user.ticket-view', compact('data','comment'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    function comment_store(Request $r){
        $validator=$this->validate($r,[
            'comment' => 'required|string',
            'file'       =>'sometimes|nullable|mimes:doc,pdf,docx,jpg,jpeg,png',
        ]);
        
        try{
            //dd($r->all());
            $ticket=Ticket::findOrFail($r->id);
            if ($ticket) {
                $data=Comment::create([
                    'user_id' => Auth::user()->id,
                    'client_id' => Auth::user()->id,
                    'ticket_id'   => $ticket->id,
                    'comment'   => $r->comment,
                    'file'  =>null
                ]);

                $fileName = ""; 
                if($r->file('file') != ""){
                    $file = $r->file('file');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/comment/', $fileName);
                    $data->file= 'public/uploads/comment/'.$fileName;
                    $data->save();
                }

                $data=new SmNotification();
                $data->user_id = Auth::user()->id;
                $data->ticket_id = $ticket->id;
                $data->role_id = Auth::user()->role_id;
                $data->message = $ticket->user->username.' comment on this ticket';
                $data->link = route('admin.ticket_view',$ticket->id);
                $data->received_id =1;
                $data->save();
                return redirect()->back();
            }
            else {
                Toastr::error('Comment not send !', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
       }
        function reopen_ticket($id){
            
           try{
                $ticket=Ticket::findOrFail($id);
                if($ticket->active_status == 3){
                
                $ticket->update([
                    'active_status' => 0
                ]);
                $data=new SmNotification();
                $data->user_id = Auth::user()->id;
                $data->ticket_id = $ticket->id;
                $data->role_id = Auth::user()->role_id;
                $data->message = $ticket->user->username.' re open  this ticket';
                $data->link = route('admin.ticket_view',$ticket->id);
                $data->received_id =1;
                $data->save();
                if ($ticket->assign_user) {
                    $data=new SmNotification();
                    $data->user_id = Auth::user()->id;
                    $data->ticket_id = $ticket->id;
                    $data->role_id = Auth::user()->role_id;
                    $data->message = $ticket->user->username.' re open  this ticket';
                    $data->link = route('admin.ticket_view',$ticket->id);
                    $data->received_id = $ticket->assign_user;
                    $data->save();
                }
                Toastr::success('Ticket reopen !', 'Success');
                return redirect()->back();
                }
                Toastr::error('Ticket already open !', 'Failed');
                return redirect()->back();
            }catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
        }
        function active_ticket(){
            try{
                $ticket=Ticket::where('user_id',Auth::user()->id)->where('active_status',0)->get();
                return view('backEnd.user.ticket-list',compact('ticket'));
            }catch (\Exception $e) {
               Toastr::error('Operation Failed', 'Failed');
               return redirect()->back(); 
            }
        }
        
        function complete_ticket(){
            try{
                $ticket=Ticket::where('user_id',Auth::user()->id)->where('active_status',2)->get();
                return view('backEnd.user.ticket-list',compact('ticket'));
            }catch (\Exception $e) {
               Toastr::error('Operation Failed', 'Failed');
               return redirect()->back(); 
            }
        }
        function comment_reply(Request $r){
            $this->validate($r,[
                'comment' => 'required|string',
                'file'       =>'sometimes|required|mimes:doc,pdf,docx,jpg,jpeg,png',
            ]);
            
            
            try{
                $comment=Comment::find($r->comment_id);
                $data=Comment::create([
                    'user_id' => Auth::user()->id,
                    'client_id' => $comment->client_id,
                    'ticket_id'   => $comment->ticket_id,
                    'comment'   => $r->comment,
                    'comment_id'   => $r->comment_id,
                    'file'  => null
                ]);
                $fileName = ""; 
                if($r->file('file') != ""){
                    $file = $r->file('file');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/comment/', $fileName);
                    $data->file= 'public/uploads/comment/'.$fileName;
                    $data->save();
                }
                $data=new SmNotification();
                $data->user_id = Auth::user()->id;
                $data->ticket_id = $comment->ticket_id;
                $data->role_id = Auth::user()->role_id;
                $data->message = Auth::user()->username.' reply on your comment';
                $data->link = route('admin.ticket_view',$comment->ticket_id);
                $data->received_id = $comment->user_id;
                $data->save();
                return redirect()->back();
            }catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back(); 
            }
        }
}
