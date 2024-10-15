<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Http\Request;
use App\Models\Specialization;
class SpecializationController extends Controller
{
    public function SpecializationStore(Request $request)
    { 
       $image_temp=null;
       $input = $request->all();
       $validator = Validator::make($input, [
           'name' => 'required|string'
       ]);
       if ($validator->fails()) {
           return response()->json($validator->errors()->tojson(),400);
       }
       $specialization = new Specialization();
       $specialization->fill([
           'name' =>$input['name'],
      
       ]);
       $specialization->save();
       return response()->json(['data' => $specialization, 'message' => 'specialization added sucessfully']);}}


