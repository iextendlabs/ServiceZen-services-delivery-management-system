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
        $account_id = $contact['linked_leads_id'] ?? [];
    
        $leadId = array_key_first($account_id);
        $input['accountId'] = $leadId;
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
        $accountId = $request->input('leads.update.0.id');
        $pipelineId = $request->input('leads.update.0.pipeline_id');

        $crm = CRM::where('accountId', $accountId)->first();

        if (!$crm) {
            return back()->with('error', 'Account ID not found in CRM.');
        }

        if (!$pipelineId) {
            return back()->with('error', 'Invalid pipeline ID.');
        }

        Log::info('CRM Updated:', [
            'accountId' => $accountId,
            'pipelineId' => $pipelineId
        ]);

        $crm->pipelineId = $pipelineId;

        if ($crm->service_id == null) {
            return $this->quoteCreate($crm);
        }

        $crm->save();

        return back()->with('success', 'Pipeline ID updated successfully!');
    }

    public function incomingLead(Request $request)
    {
        $data = $request->all();

        $input['customer_name'] = $data['unsorted']['add'][0]['source_data']['client']['name'] ?? 'N/A';
        $input['phone'] = $data['unsorted']['add'][0]['source_data']['client']['id'] ?? 'N/A';
        $input['email'] = $input['phone'] ? $input['phone']."@tadhem.com" : 'N/A';
        $input['pipelines'] = $data['unsorted']['add'][0]['pipeline_id'] ?? null;
        $input['accountId'] = $data['unsorted']['add'][0]['lead_id'] ?? 'N/A';

        $crm = CRM::where('accountId', $input['accountId'])->first();
        if ($crm) {
            return back()->with('error', 'Account ID already exists in CRM.');
        }
        
        $crm = CRM::create([
            'customer_name' => $input['customer_name'],
            'accountId' => $input['accountId'],
            'pipelineId' => $input['pipelines'],
            'email' => $input['email'],
            'phone' => $input['phone']
        ]);

        // Log the contact information
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        Log::channel('kommo_log')->info('Request Received:', ['data' => $jsonData]);

        return $this->quoteCreate($crm);
    }

    public function quoteCreate($crm)
    {
        $data = [];
        [$customer_type, $user_id] = $this->findOrCreateUser($crm);
        $data['user_id'] = $user_id;

        $service = Service::where('pipelineId', $crm->pipelineId)->first();
        if (!$service) {
            return back()->with('error', 'Service not found.');
        }

        $data['service_id'] = $service->id;
        $categoryIds = $service->categories()->pluck('category_id')->toArray();

        $staffs = User::getEligibleQuoteStaff($data['service_id'], null, true);

        $data['status'] = "Pending";
        $data['phone'] = $crm->phone ?? null;
        $data['whatsapp'] = $crm->phone ?? null;
        $data['service_name'] = $service->name;
        $data['detail'] = "I am interested in " . $service->name . ". Send me a quote.";
        $data['sourcing_quantity'] = 1;
        $data['source'] = "CRM";

        $quote = Quote::create($data);
        $quote->categories()->sync($categoryIds);

        if ($staffs->isNotEmpty()) {
            foreach ($staffs as $staff) {
                $staff->notifyOnMobile('Quote', 'A new quote has been generated with ID: ' . $quote->id, null, 'Staff App');
                $quote->staffs()->syncWithoutDetaching([
                    $staff->id => [
                        'status' => 'Pending',
                        'quote_amount' => optional($staff->staff)->quote_amount ?? 0,
                        'quote_commission' => optional($staff->staff)->quote_commission ?? 0,
                    ]
                ]);
            }
        }

        $crm->service_id = $service->id;
        $crm->save();

        $msg = "Quote request submitted successfully!";
        if ($customer_type == "New") {
            $msg .= sprintf(
                " You can login with credentials Email: %s and Password: %s to check bids on your quotation.",
                $crm->email,
                $crm->phone
            );
        }

        return back()->with('success', $msg);
    }

    private function findOrCreateUser($input)
    {
        $user = User::where('email', $input['email'])->first();
        $customer_type = "Old";

        if (!$user) {
            $user = User::create([
                'name' => $input['customer_name'],
                'email' => $input['email'],
                'password' => Hash::make($input['phone']),
            ]);

            $user->assignRole('Customer');
            $customer_type = "New";
        }

        CustomerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'number' => $input['phone'],
                'whatsapp' => $input['phone']
            ]
        );

        return [$customer_type, $user->id];
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
