<?php

namespace App\Http\Controllers\Constants;

use App\Http\Controllers\Controller;
use App\Models\ReservationType;
use App\Traits\ReturnResponse;

class ReservationTypeController extends Controller {
    use ReturnResponse;

    public function index() {
        $reservationTypes = ReservationType::query()
            ->select('id')
            ->addSelect('name_' . request()->header('lang') . ' as name')
            ->get();
        return $this->returnData('reservationTypes', $reservationTypes);
    }
}
