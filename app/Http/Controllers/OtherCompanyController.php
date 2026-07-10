<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\OtherCompany;
use App\Models\Country;

class OtherCompanyController extends Controller
{
    public function index()
    {
        $companies = OtherCompany::with('country')->orderBy('company_name')->get();

        $countries = $companies
            ->map(fn ($company) => $company->country?->name)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('Other Companies.index', compact('companies', 'countries'));
    }

    public function create()
    {
        $countries = Country::all();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD']; // Sample currencies
        $companyTypes = $this->companyTypeOptions();

        return view('Other Companies.create', compact('countries', 'currencies', 'companyTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_type' => ['nullable', 'string', Rule::in($this->companyTypeOptions())],
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'office_country_id' => 'nullable|exists:countries,id',
        ]);

        OtherCompany::create($request->except('_token'));

        return redirect()->route('other-companies.index')->with('success', 'Company created successfully.');
    }

    public function edit(OtherCompany $otherCompany)
    {
        $countries = Country::all();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AED', 'SGD', 'INR', 'AUD', 'CAD'];
        $companyTypes = $this->companyTypeOptions();

        return view('Other Companies.edit', compact('otherCompany', 'countries', 'currencies', 'companyTypes'));
    }

    public function update(Request $request, OtherCompany $otherCompany)
    {
        $request->validate([
            'company_name'      => 'required|string|max:255',
            'company_type'      => ['nullable', 'string', Rule::in($this->companyTypeOptions())],
            'email'             => 'nullable|email|max:255',
            'phone_number'      => 'nullable|string|max:255',
            'country_id'        => 'nullable|exists:countries,id',
            'office_country_id' => 'nullable|exists:countries,id',
        ]);

        $otherCompany->update($request->except(['_token', '_method']));

        return redirect()->route('other-companies.edit', $otherCompany->id)->with('success', 'Company updated successfully.');
    }

    public function destroy(OtherCompany $otherCompany)
    {
        try {
            $otherCompany->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Company deleted successfully.',
                ]);
            }

            return redirect()->route('other-companies.index')->with('success', 'Company deleted.');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting company.',
                ], 500);
            }

            return redirect()->route('other-companies.index')->with('error', 'Error deleting company.');
        }
    }

    public function createContact($otherCompanyId)
    {
        $otherCompany = OtherCompany::findOrFail($otherCompanyId);
        return view('Other Companies.contacts.create', compact('otherCompany'));
    }

    public function storeContact(Request $request, $otherCompanyId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $otherCompany = OtherCompany::findOrFail($otherCompanyId);
        
        $otherCompany->contacts()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()->route('other-companies.edit', $otherCompanyId)->with('success', 'Contact added successfully.');
    }

    public function editContact($otherCompanyId, $contactId)
    {
        $otherCompany = OtherCompany::findOrFail($otherCompanyId);
        $contact = \App\Models\Contact::findOrFail($contactId);
        return view('Other Companies.contacts.edit', compact('otherCompany', 'contact'));
    }

    public function updateContact(Request $request, $otherCompanyId, $contactId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_main_contact' => 'nullable|boolean',
        ]);

        $contact = \App\Models\Contact::findOrFail($contactId);
        
        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'is_main_contact' => $request->has('is_main_contact'),
        ]);

        return redirect()->route('other-companies.edit', $otherCompanyId)->with('success', 'Contact updated successfully.');
    }

    public function destroyContact($otherCompanyId, $contactId)
    {
        $contact = \App\Models\Contact::findOrFail($contactId);
        $contact->delete();
        return redirect()->route('other-companies.edit', $otherCompanyId)->with('success', 'Contact deleted successfully.');
    }

    private function companyTypeOptions(): array
    {
        return [
            'Ship agent',
            'External agent',
            'Vessel owner',
            'Delivery address',
            'Customer group',
            'Customer procurement group',
        ];
    }
}
