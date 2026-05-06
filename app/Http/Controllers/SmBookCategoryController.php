<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SmBookCategory;

class SmBookCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookCategories = SmBookCategory::all();
        return view('backEnd.library.bookCategoryList', compact('bookCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $request->validate([
           'category_name' => "required|unique:sm_book_categories,category_name",
        ]);

       
       $categories = new SmBookCategory();
       $categories->category_name = $request->category_name;
       $results = $categories->save();

       if($results){
           return redirect('book-category-list')->with('message-success', 'New Category has been added successfully');
       }else{
           return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editData = SmBookCategory::find($id);
        $bookCategories = SmBookCategory::all();
        return view('backEnd.library.bookCategoryList', compact('bookCategories', 'editData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
           'category_name' => "required|unique:sm_book_categories,category_name,".$id,
        ]);

       
       $categories =  SmBookCategory::find($id);
       $categories->category_name = $request->category_name;
       $results = $categories->update();

       if($results){
           return redirect('book-category-list')->with('message-success', ' Category has been updated successfully');
       }else{
           return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = SmBookCategory::destroy($id);
        if($result){
            return redirect('book-category-list')->with('message-success-delete', 'Book Category has been deleted successfully');
        }else{
            return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
        }
    }

    public function deleteBookCategoryView($id){
         return view('backEnd.library.deleteBookCategoryView', compact('id'));
    }

    public function deleteBookCategory($id){
        $result = SmBookCategory::destroy($id);
        if($result){
            return redirect('book-category-list')->with('message-success-delete', 'Book Category has been deleted successfully');
        }else{
            return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
        }
    }
}
