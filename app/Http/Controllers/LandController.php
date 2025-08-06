<?php

namespace App\Http\Controllers;

use App\Models\VideoLink;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = VideoLink::where('status', 'active')
            ->orderBy('views_count', 'desc')
            ->paginate(12);

        $data = [
            'pageTitle' => 'Premium Video Views Service',
            'videos' => $videos,
            'totalVideos' => VideoLink::where('status', 'active')->count(),
            'totalViews' => VideoLink::sum('views_count'),
            'totalEarningsPaid' => VideoLink::sum('cost_per_click')
        ];
        return view('frontend.public-gallery', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
