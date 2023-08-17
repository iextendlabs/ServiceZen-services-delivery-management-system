<?php
    
namespace App\Http\Controllers;

use App\Models\CashCollection;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class CashCollectionController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:cash-collection-list|cash-collection-edit|cash-collection-delete', ['only' => ['index','show']]);
         $this->middleware('permission:cash-collection-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:cash-collection-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter_status = $request->status;

        $query = CashCollection::latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->order_id) {
            $query->where('order_id', $request->order_id);
        }

        $cash_collections = $query->paginate(config('app.paginate'));
        
        if ($request->csv == 1) {
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=CashCollection.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            $output = fopen("php://output", "w");

            fputcsv($output, array('Order ID', 'Staff', 'Collected Amount', 'Customer', 'Order Total	', 'Description', 'Comment', 'Status', 'Date Added'));

            foreach ($cash_collections as $row) {

                    fputcsv($output, array($row->id, $row->staff_name, $row->amount, $row->order->customer->name, $row->order->total_amount, $row->description, $row->order->comment, $row->status, $row->created_at));
            }

            // Close output stream
            fclose($output);

            // Return CSV file as download
            return Response::make('', 200, $headers);
        } else if ($request->print == 1) {
            return view('cashCollections.print', compact('cash_collections'));
        } else {
            $filters = $request->only(['status','order_id']);
            $cash_collections->appends($filters);
            return view('cashCollections.index',compact('cash_collections','filter_status'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));
        }


        
    }

    public function staffCashCollection()
    {
        // $orders = Order::where(['service_staff_id'=>Auth::id(),'status'=>'Complete'])->orderBy('id','DESC')->paginate(config('app.paginate'));   
        
        $orders = Order::leftJoin('cash_collections', 'orders.id', '=', 'cash_collections.order_id')
                ->where(function ($query) {
                    // Filter orders with cash collection status not approved
                    $query->where('cash_collections.status', '!=', 'approved')
                        // Filter orders without any associated cash collection
                        ->orWhereNull('cash_collections.status');
                })
                ->select('orders.*') // Select only the columns from the "orders" table
                ->where('service_staff_id', Auth::id())->where('orders.status', 'Complete')->orderBy('id','DESC')->paginate(config('app.paginate'));
                
        return view('cashCollections.staffCashCollection',compact('orders'))
            ->with('i', (request()->input('page', 1) - 1) * config('app.paginate'));      
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Order $order)
    {
        return view('cashCollections.create',compact('order'));   
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'description' => 'required',
            'staff_id' => 'required',
            'order_id' => 'required',
        ]);

        $input = $request->all();
        $input['status'] = "Not Approved";

        CashCollection::create($input);
        
        return redirect()->route('staffCashCollection')
                        ->with('success','Cash Collection created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\CashCollection  $cash_collection
     * @return \Illuminate\Http\Response
     */
    public function show(CashCollection $cash_collection)
    {
        return view('cashCollections.show',compact('cash_collection'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CashCollection  $cash_collection
     * @return \Illuminate\Http\Response
     */

    public function cashCollectionUpdate(Request $request, $id)
    {
        request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $cash_collections = CashCollection::find($id);
        $input = $request->all();

        if (isset($request->image)) {
            if ($cash_collections->image && file_exists(public_path('cash-collections-images').'/'.$cash_collections->image)) {
                unlink(public_path('cash-collections-images').'/'.$cash_collections->image);
            }
        
            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            $request->image->move(public_path('cash-collections-images'), $filename);
        
            $input['image'] = $filename;
        }
        
        $cash_collections->update($input);

        return redirect()->route('cashCollection.index')
                        ->with('success','Cash Collection updated successfully');
    }

    public function updateImage(Request $request, $id){

        request()->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $cash_collections = CashCollection::find($id);
        $input = $request->all();

        if (isset($request->image)) {
            if ($cash_collections->image && file_exists(public_path('cash-collections-images').'/'.$cash_collections->image)) {
                unlink(public_path('cash-collections-images').'/'.$cash_collections->image);
            }
        
            $filename = time() . '.' . $request->image->getClientOriginalExtension();

            $request->image->move(public_path('cash-collections-images'), $filename);
        
            $input['image'] = $filename;
        }
        
        $cash_collections->update($input);

        return redirect()->route('orders.index')
                        ->with('success','Cash Collection image uploaded successfully');
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CashCollection  $cash_collection
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cash_collection = CashCollection::find($id);
        $cash_collection->delete();
    
        return redirect()->route('cashCollection.index')
                        ->with('success','Cash Collection deleted successfully');
    }

    public function uploadImageForm($order_id){
        
        $cash_collection = CashCollection::where('order_id',$order_id)->first();
        return view('cashCollections.uploadImage',compact('cash_collection'));
    
    }
}