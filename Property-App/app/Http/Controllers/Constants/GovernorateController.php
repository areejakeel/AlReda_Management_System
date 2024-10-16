<?php

namespace App\Http\Controllers\Constants;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Traits\ReturnResponse;

class GovernorateController extends Controller {
    use ReturnResponse;

    public function index() {
        $governorates = Governorate::query()
            ->select('id')
            ->addSelect('name_' . request()->header('lang') . ' as name')
            ->get();
        return $this->returnData('governorates', $governorates);
    }
}
