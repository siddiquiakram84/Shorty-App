<?php

namespace App\Http\Controllers;

use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Validator;
use App\Models\Analytics;
use Illuminate\Support\Facades\Hash;
use App\Models\Url;
use App\Models\Click;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UrlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('urls.index', [
            'urls' => Url::with('user')->where('user_id', auth()->id())->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'original_url' => 'required|url|valid_url',
        ];

        // Custom error messages
        $messages = [
            'original_url.url' => 'The :attribute must be a valid URL.',
            'original_url.valid_url' => 'The :attribute must have a valid scheme and domain extension.',
        ];

        // Register the custom rule
        Validator::extend('valid_url', function ($attribute, $value, $parameters, $validator) {
            $parsedUrl = parse_url($value);

            // Check if the URL has a scheme and a valid domain extension
            return isset($parsedUrl['scheme']) && isset($parsedUrl['host']) && preg_match('/\.\w+$/', $parsedUrl['host']);
        });

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'store')
                ->withInput()
                ->with('error', 'Please fix the errors in the form.');
        }
    
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $data['title'] = Str::ucfirst($request->title);
        $value = $request->original_url;
        $data['original_url'] = $value;

        // Generating the short Url encrypted strings. Hashed this with the 'user_id'
        $maxAttempts = 5;
        $attempts = 0;

        // Assuming you have a logged-in user
        $user = auth()->user();

        do {
            // Generate a random string of length 8
            $randomString = Str::random(9);
            // Combine user id and random string
            $combinedString = $user->id . $randomString;
            $hashedRandomString = Hash::make($combinedString);
            $specialCharacter = '!%&';
            $hashedRandomString = $specialCharacter . substr($hashedRandomString, 0, 3);
            $exists = Url::where('shortener_url', $hashedRandomString)->exists();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);

        if ($attempts === $maxAttempts) {
            return redirect()->back()->with('error', 'Unable to generate a unique short URL after '.$maxAttempts.' attempts. Please try again.');
        }

        $data['shortener_url'] = $hashedRandomString;
        Url::create($data);
        return redirect(route('urls.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Url  $url
     * @return \Illuminate\Http\Response
     */
    public function show(Url $url)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Url  $url
     * @return \Illuminate\Http\Response
     */
    public function edit(Url $url)
    {
        return view('urls.edit', [
            'url' => $url,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Url  $url
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Url $url)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'original_url' => 'required|string|max:255',
        ]);
        $validated['shortener_url'] = Str::random(5);

        $url->update($validated);
        return redirect(route('urls.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Url  $url
     * @return \Illuminate\Http\Response
     */
    public function destroy(Url $url)
    {
        $url->delete();
        return redirect(route('urls.index'));
    }

    public function shortenLink($shortener_url)
    {
        $url = Url::where('shortener_url', $shortener_url)->first();

        // Record analytics data
        $agent = new Agent();

        $analyticsData = [
            'url_id' => $url->id,
            'user_agent' => request()->header('User-Agent'),
            'ip_address' => request()->ip(),
            'os' => $agent->platform(),
            'os' => $agent->platform(),
            'device' => $agent->device(),
            'browser' => $agent->browser(),
            'user_location' => $this->getUserLocation(request()->ip()),
            'accessed_at' => now(),
        ];

        Analytics::create($analyticsData);

        // Redirect to the original URL
        return redirect($url->original_url);
    }

    public function showAnalytics($urlId)
    {
        $url = Url::with('analytics')->find($urlId);

        return view('Analytics', ['url' => $url]);
    }

    private function getUserLocation($ipAddress)
{
    $ipAddress = '8.8.8.8';
    try {
        $location = Location::get($ipAddress);
        // dd($location);

        $locationString = '';

        if ($location) {
            $locationString = $location->countryName . ', ' . $location->regionName . ', ' . $location->cityName;
        }

        return $locationString;
    } catch (\Exception $e) {
        // Log or dd the exception message for debugging
        dd($e->getMessage());
    }
}
    

}