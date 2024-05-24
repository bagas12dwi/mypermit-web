<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function getHistory(Request $request)
    {
        $permit_id = $request->input('permit_id');

        $data = History::where('permitt_id', $permit_id)
            ->orderBy('id', 'DESC')
            ->get();

        if ($data) {
            return ApiFormatter::createApi(200, 'Success', $data);
        } else {
            return ApiFormatter::createApi(400, 'Failed');
        }
    }
}
