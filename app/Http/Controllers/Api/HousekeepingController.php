<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PrintController;
use App\Models\Housekeeping;
use App\Models\Permitt;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HousekeepingController extends Controller
{
    public function storeHousekeeping(Request $request)
    {
        $requestData = $request->all();
        $permitId = $request->permit_id;
        $permit = Permitt::find($permitId);
        $permitDate = Carbon::parse($permit->date);

        // Ambil tanggal sekarang
        $now = Carbon::now();

        // Hitung selisih hari
        $daysDifference = $permitDate->diffInDays($now);
        $day = $daysDifference + 1;

        try {
            foreach ($requestData['housekeeping'] as $housekeeping) {
                Housekeeping::create([
                    'permitt_id' => $permitId,
                    'pertanyaan' => $housekeeping['pertanyaan'],
                    'value' => $housekeeping['value'],
                    'day' => $day,
                    'is_print' => true
                ]);
            }

            PrintController::print($permitId, $day);

            return ApiFormatter::createApi(200, 'Success');
        } catch (\Throwable $th) {
            return ApiFormatter::createApi(400, $th);
        }
    }
}
