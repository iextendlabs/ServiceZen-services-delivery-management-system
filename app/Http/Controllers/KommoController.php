<?php

namespace App\Http\Controllers;

use App\Models\CRM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $crm->pipelineId = $pipelineId;
        $crm->save();
        
        Log::info('kommo update Form Data:', [
            'accountId' => $accountId,
            'pipelineId' => $pipelineId
        ]);

        return back()->with('success', 'Pipeline ID updated successfully!');
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
