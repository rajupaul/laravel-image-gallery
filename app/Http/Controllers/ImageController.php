<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use Image as InterventionImage;
use File;
class ImageController extends Controller
{
    public function storeImage(Request $request){
     $request->validate([
     'caption'=>'required|max:255',
     'category'=>'required',
     'image'=>'required|image|mimes:png,jpg,jpeg,bmp'
     ],[
      'category.required'=>'Please select an category'
     ]);

     if($request->hasFile('image')){
      $file=$request->file('image');
      $image_name=time().'.'.$file->getClientOriginalExtension();


     $destinationPath = public_path('/user_images/thumbnail');

     $resize_image = InterventionImage::make($file->getRealPath());

     $resize_image->resize(300, 200, function($constraint){
      // $constraint->aspectRatio();
     })->save($destinationPath . '/' . $image_name);

    

     $file->move(public_path('/user_images'), $image_name);

     } 

     Image::create([
      'user_id'=>auth()->id(),
      'caption'=>$request->caption,
      'category'=>$request->category,
      'image'=>$image_name
     ]);


     return redirect()->back()->with('success','Image uploaded succesfully');

    }
    public function deleteImage(Request $request,Image $image){
     
     if($image->user_id!=auth()->id()){
       abort(403);
     }
      
       $file_path=public_path('/user_images/'.$image->image);
      if(File::exists($file_path)){
        File::delete($file_path);
      }
      $file_path_thumb=public_path('/user_images/thumbnail/'.$image->image);
      if(File::exists($file_path_thumb)){
        File::delete($file_path_thumb);
      }
     
      $image->delete();
      return redirect()->back()->with('success','Image deleted succesfully');
     

    }
}
