<?php

namespace App\Http\Services;

use App\Http\Resources\DivCountResource;
use App\Models\DivCount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DivCountService extends Service
{
    /*
     * Fetch All Div Counts*/
    public function index()
    {
        $getDivCounts = DivCount::paginate();

        $divCounts = DivCountResource::collection($getDivCounts);

        return $divCounts;
    }

    /*
     * Store Div Counts */
    public function store($request)
    {
        $divCount = new DivCount;
        $divCount->url = $request->url;
        $divCount->count = $request->coiunt;
        $saved = $divCount->save();
        $message = "Div count saved";

        return [$saved, $message, $divCount];
    }

    /*
     * Show Dashboard Data
     */
    public function dashboard()
    {
        $uniqueUrls = DivCount::select("url")
            ->distinct()
            ->count();

        $year = date('Y');

        $groupedByMonth = DivCount::select(DB::raw('MONTH(created_at) as month, COUNT(*) as count'))
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->map(function ($item) {
                return [
                    "month" => Carbon::create()
                        ->month($item->month)
                        ->format("M"),
                    "count" => $item->count,
                ];
            });

        $groupedByDivsPerUrl = DivCount::select(DB::raw('url as url, SUM(count) as divs'))
            ->groupBy(DB::raw('url'))
            ->get()
            ->map(function ($item) {
                // Get the domain name
                $url = trim($item->url, "https://");
                $url = substr($url, 0, strrpos($url, '/'));

                return [
                    "url" => $url,
                    "divs" => $item->divs,
                ];
            });

        return [
            "uniqueUrls" => $uniqueUrls,
            "groupedByMonth" => $groupedByMonth,
            "groupedByDivsPerUrl" => $groupedByDivsPerUrl,
        ];
    }
}
