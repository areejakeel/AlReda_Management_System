<?php

namespace App\Http\Controllers\Constants;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Traits\ReturnResponse;

class RegionController extends Controller {
    use ReturnResponse;

    public function index($governorateId) {
        $governorate = Governorate::find($governorateId);

        if (!$governorate) {
            return $this->returnError(404, 'Invalid display type ID');
        }

        $regions = $governorate->regions()
            ->select(['id','x', 'y'])
            ->addSelect('name_' . request()->header('lang') . ' as name')
            ->get();

        return $this->returnData('regions', $regions);
    }
}
