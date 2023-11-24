<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Url;
use App\Models\Analytics;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class AnalyticsController extends Controller
{
    public function showAnalytics($urlId)
    {
        $url = Url::with('analytics')->find($urlId);
        if (!$url) {
            // Handle the case where the URL is not found, for example, redirect or show an error message.
            return redirect()->route('urls.index')->with('error', 'URL not found');
        }
        return view('Analytics', ['url' => $url]);
    }

}