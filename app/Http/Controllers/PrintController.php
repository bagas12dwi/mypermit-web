<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Housekeeping;
use App\Models\Permitt;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrintController extends Controller
{
    public static function print(int $permitId, int $day)
    {
        $permit = Permitt::with('user', 'workPreparation', 'hazard', 'control')->find($permitId);
        $housekeeping = Housekeeping::where('permitt_id', $permitId)->where('day', $day)->get();
        $permitDate = Carbon::parse($permit->date);

        // Ambil tanggal sekarang
        $now = Carbon::now();

        // Hitung selisih hari
        $daysDifference = $permitDate->diffInDays($now);
        $pdf = Pdf::loadView('print', [
            'title' => 'Print-' . $permit->permitt_number,
            'permit' => $permit,
            'housekeeping' => $housekeeping,
            'dayDifference' => $daysDifference + 1,
            'now' => $now
        ]);
        $random = Str::random(40);
        $path = 'pdf/' . $random;

        // return $pdf->stream('test.pdf');

        Storage::put($path . '.pdf', $pdf->output());
        return Document::create([
            'user_id' => $permit->user_id,
            'permitt_id' => $permit->id,
            'day' => $day,
            'document_path' => $path . '.pdf'
        ]);
    }
}
