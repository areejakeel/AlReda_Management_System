<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\Center;
use App\Models\Record;
use Illuminate\Http\Request;

class CenterController extends Controller
{
    public function ShowProfileCentre()
    {
    $centre = Center::all();

    if ($centre->isEmpty()) {
        return response()->json([
            'message' => 'No center found',
        ]);
    }

    return response()->json([
        'status'=>true,
        'data' =>  $centre,
        'message' => 'this is Profile of reda center ',
    ]);
    }
     public function storeProfile(Request $request)
     {
        $image_temp=null;
         $input = $request->all();
         $validator = Validator::make($input, [
             'center_name' => 'required|string',
             'description' => 'required|string',
             'phone'=>'required|max:10',
             'center_img'=>'required',
         ]);
         if ($validator->fails()) {
            return response()->json($validator->errors()->tojson(),400);
           }
       
         $center = new Center();
         if ($request->hasFile('center_img')) {
            $image = $request->file('center_img');
            if ($image->isValid()) {
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('image'), $filename);
                $center_img = url('/image/' . $filename);
            }
        } else {
           
            $center_img = null;
       
        }
       // $doctor->clinics_id= $request->input('clinics_id');
       $center->center_img = $center_img;
       $center->center_name=$request->input('center_name');
        $center->description=$request->input('description');
        $center->phone=$request->input('phone');   
         $center->save();
         return response()->json([
            'data' => $center,
            'message' => 'center added successfully'
        ]);
     }
   
     public function updateProfile(Request $request, int $id)
     {
         $validator = Validator::make($request->all(), [
             'center_name' => 'required|string',
             'description' => 'required|string',
             'phone' => 'required|max:10',
             'center_img' => 'required|image' // Added image validation
         ]);
     
         if ($validator->fails()) {
             return response()->json($validator->errors()->toJson(), 400);
         }
     
         $center = Center::find($id);
         if (!$center) {
             return response()->json('No such Profile found', 404);
         }
     
         $center_img = $center->center_img; // Default to current image
     
         if ($request->hasFile('center_img')) {
             $image = $request->file('center_img');
             if ($image->isValid()) {
                 $filename = time() . '.' . $image->getClientOriginalExtension();
                 $image->move(public_path('image'), $filename);
                 $center_img = url('/image/' . $filename);
             }
         }
     
         $center->update([
             'center_name' => $request->center_name,
             'description' => $request->description,
             'phone' => $request->phone,
             'center_img' => $center_img
         ]);
     
         return response()->json([
             "message" => "This profile updated successfully",
             "data" => $center
         ], 200);
     }

     public function destroyProfile($id)
     {
         $center = Center::find($id);
         
         if (!$center) {
             return response()->json('No such Profile found', 404);
         }
         
         $center->delete();
         
         return response()->json([
             'message' => 'Center profile deleted successfully',
         ]);
     }
     
     public function   allRecords(){


        $records = Record::all();
        return response()->json(['data' => $records, 'message' => 'Records retrieved successfully']);
    }
}
