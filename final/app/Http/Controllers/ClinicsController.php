<?php

namespace App\Http\Controllers;

use App\Models\Clinics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ClinicResource;


class ClinicsController extends Controller
{ 
      
 public function storeClinic(Request $request)
     { 
        $input = $request->all();
        $validator = Validator::make($input, [
            'clinic_name' => 'required|string',
            'clinic_img' => 'required',
            'description' =>'required'
      
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->tojson(),400);
        }
    
        
        $clinics = new Clinics();
   
        $clinics->clinic_name= $request->input('clinic_name');
        $clinics->clinic_img= $request->input('clinic_img');
            if ($request->hasFile('clinic_img')) {
                $image = $request->file('clinic_img');
                if ($image->isValid()) {
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('image'), $filename);
                    $doctor_img = url('/image/' . $filename);
                }
             else {
               
                $clinic_img = null;
           
            }
            $clinics->description= $request->input('description');
            
       
        }
        $clinics->clinic_img = $doctor_img;
            
        $clinics->save();
        return response()->json(['data' => $clinics, 'message' => 'clinincs added sucessfully']);}
   

   
        public function updateClinic(Request $request, int $id)
        {
            $validator = Validator::make($request->all(), [
                'clinic_name' => 'required|string',
                'description' => 'required|string',
                'clinic_img' => 'required|image' // Added image validation
            ]);
        
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
        
            $clinic = Clinics::find($id);
            if (!$clinic) {
                return response()->json('No such clinic found', 404);
            }
        
            $clinic_img = $clinic->clinic_img; // Default to current image
        
            if ($request->hasFile('clinic_img')) {
                $image = $request->file('clinic_img');
                if ($image->isValid()) {
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('image'), $filename);
                    $clinic_img = url('/image/' . $filename);
                }
            }
        
            $clinic->update([
                'clinic_name' => $request->clinic_name,
                'clinic_img' => $clinic_img,
                'description' => $request->description,
    
            ]);
        
            return response()->json([
                "message" => "This proclinicfile updated successfully",
                "data" => $clinic
            ], 200);
        }
        public function destroyClinic($id)
        {
            $clinic = Clinics::find($id);
            
            if (!$clinic) {
              return response()->json('No such Profile found', 404);
            }
            
            $clinic->delete();
            
            return response()->json([
                'message' => 'Clinic deleted successfully',
            ]);
        }
    
    public function showAllClinics()
    {
        $clinics = Clinics::all();
    
        if ($clinics->isEmpty()) {
            return response()->json([
                'message' => 'No clinics found',
            ]);
        }
    
        return response()->json([
            'data' => $clinics,
            'message' => 'Successfully retrieved cLinics',
        ]);
    }
    public function search_clinic($clinic_name)
    {
      $clinics= Clinics ::where('clinic_name','LIKE','%'.$clinic_name.'%')->get();

        if ($clinics->isEmpty()) {
            return response()->json( 'This clinic not found', 404);
        }
        return response()->json([ClinicResource::collection($clinics), 'This clinic  that you need'], 200);
    
    }
        public function clinicall()
{
    $clinics = Clinics::all();
    return response()->json(['data' => $clinics, 'message' => 'Clinics retrieved successfully']);
}
}
