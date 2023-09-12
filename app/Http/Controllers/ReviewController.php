<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:review-list|review-create|review-edit|review-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:review-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:review-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:review-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = Review::latest()->paginate(config('app.paginate'));
        return view('reviews.index', compact('reviews'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::all();
        $user = User::find(Auth::user()->id);

        return view('reviews.create', compact('user', 'services'));
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
        return redirect()->route('reviews.index')
            ->with('success', 'REview created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $review = Review::find($id);

        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $review = Review::find($id);
        $services = Service::all();

        return view('reviews.edit', compact('review', 'services'));
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
        request()->validate([
            'user_name' => 'required',
            'content' => 'required',
            'rating' => 'required',
        ]);

        $review = Review::find($id);

        $input = $request->all();

        if ($request->video) {

            if ($review->video && file_exists(public_path('review-videos') . '/' . $review->video)) {
                unlink(public_path('review-videos') . '/' . $review->video);
            }

            $video = $request->video;
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('review-videos'), $videoName);
            $input['video'] = $videoName;
        }

        $review->update($input);

        if ($request->images) {
            $images = $request->images;

            foreach ($images as $image) {
                $filename = mt_rand() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('review-images'), $filename);
                ReviewImage::create([
                    'image' => $filename,
                    'review_id' => $id,
                ]);
            }
        }

        return redirect()->route('reviews.index')
            ->with('success', 'Review Update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $review = Review::find($id);
        if (isset($review->images)) {
            foreach ($review->images as $image) {
                if ($image->image && file_exists(public_path('review-images') . '/' . $image->image)) {
                    unlink(public_path('review-images') . '/' . $image->image);
                }
            }
        }

        if ($review->video && file_exists(public_path('review-videos') . '/' . $review->video)) {
            unlink(public_path('review-videos') . '/' . $review->video);
        }

        $review->delete();

        return redirect()->route('reviews.index')
            ->with('success', 'Review deleted successfully');
    }

    public function removeImages(Request $request)
    {
        $review_images = ReviewImage::where('review_id', $request->id)->pluck('image')->toArray();

        $deleteImage = $request->image;

        if (($key = array_search($deleteImage, $review_images)) !== false) {
            if ($deleteImage && file_exists(public_path('review-images') . '/' . $deleteImage)) {
                unlink(public_path('review-images') . '/' . $deleteImage);
            }
        }

        ReviewImage::where('image', $request->image)->delete();

        return redirect()->back()->with('success', 'Image Remove successfully.');
    }

    public function removeVideo(Request $request)
    {
        $review = Review::find($request->id);

        if ($review->video && file_exists(public_path('review-videos') . '/' . $review->video)) {
            unlink(public_path('review-videos') . '/' . $review->video);
        }

        $review->video = '';

        $review->save();

        return redirect()->back()->with('success', 'Image Remove successfully.');
    }
}
