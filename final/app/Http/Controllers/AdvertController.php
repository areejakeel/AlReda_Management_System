<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AdvertController extends Controller
{
    public function show_advert_detail($id)
    {
    $advert=Advert::find($id);

    if(!$advert){

        return response()->json(['message'=>'advert not found'],404);

    }

    return response()->json([
        'data'=>$advert
    ]);

    }
    public function show_advert_detail2($id)
    {
    $advert=Advert::find($id);

    if(!$advert){

        return response()->json(['message'=>'advert not found'],404);

    }

    return response()->json([
        'data'=>$advert
    ]);

    }

    public function store_advert(Request $request)
    {
      $input = $request->all();
      $validator = Validator::make($input, [
          'advert_image' => 'required|image',
          'description' => 'required'
      ]);
  
      if ($validator->fails()) {
          return response()->json($validator->errors()->tojson(), 400);
      }
  
      $advert = new Advert();
  
      if ($request->hasFile('advert_image')) {
          $image = $request->file('advert_image');
          if ($image->isValid()) {
              $filename = time() . '.' . $image->getClientOriginalExtension();
              $image->move(public_path('image'), $filename);
              $advert_image = url('/image/' . $filename);
          }
      } else {
         
          $advert_image = null;
     
      }
      $advert->advert_image = $advert_image;
      $advert->description=$request->input('description');
      $advert->status='waiting';
  
      $advert->save();
  
      return response()->json([
          'data' => $advert,
          'message' => 'Advert added successfully, waiting admin approval'
      ]);
  }
public function showWaitingAdverts(Advert $advert)
{  $advert=Advert::where('status','waiting')->get();
    return response()->json(['Advert'=> $advert]);
}

public function showWaitingAdverts2(Advert $advert)
{  $advert=Advert::where('status','waiting')->get();
    return response()->json(['Advert'=> $advert]);
}
public function showAd(Advert $advert)
    {  $advert=Advert::where('status','approved')->get();
        return response()->json(['Advert'=> $advert]);
    }
public function update_advert_status(Request $request, $id)
{
    $input = $request->all();
    $validator = Validator::make($input, [
        'status' => 'required|in:pending,approved,rejected'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $advert = Advert::find($id);

    if (!$advert) {
        return response()->json(['message' => 'Advert not found'], 404);
    } 
    $advert->previous_status = $advert->status;
    $advert->status = $input['status'];
    $advert->save();

    return response()->json([
        'data' => $advert,
        'message' => 'Advert status updated successfully'
    ]);
}
    public function destroy(Advert $advert)
    {
        //
    }
    
    ////////////////////////////////////////////////


    public function getApprovedAdverts(){
        $approved_adverts = Advert::where('status', 'approved')->get()->makeHidden('status');
        return response()->json([
            'data' => $approved_adverts,
        ]);
    }
    
}
