<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\Analytics\AnalyticsService;
use App\Models\Keyword;
use Exception;

class HomeController extends Controller
{
    protected $analytics;

    public function __construct(AnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    public function index(Request $request)
    {
        $searchResults = null;

        if ($request->has('keyword')) {
            try {
                $keywordValue = $request->input('keyword');

                // Same logic as your AnalyticsController
                Keyword::create(['keyword' => $keywordValue]);

                $filters = [
                    'position'   => $request->input('position', 'any'),
                    'alphabets'  => $request->boolean('alphabets', true),
                    'digits'     => $request->boolean('digits', true),
                    'hyphens'    => $request->boolean('hyphens', true),
                    'idns'       => $request->boolean('idns', true),
                    'active'     => $request->boolean('active', true),
                    'parked'     => $request->boolean('parked', true),
                    'inactive'   => $request->boolean('inactive', true),
                    'extensions' => $request->input('extensions', []),
                    'exclude'    => $request->input('exclude', ''),
                    'minLength'  => $request->input('minLength', ''),
                    'maxLength'  => $request->input('maxLength', ''),
                    'limit'      => $request->input('limit', 100),
                ];

                $searchResults = $this->analytics->getRelatedDomains($keywordValue, $filters);

            } catch (Exception $ex) {
                $searchResults = null;
            }
        }

        return Inertia::render('Home', [
            'searchResults' => $searchResults,
        ]);
    }
}
