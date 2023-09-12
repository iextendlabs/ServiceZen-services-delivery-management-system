<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\Setting;
use Illuminate\Http\Request;

class SiteReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $review_char_limit = Setting::where('key', 'Character Limit Of Review On Home Page')->value('value');

        $reviews = Review::latest()->paginate(config('app.paginate'));
        return view('site.reviews.index', compact('reviews', 'review_char_limit'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
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
        request()->validate([
            'user_name' => 'required',
            'content' => 'required',
            'rating' => 'required',
            'video' => 'mimetypes:video/*,video/*,application/octet-stream|max:50000',
        ]);

        $input = $request->all();

        if ($request->video) {
            $video = $request->video;
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('review-videos'), $videoName);
            $input['video'] = $videoName;
        }

        $review = Review::create($input);

        if ($request->images) {
            $images = $request->images;

            foreach ($images as $image) {
                $filename = mt_rand() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('review-images'), $filename);
                // dd($filename);
                ReviewImage::create([
                    'image' => $filename,
                    'review_id' => $review->id,
                ]);
            }
        }
        return redirect()->back()
            ->with('success', 'Review created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
