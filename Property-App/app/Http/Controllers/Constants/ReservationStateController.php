<?php

namespace App\Http\Controllers\Constants;

use App\Http\Controllers\Controller;
use App\Models\ReservationState;
use App\Traits\ReturnResponse;

class ReservationStateController extends Controller {
    use ReturnResponse;

    public function index() {
        $reservationStates = ReservationState::query()
            ->select('id')
            ->addSelect('name_' . request()->header('lang') . ' as name')
            ->get();
        return $this->returnData('reservationStates', $reservationStates);
    }
}
