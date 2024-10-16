<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Traits\ReturnResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    use ReturnResponse;

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
            'property_id'=>'required|exists:properties,id',
            'description' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->returnError(422, $validator->errors());
        }
        $report = new Report();
        $user_id = Auth::id();
        $report->fill([
            'description' => $input['description'],
            'user_id' => $user_id,
            'property_id' => $input['property_id']
        ]);
        $report->save();
        return $this->returnSuccessMessage('report added successfully');
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
