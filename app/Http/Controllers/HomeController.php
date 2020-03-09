<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $query=Image::where('user_id',auth()->id());
        if($request->category){
          $query->where('category',$request->category);
        }

        if($request->sort_by){
          $order=($request->sort_by=='oldest')?'ASC':'DESC';
          $query->orderBy('created_at',$order);
        }else{
            $query->orderBy('created_at','desc');
        }

        $data['images']=$query->paginate(8);

        return view('home',$data);
    }
}
