<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Traits\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ReturnResponse;


class PropertyController extends Controller
{
    use ReturnResponse, Helper;

    public function index()
    {

    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'price' => 'required|regex:/^\d*(\.\d{2})?$/',
            'space' => 'required|regex:/^\d*(\.\d{2})?$/',
            'region_id' => 'required|exists:regions,id',
            'images' => 'required|array',
            'images.*' => 'image'
        ]);
        if ($validator->fails()) {
            return $this->returnError(422, $validator->errors());
        }
        $property = new Property();
        $user_id = Auth::id();
        $property->fill(
            [
                'price' => $input['price'],
                'space' => $input['space'],
                'user_id' => $user_id,
                'region_id' => $input['region_id'],
                'property_state_id' => 5 //processing
            ]
        );
        $property->save();
        $this->saveImages($input['images'],$property->id,"Property $property->id");
        return $this->returnData('property', $property, 'property added successfully');

    }

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
