<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerInvoiceDetail;
use App\Models\CustomerResponsible;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Country;
use App\Models\User;
use App\Models\CustomerGroup;
use App\Models\CustomerSop;
use App\Models\CustomerNotificationSetting;
use App\Models\CustomerDocument;
use App\Models\CustomerVessel;
use App\Models\Contact;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with([
            'primaryAddress.country',
            'responsible.accountManager.office',
            'responsible.salesManager',
            'contacts' => fn ($query) => $query->where('is_main_contact', true),
        ])->orderBy('customer_name')->get();

        $responsibleOffices = $customers
            ->map(fn ($customer) => $customer->responsible?->accountManager?->office?->office_short_name)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $accountManagers = $customers
            ->map(fn ($customer) => $customer->responsible?->accountManager?->name)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $salesManagers = $customers
            ->map(fn ($customer) => $customer->responsible?->salesManager?->name)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $countries = $customers
            ->map(fn ($customer) => $customer->primaryAddress?->country?->name)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('customers.index', compact(
            'customers',
            'responsibleOffices',
            'accountManagers',
            'salesManagers',
            'countries'
        ));
    }

    public function create()
    {
        $countries = Country::all();
        $salesManagers = Contact::where('category', 'sales')->get();
        $groups = CustomerGroup::all();
        
        return view('customers.create', compact('countries', 'salesManagers', 'groups'));
    }

    public function store(Request $request)
    {
        // Server-side validation
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:150',
            'email' => ['required', 'string', 'max:500', $this->multipleEmailsValidator()],
            'customer_group' => 'required',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required',
            'invoice_recipient_name' => 'required|string',
            'invoice_recipient_address' => 'required|string',
            'invoice_city' => 'required|string',
            'invoice_country' => 'required',
            'currency' => 'required',
            'invoicing_email' => 'required|email',
            'sales_manager' => 'required',
            'main_account_manager' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Handle customer_group_id (if N/A, set to null)
            $customerGroupId = ($request->customer_group === 'N/A') ? null : $request->customer_group;

            // 1. Create Customer
            $customer = Customer::create([
                'customer_name' => $request->customer_name,
                'customer_number' => $request->customer_number_fm,
                'customer_group_id' => $customerGroupId,
                'phone' => $request->phone_number,
                'email' => $request->email,
                'internal_shipment' => $request->internal_shipment,
                'remarks' => $request->remarks,
                'special_considerations' => $request->special_considerations,
                'un_locode' => $request->un_locode,
                'show_transport_details' => $request->has('show_transport_details') ? 1 : 0,
                'esea_store_stock_only' => $request->has('esea_store_stock_only') ? 1 : 0,
            ]);

            // 2. Create Addresses
            // Primary Address
            CustomerAddress::create([
                'customer_id' => $customer->id,
                'type' => 'primary',
                'street' => $request->street_address,
                'city' => $request->city,
                'state' => $request->district_state,
                'zip_code' => $request->zip_code,
                'country_id' => $request->country,
                'port_code' => $request->port_code,
            ]);

            // Postal Address (Optional)
            if ($request->filled('postal_street_address') || $request->filled('postal_city')) {
                CustomerAddress::create([
                    'customer_id' => $customer->id,
                    'type' => 'postal',
                    'street' => $request->postal_street_address,
                    'city' => $request->postal_city,
                    'state' => $request->postal_district_state,
                    'zip_code' => $request->postal_zip_code,
                    'country_id' => $request->postal_country,
                ]);
            }

            // Invoice Address (Using dedicated fields from form)
            CustomerAddress::create([
                'customer_id' => $customer->id,
                'type' => 'invoice',
                'street' => $request->invoice_recipient_address,
                'city' => $request->invoice_city,
                'state' => $request->invoice_district_state,
                'zip_code' => $request->invoice_zip_code,
                'country_id' => $request->invoice_country,
            ]);

            // 3. Create Invoice Details
            CustomerInvoiceDetail::create([
                'customer_id' => $customer->id,
                'invoice_recipient_name' => $request->invoice_recipient_name,
                'invoice_email' => $request->invoicing_email,
                'invoice_email_cc' => $request->invoicing_email_cc,
                'currency_code' => $request->currency,
                'payment_terms_days' => $request->payment_terms ?? 30,
                'invoice_frequency' => $request->invoice_frequency,
                'invoice_remarks' => $request->invoicing_remarks,
                'vat_number' => $request->vat_number,
                'eori_number' => $request->eori_number,
            ]);

            // 4. Create Responsible
            CustomerResponsible::create([
                'customer_id' => $customer->id,
                'sales_manager_id' => $request->sales_manager,
                'account_manager_id' => $request->main_account_manager,
                'accounting_user_id' => $request->responsible_accounting_users,
            ]);

            // 5. Create SOP
            CustomerSop::create([
                'customer_id' => $customer->id,
                'send_stocklist' => $request->send_stocklist,
                'onboard_delivery' => $request->onboard_delivery,
                'quotes_prior_to_instructions' => $request->quotes_prior_to_instructions,
                'agreed_rate' => $request->agreed_rate,
                'invoicing_procedure' => $request->invoicing_procedure,
                'pending_entry' => $request->pending_entry,
                'special_pending_routines' => $request->special_pending_routines,
                'other_procedures_comments' => $request->other_procedures_comments,
            ]);

            // 6. Create Notification Settings
            CustomerNotificationSetting::create([
                'customer_id' => $customer->id,
                'notify_stock_items' => $request->notify_stock_items,
                'send_automatic_first_mile_email' => $request->has('send_automatic_first_mile_email') ? 1 : 0,
                'notify_first_mile_email_sent' => $request->notify_first_mile_email_sent,
                'shipping_free_storage_days' => $request->shipping_free_storage_days,
                'shipping_free_storage_weight' => $request->shipping_free_storage_weight,
                'shipping_free_storage_volume' => $request->shipping_free_storage_volume,
                'notify_free_storage_exceeded' => $request->notify_free_storage_exceeded,
            ]);

            DB::commit();

            return redirect()->route('customers.index')->with('success', 'Customer created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving customer: ' . $e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', 'Failed to save customer. Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $customer = Customer::with([
            'primaryAddress',
            'postalAddress',
            'invoiceAddress',
            'invoiceDetail',
            'responsible.accountManager',
            'responsible.salesManager',
            'responsible.accountingUser.office',
            'group',
            'sop',
            'notificationSetting',
            'documents',
            'vessels',
            'contacts'
        ])->findOrFail($id);
        $countries = Country::all();
        $salesManagers = Contact::where('category', 'sales')->get();
        $groups = CustomerGroup::all();
        
        return view('customers.edit', compact('customer', 'countries', 'salesManagers', 'groups', 'id'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Server-side validation (matching create rules)
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:150',
            'email' => ['required', 'string', 'max:500', $this->multipleEmailsValidator()],
            'customer_group' => 'required',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required',
            'invoice_recipient_name' => 'required|string',
            'invoice_recipient_address' => 'required|string',
            'invoice_city' => 'required|string',
            'invoice_country' => 'required',
            'currency' => 'required',
            'invoicing_email' => 'required|email',
            'sales_manager' => 'required',
            'main_account_manager' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Handle customer_group_id
            $customerGroupId = ($request->customer_group === 'N/A') ? null : $request->customer_group;

            // Handle Logo Upload
            $logoPath = $customer->logo;
            if ($request->hasFile('logo')) {
                if ($logoPath) {
                    \Storage::disk('public')->delete($logoPath);
                }
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

            // 1. Update Customer
            $customer->update([
                'customer_name' => $request->customer_name,
                'customer_number' => $request->customer_number_fm,
                'customer_group_id' => $customerGroupId,
                'phone' => $request->phone_number,
                'email' => $request->email,
                'internal_shipment' => $request->internal_shipment,
                'remarks' => $request->remarks,
                'special_considerations' => $request->special_considerations,
                'un_locode' => $request->un_locode,
                'show_transport_details' => $request->has('show_transport_details') ? 1 : 0,
                'esea_store_stock_only' => $request->has('esea_store_stock_only') ? 1 : 0,
                'logo' => $logoPath,
            ]);

            // 2. Update/Create Addresses
            // Primary Address
            CustomerAddress::updateOrCreate(
                ['customer_id' => $customer->id, 'type' => 'primary'],
                [
                    'street' => $request->street_address,
                    'city' => $request->city,
                    'state' => $request->district_state,
                    'zip_code' => $request->zip_code,
                    'country_id' => $request->country,
                    'port_code' => $request->port_code,
                ]
            );

            // Postal Address
            if ($request->filled('postal_street_address')) {
                CustomerAddress::updateOrCreate(
                    ['customer_id' => $customer->id, 'type' => 'postal'],
                    [
                        'street' => $request->postal_street_address,
                        'city' => $request->postal_city,
                        'state' => $request->postal_district_state,
                        'zip_code' => $request->postal_zip_code,
                        'country_id' => $request->postal_country,
                    ]
                );
            }

            // Invoice Address
            CustomerAddress::updateOrCreate(
                ['customer_id' => $customer->id, 'type' => 'invoice'],
                [
                    'street' => $request->invoice_recipient_address,
                    'city' => $request->invoice_city,
                    'state' => $request->invoice_district_state,
                    'zip_code' => $request->invoice_zip_code,
                    'country_id' => $request->invoice_country,
                ]
            );

            // 3. Update Invoice Details
            CustomerInvoiceDetail::updateOrCreate(
                ['customer_id' => $customer->id],
                [
                    'invoice_recipient_name' => $request->invoice_recipient_name,
                    'invoice_email' => $request->invoicing_email,
                    'invoice_email_cc' => $request->invoicing_email_cc,
                    'currency_code' => $request->currency,
                    'payment_terms_days' => $request->payment_terms ?? 30,
                    'invoice_frequency' => $request->invoice_frequency,
                    'invoice_remarks' => $request->invoicing_remarks,
                    'vat_number' => $request->vat_number,
                    'eori_number' => $request->eori_number,
                ]
            );

            // 4. Update Responsible
        CustomerResponsible::updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'sales_manager_id' => $request->sales_manager,
                'account_manager_id' => $request->main_account_manager,
                'accounting_user_id' => $request->responsible_accounting_users,
            ]
        );

        // 5. Update SOP
        CustomerSop::updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'send_stocklist' => $request->send_stocklist,
                'onboard_delivery' => $request->onboard_delivery,
                'quotes_prior_to_instructions' => $request->quotes_prior_to_instructions,
                'agreed_rate' => $request->agreed_rate,
                'invoicing_procedure' => $request->invoicing_procedure,
                'pending_entry' => $request->pending_entry,
                'special_pending_routines' => $request->special_pending_routines,
                'other_procedures_comments' => $request->other_procedures_comments,
            ]
        );

        // 6. Update Notification Settings
        CustomerNotificationSetting::updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'notify_stock_items' => $request->notify_stock_items,
                'send_automatic_first_mile_email' => $request->has('send_automatic_first_mile_email') ? 1 : 0,
                'notify_first_mile_email_sent' => $request->notify_first_mile_email_sent,
                'shipping_free_storage_days' => $request->shipping_free_storage_days,
                'shipping_free_storage_weight' => $request->shipping_free_storage_weight,
                'shipping_free_storage_volume' => $request->shipping_free_storage_volume,
                'notify_free_storage_exceeded' => $request->notify_free_storage_exceeded,
            ]
        );

            // 7. Handle SOP Document Removals
            if ($request->filled('removed_documents')) {
                $idsToRemove = array_filter(explode(',', $request->removed_documents));
                foreach ($idsToRemove as $docId) {
                    $doc = CustomerDocument::find(trim($docId));
                    if ($doc && $doc->customer_id == $customer->id) {
                        \Storage::disk('public')->delete($doc->file_path);
                        $doc->delete();
                    }
                }
            }

            // 8. Handle SOP Document Uploads
            if ($request->hasFile('sop_documents')) {
                foreach ($request->file('sop_documents') as $file) {
                    $path = $file->store('sop_documents', 'public');
                    CustomerDocument::create([
                        'customer_id' => $customer->id,
                        'file_name'   => $file->getClientOriginalName(),
                        'file_path'   => $path,
                        'file_type'   => 'sop',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating customer: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update customer. Error: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Upload a single SOP document for a customer.
     */
    public function uploadDocument(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file provided'], 422);
        }

        $file = $request->file('file');
        $path = $file->store('sop_documents', 'public');

        $doc = CustomerDocument::create([
            'customer_id' => $customer->id,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_type'   => 'sop',
        ]);

        return response()->json([
            'id'        => $doc->id,
            'file_name' => $doc->file_name,
            'file_url'  => asset('storage/' . $doc->file_path),
        ]);
    }

    /**
     * AJAX: Delete a SOP document.
     */
    public function deleteDocument(Request $request, $docId)
    {
        $doc = CustomerDocument::findOrFail($docId);
        \Storage::disk('public')->delete($doc->file_path);
        $doc->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for creating a new vessel.
     */
    public function createVessel($id)
    {
        $customer = Customer::findOrFail($id);
        $customerContacts = $customer->contacts;

        return view('customers.customer-vessels-add', compact('id', 'customerContacts'));
    }

    /**
     * Show the form for creating a new contact.
     */
    public function createContact($id)
    {
        return view('contacts.create', ['customer_id' => $id]);
    }

    /**
     * Show the form for editing a contact.
     */
    public function editContact($id)
    {
        $contact = Contact::findOrFail($id);
        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update a contact's details.
     */
    public function updateContact(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()->route('customers.edit', $contact->customer_id)->with('success', 'Contact updated successfully.');
    }

    /**
     * Delete a contact.
     */
    public function deleteContact($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Store a new contact for a customer.
     */
    public function storeContact(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $customer->contacts()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()->route('customers.edit', $id)->with('success', 'Contact added successfully.');
    }

    /**
     * Store a new vessel for a customer.
     */
    public function storeVessel(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'vessel' => 'required|string|max:255',
            'account_manager' => 'required|string|max:255',
        ]);

        // Extract contact data (expecting only one contact in the array based on form logic)
        $contactData = $request->input('contacts.1', []); // We use key 1 as per the JS implementation

        CustomerVessel::create([
            'customer_id' => $customer->id,
            // Vessel information
            'vessel' => $request->vessel,
            'vessel_name_alias' => $request->vessel_name_alias,
            'vessel_imo' => $request->vessel_imo,
            'shipyard' => $request->shipyard,
            'shipyard_location' => $request->shipyard_location,
            'not_in_transit' => $request->has('not_in_transit') ? 1 : 0,
            'inactive_vessel' => $request->has('inactive_vessel') ? 1 : 0,
            'sanction_blocked' => $request->has('sanction_blocked') ? 1 : 0,
            'financially_blocked' => $request->has('financially_blocked') ? 1 : 0,
            'pre_payment_only' => $request->has('pre_payment_only') ? 1 : 0,
            'customer_vessel_code' => $request->customer_vessel_code,
            'vessel_type_alias' => $request->vessel_type_alias,
            'po_example' => $request->po_example,
            'internal_shipment' => $request->internal_shipment,
            'except_from_hubs' => $request->except_from_hubs,
            'remarks' => $request->remarks,
            // Responsible managers
            'manager' => $request->manager,
            'account_manager' => $request->account_manager,
            'receivers_stocklists' => $request->receivers_stocklists,
            // Invoice details
            'invoice_vessel_separately' => $request->has('invoice_vessel_separately') ? 1 : 0,
            'title_invoice_recipient' => $request->title_invoice_recipient,
            'yearly_customer_reference' => $request->yearly_customer_reference,
            // Home ports
            'home_consolidation_port' => $request->home_consolidation_port,
            'home_delivery_port' => $request->home_delivery_port,
            // Contact details
            'contact_id' => $contactData['contact_id'] ?? null,
            'contact_stocklists' => isset($contactData['stocklists']) ? 1 : 0,
            'contact_pre_alerts' => isset($contactData['pre_alerts']) ? 1 : 0,
            'contact_stock_notifications' => isset($contactData['stock_notifications']) ? 1 : 0,
            'contact_free_storage_notifications' => isset($contactData['free_storage_notifications']) ? 1 : 0,
            'contact_offers' => isset($contactData['offers']) ? 1 : 0,
        ]);

        return redirect()->route('customers.edit', $id)->with('success', 'Vessel added successfully.');
    }

    /**
     * Show the edit form for a vessel.
     */
    public function editVessel($id)
    {
        $vessel = CustomerVessel::with('customer.contacts')->findOrFail($id);
        $customerContacts = $vessel->customer->contacts;

        return view('customers.customer-vessels', compact('vessel', 'customerContacts'));
    }

    /**
     * Update the specified vessel.
     */
    public function updateVessel(Request $request, $id)
    {
        $vessel = CustomerVessel::findOrFail($id);

        $request->validate([
            'vessel' => 'required|string|max:255',
            'account_manager' => 'required|string|max:255',
        ]);

        // Extract contact data
        $contactData = $request->input('contacts.1', []);

        $vessel->update([
            // Vessel information
            'vessel' => $request->vessel,
            'vessel_name_alias' => $request->vessel_name_alias,
            'vessel_imo' => $request->vessel_imo,
            'shipyard' => $request->shipyard,
            'shipyard_location' => $request->shipyard_location,
            'not_in_transit' => $request->has('not_in_transit') ? 1 : 0,
            'inactive_vessel' => $request->has('inactive_vessel') ? 1 : 0,
            'sanction_blocked' => $request->has('sanction_blocked') ? 1 : 0,
            'financially_blocked' => $request->has('financially_blocked') ? 1 : 0,
            'pre_payment_only' => $request->has('pre_payment_only') ? 1 : 0,
            'customer_vessel_code' => $request->customer_vessel_code,
            'vessel_type_alias' => $request->vessel_type_alias,
            'po_example' => $request->po_example,
            'internal_shipment' => $request->internal_shipment,
            'except_from_hubs' => $request->except_from_hubs,
            'remarks' => $request->remarks,
            // Responsible managers
            'manager' => $request->manager,
            'account_manager' => $request->account_manager,
            'receivers_stocklists' => $request->receivers_stocklists,
            // Invoice details
            'invoice_vessel_separately' => $request->has('invoice_vessel_separately') ? 1 : 0,
            'title_invoice_recipient' => $request->title_invoice_recipient,
            'yearly_customer_reference' => $request->yearly_customer_reference,
            // Home ports
            'home_consolidation_port' => $request->home_consolidation_port,
            'home_delivery_port' => $request->home_delivery_port,
            // Contact details
            'contact_id' => $contactData['contact_id'] ?? null,
            'contact_stocklists' => isset($contactData['stocklists']) ? 1 : 0,
            'contact_pre_alerts' => isset($contactData['pre_alerts']) ? 1 : 0,
            'contact_stock_notifications' => isset($contactData['stock_notifications']) ? 1 : 0,
            'contact_free_storage_notifications' => isset($contactData['free_storage_notifications']) ? 1 : 0,
            'contact_offers' => isset($contactData['offers']) ? 1 : 0,
        ]);

        return redirect()->route('customers.edit', $vessel->customer_id)->with('success', 'Vessel updated successfully.');
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
