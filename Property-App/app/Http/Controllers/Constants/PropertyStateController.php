<?php

namespace App\Http\Controllers\Constants;

use App\Http\Controllers\Controller;
use App\Models\PropertyState;
use App\Traits\ReturnResponse;

class PropertyStateController extends Controller {
    use ReturnResponse;

    public function index(\Request $request) {
        $propertyStates = PropertyState::query()
            ->select('id')
            ->addSelect('name_' . request()->header('lang') . ' as name')
            ->get();
        return $this->returnData('propertyStates', $propertyStates);
    }
}
