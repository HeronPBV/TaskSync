<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateDataReportJob;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    public function index()
    {
        $report = Cache::get('latest_data_report');

        if (!$report) {
            return response()->json(['message' => 'No reports available.'], 404);
        }

        return response()->json(['report' => $report]);
    }

    public function generate()
    {
        dispatch(new GenerateDataReportJob());

        return response()->json(['message' => 'Report generation started.']);
    }
}
