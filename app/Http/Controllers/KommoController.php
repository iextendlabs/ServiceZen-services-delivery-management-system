<?php

namespace App\Http\Controllers;

use App\Models\CRM;
use App\Models\CustomerProfile;
use App\Models\Quote;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class KommoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $data = $request->all();

        $contact = $data['contacts']['add'][0] ?? [];

        $input = [];
        $input['customer_name'] = $contact['name'] ?? 'N/A';
        $input['accountId'] = $contact['account_id'];
        $input['email'] = 'N/A';
        $input['phone'] = 'N/A';

        // Extract phone number
        if (!empty($contact['custom_fields'])) {
            foreach ($contact['custom_fields'] as $field) {
                if ($field['code'] === 'PHONE') {
                    $input['phone'] = $field['values'][0]['value'] ?? 'N/A';
                }
                if ($field['code'] === 'EMAIL') {
                    $input['email'] = $field['values'][0]['value'] ?? 'N/A';
                }
            }
        }

        CRM::create([
            'customer_name' => $input['customer_name'],
            'accountId' => $input['accountId'],
            'pipelineId' => $data['pipelines']['add'][0]['id'] ?? null,
            'email' => $input['email'],
            'phone' => $input['phone']
        ]);
        
        // Log the contact information
        Log::info('kommo Form Data:', [
            'contact' => $contact,
            'contactName' => $input['customer_name'],
            'accountId' => $input['accountId'],
            'phone' => $input['phone'],
            'email' => $input['email']
        ]);

        return back()->with('success', 'kommo saved successfully!');
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
    public function update(Request $request)
    {
        $accountId = $request->input('account.id');
        $pipelineId = $request->input('leads.update.0.pipeline_id');

        $crm = CRM::where('accountId', $accountId)->first();

        if (!$crm) {
            return back()->with('error', 'Account ID not found in CRM');
        }
        $this->quoteCreate($crm);

        $crm->pipelineId = $pipelineId;
        $crm->save();
        
        Log::info('kommo update Form Data:', [
            'accountId' => $accountId,
            'pipelineId' => $pipelineId
        ]);

        return back()->with('success', 'Pipeline ID updated successfully!');
    }

    public function quoteCreate($input)
    {

        list($customer_type, $user_id) = $this->findOrCreateUser($input);
        $input['user_id'] = $user_id;

        $service_id = 63;
        $zone = "Ajman";

        $service = Service::findOrFail($service_id);
        $categoryIds = $service->categories()->pluck('category_id')->toArray();

        $staffs = User::getEligibleQuoteStaff($service_id, $zone ?? null);

        $input['status'] = "Pending";

        $input['phone'] = $input['phone'] ?? null;
        $input['whatsapp'] = $input['phone'] ?? null;
        
        $quote = Quote::create($input);

        $quote->categories()->sync($categoryIds);
        if (count($staffs) > 0) {
            foreach ($staffs as $staff) {
                $staff->notifyOnMobile('Quote', 'A new quote has been generated with ID: ' . $quote->id);
                $quote->staffs()->syncWithoutDetaching([
                    $staff->id => [
                        'status' => 'Pending',
                        'quote_amount' => $staff->staff->quote_amount,
                        'quote_commission' => $staff->staff->quote_commission
                    ]
                ]);
            }
        }
        if (isset($customer_type) && $customer_type == "New") {
            $msg = sprintf(
                "Quote request submitted successfully! You can login with credentials Email: %s and Password: %s to check bids on your quotation.",
                $input['email'],
                $input['phone']
            );
        } else {
            $msg = "Quote request submitted successfully! ";
        }
        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
    }

    private function findOrCreateUser($input)
    {
        $user = User::where('email', $input['email'])->first();

        if (!isset($user)) {
            $user = User::create([
                'name' => $input['customer_name'],
                'email' => $input['email'],
                'password' => Hash::make($input['phone']),
            ]);

            $user->assignRole('Customer');
            $customer_type = "New";
            $input['user_id'] = $user->id;
        } else {
            $customer_type = "Old";
            $input['user_id'] = $user->id;
        }

        $input['number'] = $input['phone'];
        $input['whatsapp'] = $input['phone'];

        if ($customer_type == "New") {
            CustomerProfile::create($input);
        }

        return [$customer_type, $input['user_id']];
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
