<?php

namespace App\Http\Controllers;

use App\Models\Review;
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
        ]);

        $input = $request->all();
        if ($request->image) {
            // create a unique filename for the image
            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            // move the uploaded file to the public/staff-images directory
            $request->image->move(public_path('review-images'), $filename);

            // save the filename to the gallery object and persist it to the database

            $input['image'] = $filename;
        }
        Review::create($input);
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
        if (isset($request->image)) {
            //delete previous Image if new Image submitted
            if ($review->image && file_exists(public_path('review-images') . '/' . $review->image)) {
                unlink(public_path('review-images') . '/' . $review->image);
            }

            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            // move the uploaded file to the public/review-images directory
            $request->image->move(public_path('review-images'), $filename);

            // save the filename to the gallery object and persist it to the database

            $input['image'] = $filename;
        }

        $review->update($input);

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
        $review->delete();

        return redirect()->route('reviews.index')
            ->with('success', 'Review deleted successfully');
    }
}
