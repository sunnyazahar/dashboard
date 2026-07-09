<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Country;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $agents = Agent::with('country')->orderBy('agent_name')->get();

        $countries = $agents
            ->map(fn ($agent) => $agent->country?->name)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $agentTypes = $agents
            ->pluck('agent_type')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('Agents.index', compact('agents', 'countries', 'agentTypes'));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('Agents.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_name' => 'required|string|max:255',
            'company_id' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'code_description' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
            'remarks' => 'nullable|string',
            'special_considerations' => 'nullable|string',
            'show_pre_alert' => 'nullable|boolean',
            'agent_address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'district_state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'port_code' => 'nullable|string|max:255',
            'office_address' => 'nullable|string',
            'office_city' => 'nullable|string|max:255',
            'office_district_state' => 'nullable|string|max:255',
            'office_zip_code' => 'nullable|string|max:255',
            'office_country_id' => 'nullable|exists:countries,id',
            'eori_number' => 'nullable|string|max:255',
            'un_locode' => 'nullable|string|max:255',
            'agent_type' => 'nullable|string|max:255',
        ]);

        $validated['show_pre_alert'] = $request->has('show_pre_alert');

        Agent::create($validated);

        return redirect()->route('agents.index')->with('success', 'Agent created successfully.');
    }

    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('Agents.edit', compact('agent', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);

        $validated = $request->validate([
            'agent_name'          => 'required|string|max:255',
            'company_id'          => 'nullable|string|max:255',
            'code'                => 'nullable|string|max:255',
            'code_description'    => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:255',
            'email'               => ['nullable', 'string', 'max:255', $this->multipleEmailsValidator()],
            'remarks'             => 'nullable|string',
            'special_considerations' => 'nullable|string',
            'show_pre_alert'      => 'nullable|boolean',
            'agent_address'       => 'nullable|string',
            'city'                => 'nullable|string|max:255',
            'district_state'      => 'nullable|string|max:255',
            'zip_code'            => 'nullable|string|max:255',
            'country_id'          => 'nullable|exists:countries,id',
            'port_code'           => 'nullable|string|max:255',
            'office_address'      => 'nullable|string',
            'office_city'         => 'nullable|string|max:255',
            'office_district_state' => 'nullable|string|max:255',
            'office_zip_code'     => 'nullable|string|max:255',
            'office_country_id'   => 'nullable|exists:countries,id',
            'eori_number'         => 'nullable|string|max:255',
            'un_locode'           => 'nullable|string|max:255',
            'agent_type'          => 'nullable|string|max:255',
            
            // Billing
            'invoicing_name'      => 'nullable|string|max:255',
            'billing_address'     => 'nullable|string',
            'billing_city'        => 'nullable|string|max:255',
            'billing_district_state'=> 'nullable|string|max:255',
            'billing_zip_code'    => 'nullable|string|max:255',
            'billing_country_id'  => 'nullable|exists:countries,id',
            'invoicing_emails'    => 'nullable|string|max:255',
            'invoicing_emails_cc' => 'nullable|string|max:255',
            'vat_number'          => 'nullable|string|max:255',
            'invoicing_frequency' => 'nullable|string|max:255',
            'rebate_percentage'   => 'nullable|numeric',
            'outgoing_currency'   => 'nullable|string|max:255',
            'outgoing_payment_terms'=> 'nullable|string|max:255',
            'incoming_currency'   => 'nullable|string|max:255',
            'incoming_payment_terms'=> 'nullable|string|max:255',

            // SOP
            'coc_signed_date'     => 'nullable|date',
            'responsible_manager' => 'nullable|string|max:255',

            // Pricing
            'purchase_rate'       => 'nullable|string|max:255',
            'sell_rate'           => 'nullable|string|max:255',
            'profit'              => 'nullable|string|max:255',

            // Email
            'export_email_services'=> 'nullable|string',
            'import_email_services'=> 'nullable|string',
            'status_changed_emails'=> 'nullable|string|max:255',
            'stock_item_changed_emails'=> 'nullable|string|max:255',
            'quote_requests_emails'=> 'nullable|string|max:255',

            // Scan gun
            'scangun_login'       => 'nullable|string|max:255',
            'scangun_password'    => 'nullable|string|max:255',
            
            // Exceptions array
            'billing_exceptions'  => 'nullable|array',
        ]);

        $validated['show_pre_alert'] = $request->has('show_pre_alert');
        $validated['applies_to_rebate'] = $request->has('applies_to_rebate');
        $validated['coc_signed'] = $request->has('coc_signed');
        $validated['sop_implemented'] = $request->has('sop_implemented');
        $validated['calculate_sell_rates'] = $request->has('calculate_sell_rates');
        $validated['scangun_enable_picture'] = $request->has('scangun_enable_picture');
        $validated['scangun_enable_detailed_shipment'] = $request->has('scangun_enable_detailed_shipment');

        $agent->update($validated);

        // Handle Billing Exceptions
        $agent->billingExceptions()->delete(); // Clear old exceptions
        if ($request->has('billing_exceptions') && is_array($request->billing_exceptions)) {
            $exceptionsData = $request->billing_exceptions;
            
            // Make sure the structure exists (e.g., if there's at least one office)
            if (isset($exceptionsData['office']) && is_array($exceptionsData['office'])) {
                foreach ($exceptionsData['office'] as $index => $office) {
                    // Only save if there's some data
                    if ($office || $exceptionsData['invoice_to_agent'][$index] || $exceptionsData['currency'][$index] || $exceptionsData['payment_terms'][$index]) {
                        $agent->billingExceptions()->create([
                            'office' => $office,
                            'invoice_to_agent' => $exceptionsData['invoice_to_agent'][$index] ?? null,
                            'currency' => $exceptionsData['currency'][$index] ?? null,
                            'payment_terms' => $exceptionsData['payment_terms'][$index] ?? null,
                        ]);
                    }
                }
            }
        }

        // Handle File Uploads for SOP
        if ($request->hasFile('sop_documents')) {
            foreach ($request->file('sop_documents') as $file) {
                $filename = $file->getClientOriginalName();
                $path = $file->store('agent_documents', 'public');
                $agent->documents()->create([
                    'section' => 'sop',
                    'filename' => $filename,
                    'file_path' => $path,
                ]);
            }
        }

        // Handle File Uploads for Pricing
        if ($request->hasFile('pricing_documents')) {
            foreach ($request->file('pricing_documents') as $file) {
                $filename = $file->getClientOriginalName();
                $path = $file->store('agent_documents', 'public');
                $agent->documents()->create([
                    'section' => 'pricing',
                    'filename' => $filename,
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('agents.edit', $id)->with('success', 'Agent updated successfully.');
    }

    public function deleteDocument($id)
    {
        $document = \App\Models\AgentDocument::findOrFail($id);
        
        // Delete file from storage
        \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        
        // Delete record from DB
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    public function storeContact(Request $request, $agent_id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        \App\Models\Contact::create([
            'agent_id' => $agent_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact') ? 1 : 0,
        ]);

        return redirect()->route('agents.edit', $agent_id)->with('success', 'Contact added successfully.');
    }

    public function editContact($id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        return view('Agents.contacts.edit', compact('contact'));
    }

    public function updateContact(Request $request, $id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact') ? 1 : 0,
        ]);

        return redirect()->route('agents.edit', $contact->agent_id)->with('success', 'Contact updated successfully.');
    }

    public function destroyContact($id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        $agent_id = $contact->agent_id;
        $contact->delete();

        return redirect()->route('agents.edit', $agent_id)->with('success', 'Contact deleted successfully.');
    }

    public function storeUser(Request $request, $agent_id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        \App\Models\AgentUser::create([
            'agent_id' => $agent_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
        ]);

        return redirect()->route('agents.edit', $agent_id)->with('success', 'User added successfully.');
    }

    public function editUser($id)
    {
        $user = \App\Models\AgentUser::findOrFail($id);
        return view('Agents.Users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = \App\Models\AgentUser::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
        ]);

        return redirect()->route('agents.edit', $user->agent_id)->with('success', 'User updated successfully.');
    }

    public function destroyUser($id)
    {
        $user = \App\Models\AgentUser::findOrFail($id);
        $agent_id = $user->agent_id;
        $user->delete();

        return redirect()->route('agents.edit', $agent_id)->with('success', 'User deleted successfully.');
    }

    private function multipleEmailsValidator(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }

            $emails = preg_split('/\s*[,;]\s*/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($emails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $fail('Each email address must be valid.');
                    return;
                }
            }
        };
    }
}
