<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use Illuminate\Http\Request;
use App\SmBook;
use App\SmBookCategory;
use App\SmSubject;
use App\SmLibraryMember;
use App\SmStudent;
use App\SmStaff;
use App\SmBookIssue;
use DB;
use Validator;

class SmBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }


    public function index(Request $request)
    {
        $books = SmBook::orderBy('id', 'DESC')->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($books, null);
        }

        return view('backEnd.library.bookList', compact('books'));
    }

    public function addBook(Request $request)
    {
        $categories = SmBookCategory::all();
        $subjects = SmSubject::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['categories'] = $categories->toArray();
            $data['subjects'] = $subjects->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.library.addBook', compact('categories', 'subjects'));
    }

    public function saveBookData(Request $request)
    {
        $input = $request->all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'book_title' => "required",
                'book_category_id' => "required",
                'subject' => "required",
                'user_id' => "required"
            ]);
        } else {
            $validator = Validator::make($input, [
                'book_title' => "required",
                'book_category_id' => "required",
                'subject' => "required"
            ]);
        }

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth()->user();

        if ($user) {
            $user_id = $user->id;
        } else {
            $user_id = $request->user_id;
        }
        $books = new SmBook();
        $books->book_title = $request->book_title;
        $books->book_category_id = $request->book_category_id;
        $books->book_number = $request->book_number;
        $books->isbn_no = $request->isbn_no;
        $books->publisher_name = $request->publisher_name;
        $books->author_name = $request->author_name;
        $books->subject = $request->subject;
        $books->rack_number = $request->rack_number;
        $books->quantity = $request->quantity;
        $books->book_price = $request->book_price;
        $books->details = $request->details;
        $books->post_date = date('Y-m-d');
        $books->created_by = $user_id;
        $results = $books->save();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'New Book has been added successfully.');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        } else {
            if ($results) {
                return redirect('book-list')->with('message-success', 'New Book has been added successfully.');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }

    }

    public function editBook(Request $request, $id)
    {
        $editData = SmBook::find($id);
        $categories = SmBookCategory::all();
        $subjects = SmSubject::all();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['editData'] = $editData->toArray();
            $data['categories'] = $categories->toArray();
            $data['subjects'] = $subjects->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.library.addBook', compact('editData', 'categories', 'subjects'));
    }

    public function updateBookData(Request $request, $id)
    {
        $input = $request->all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'book_title' => "required",
                'book_category_id' => "required",
                'subject' => "required",
                'user_id' => "required"
            ]);
        } else {
            $validator = Validator::make($input, [
                'book_title' => "required",
                'book_category_id' => "required",
                'subject' => "required"
            ]);
        }

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth()->user();

        if ($user) {
            $user_id = $user->id;
        } else {
            $user_id = $request->user_id;
        }

        $books = SmBook::find($id);
        $books->book_title = $request->book_title;
        $books->book_category_id = $request->book_category_id;
        $books->book_number = $request->book_number;
        $books->isbn_no = $request->isbn_no;
        $books->publisher_name = $request->publisher_name;
        $books->author_name = $request->author_name;
        $books->subject = $request->subject;
        $books->rack_number = $request->rack_number;
        $books->quantity = $request->quantity;
        $books->book_price = $request->book_price;
        $books->details = $request->details;
        $books->post_date = date('Y-m-d');
        $books->updated_by = $user_id;
        $results = $books->update();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($results) {
                return ApiBaseMethod::sendResponse(null, 'Book Data has been updated successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        } else {
            if ($results) {
                return redirect('book-list')->with('message-success', 'Book Data has been updated successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }

    public function deleteBookView(Request $request, $id)
    {
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($id, null);
        }
        return view('backEnd.library.deleteBookView', compact('id'));
    }

    public function deleteBook(Request $request, $id)
    {
        $result = SmBook::destroy($id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Book has been deleted successfully');
            } else {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
        } else {
            if ($result) {
                return redirect()->back()->with('message-success', 'Book has been deleted successfully');
            } else {
                return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
            }
        }
    }

    public function memberList(Request $request)
    {
        $activeMembers = SmLibraryMember::where('active_status', '=', 1)->get();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {

            return ApiBaseMethod::sendResponse($activeMembers, null);
        }
        return view('backEnd.library.memberLists', compact('activeMembers'));
    }

    public function issueBooks(Request $request, $member_type, $student_staff_id)
    {

        $memberDetails = SmLibraryMember::where('student_staff_id', '=', $student_staff_id)->first();

        if ($member_type == 2) {
            $getMemberDetails = SmStudent::select('full_name', 'email', 'mobile', 'student_photo')->where('user_id', '=', $student_staff_id)->first();
        } else {
            $getMemberDetails = SmStaff::select('full_name', 'email', 'mobile', 'staff_photo')->where('user_id', '=', $student_staff_id)->first();
        }

        $books = SmBook::all();
        $totalIssuedBooks = SmBookIssue::where('member_id', '=', $student_staff_id)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['memberDetails'] = $memberDetails->toArray();
            $data['books'] = $books->toArray();
            $data['totalIssuedBooks'] = $totalIssuedBooks->toArray();
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.library.issueBooks', compact('memberDetails', 'books', 'getMemberDetails', 'totalIssuedBooks'));
    }

    public function saveIssueBookData(Request $request)
    {
        $input = $request->all();
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $validator = Validator::make($input, [
                'book_id' => "required",
                'due_date' => "required",
                'user_id' => "required"
            ]);
        } else {
            $validator = Validator::make($input, [
                'book_id' => "required",
                'due_date' => "required"
            ]);
        }

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth()->user();

        if ($user) {
            $user_id = $user->id;

        } else {
            $user_id = $request->login_id;
        }
        $bookIssue = new SmBookIssue();
        $bookIssue->book_id = $request->book_id;
        $bookIssue->member_id = $request->member_id;
        $bookIssue->given_date = date('Y-m-d');
        $bookIssue->due_date = date('Y-m-d', strtotime($request->due_date));
        $bookIssue->issue_status = 'I';
        $bookIssue->created_by = $user_id;
        $results = $bookIssue->save();
        $bookIssue->toArray();

        if ($results) {
            $books = SmBook::find($request->book_id);
            $books->quantity = $books->quantity - 1;
            $result = $books->update();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Book Issued  successfully');
            }

            return redirect()->back()->with('message-success', 'Book Issued  successfully');
        } else {

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }

            return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
        }


    }

    public function returnBookView(Request $request, $issue_book_id)
    {
        if(ApiBaseMethod::checkUrl($request->fullUrl())){
            return ApiBaseMethod::sendResponse($issue_book_id, null);
        }
        return view('backEnd.library.returnBookView', compact('issue_book_id'));

    }

    public function returnBook(Request $request, $issue_book_id)
    {

        $user = Auth()->user();
        if ($user) {
            $updated_by = $user->id;

        } else {
            $updated_by = $request->updated_by;

        }
        $return = SmBookIssue::find($issue_book_id);
        $return->issue_status = "R";
        $return->updated_by = $updated_by;
        $results = $return->update();

        if ($results) {

            $books_id = SmBookIssue::select('book_id')->where('id', $issue_book_id)->first();
            $books = SmBook::find($books_id->book_id);
            $books->quantity = $books->quantity + 1;
            $result = $books->update();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse(null, 'Book has been Returned  successfully');
            }
            return redirect()->back()->with('message-success-return', 'Book has been Returned  successfully');
        } else {

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Something went wrong, please try again.');
            }
            return redirect()->back()->with('message-danger-return', 'Something went wrong, please try again');
        }
    }

    public function allIssuedBook(Request $request)
    {
        $books = SmBook::select('id', 'book_title')->where('active_status', 1)->get();
        $subjects = SmSubject::select('id', 'subject_name')->where('active_status', 1)->get();

        $issueBooks = DB::select(DB::raw("SELECT i.*, b.book_title, b.book_number, 
   b.isbn_no, b.author_name, m.member_type, m.student_staff_id, s.subject_name 
   FROM sm_book_issues i
   LEFT JOIN sm_books b ON i.book_id = b.id
   LEFT JOIN sm_library_members m ON i.member_id = m.student_staff_id
   LEFT JOIN sm_subjects s ON b.subject = s.id
   WHERE i.issue_status = 'I'"));

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['books'] = $books->toArray();
            $data['subjects'] = $subjects->toArray();
            $data['issueBooks'] = $issueBooks;
            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.library.allIssuedBook', compact('books', 'subjects', 'issueBooks', 'student_detail'));
    }

    public function searchIssuedBook(Request $request)
    {

        $book_id = $request->book_id;
        $book_number = $request->book_number;
        $subject_id = $request->subject_id;

        $query = '';
        if (!empty($request->book_id)) {
            $query = "AND i.book_id = '$request->book_id'";
        }

        if (!empty($request->book_number)) {
            $query .= "AND b.book_number = '$request->book_number'";
        }

        if (!empty($request->subject_id)) {
            $query .= "AND b.subject = '$request->subject_id'";
        }

        $issueBooks = DB::select(DB::raw("SELECT i.*, b.book_title, b.book_number, 
   b.isbn_no, b.author_name, m.member_type, m.student_staff_id, s.subject_name 
   FROM sm_book_issues i
   LEFT JOIN sm_books b ON i.book_id = b.id
   LEFT JOIN sm_library_members m ON i.member_id = m.student_staff_id
   LEFT JOIN sm_subjects s ON b.subject = s.id
   WHERE i.issue_status = 'I' $query"));

        $books = SmBook::select('id', 'book_title')->where('active_status', 1)->get();
        $subjects = SmSubject::select('id', 'subject_name')->where('active_status', 1)->get();

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['book_id'] = $book_id;
            $data['book_number'] = $book_number;
            $data['subject_id'] = $subject_id;
            $data['books'] = $books->toArray();
            $data['$subjects'] = $subjects->toArray();
            $data['issueBooks'] = $issueBooks;
            return ApiBaseMethod::sendResponse($data, null);
        }
        return view('backEnd.library.allIssuedBook', compact('issueBooks', 'books', 'subjects', 'book_id', 'book_number', 'subject_id'));
    }

    public static function pp($data)
    {
        echo "<pre>";
        print_r($data);
        exit;
    }
}
