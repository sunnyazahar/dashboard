<?php

namespace App\Http\Controllers;

use App\Models\AdministrationChangeLog;
use App\Models\Agent;
use App\Models\AgentUser;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerVessel;
use App\Models\Hub;
use App\Models\HubUser;
use App\Models\Office;
use App\Models\OtherCompany;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class AdministrationChangeLogController extends Controller
{
    private const ENTITY_TYPES = [
        Office::class => 'Office',
        Hub::class => 'Hub',
        Agent::class => 'Agent',
        OtherCompany::class => 'Other company',
        Supplier::class => 'Supplier',
        Customer::class => 'Customer',
        CustomerVessel::class => 'Vessel',
        Contact::class => 'Contact',
        HubUser::class => 'Hub user',
        AgentUser::class => 'Agent user',
    ];

    public function index()
    {
        $users = User::query()->orderBy('name')->get(['id', 'name']);
        $entityTypes = self::ENTITY_TYPES;

        return view('administration.change-logs', compact('users', 'entityTypes'));
    }

    public function search(Request $request)
    {
        $query = AdministrationChangeLog::query()
            ->with(['user', 'loggable'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->filled('entity_type') && array_key_exists($request->entity_type, self::ENTITY_TYPES)) {
            $query->where('loggable_type', $request->entity_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('field', 'like', '%' . $search . '%');
            });
        }

        $logs = $query->paginate(50);

        $rows = $logs->getCollection()->map(function (AdministrationChangeLog $log) {
            return [
                'date' => optional($log->created_at)->format('d.m.Y H:i'),
                'entity_label' => self::ENTITY_TYPES[$log->loggable_type] ?? class_basename($log->loggable_type),
                'record_name' => $this->resolveRecordName($log),
                'record_url' => $this->resolveRecordUrl($log),
                'title' => $log->title,
                'description' => $log->description,
                'user_name' => $log->user?->name ?? 'System',
            ];
        })->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem(),
            ],
        ]);
    }

    private function resolveRecordName(AdministrationChangeLog $log): string
    {
        $model = $log->loggable;

        if (! $model) {
            return '#' . $log->loggable_id . ' (deleted)';
        }

        return match (true) {
            $model instanceof Office => (string) ($model->office_name ?: 'Office #' . $model->id),
            $model instanceof Hub => (string) ($model->hub_name ?: 'Hub #' . $model->id),
            $model instanceof Agent => (string) ($model->agent_name ?: 'Agent #' . $model->id),
            $model instanceof OtherCompany => (string) ($model->company_name ?: 'Company #' . $model->id),
            $model instanceof Supplier => (string) ($model->supplier_name ?: 'Supplier #' . $model->id),
            $model instanceof Customer => (string) ($model->customer_name ?: 'Customer #' . $model->id),
            $model instanceof CustomerVessel => (string) ($model->vessel ?: 'Vessel #' . $model->id),
            $model instanceof Contact,
            $model instanceof HubUser,
            $model instanceof AgentUser => (string) ($model->name ?: 'User #' . $model->id),
            default => class_basename($model) . ' #' . $model->getKey(),
        };
    }

    private function resolveRecordUrl(AdministrationChangeLog $log): ?string
    {
        $model = $log->loggable;

        if (! $model) {
            return null;
        }

        try {
            return match (true) {
                $model instanceof Office => route('offices.edit', $model->id),
                $model instanceof Hub => route('hub.show', $model->id),
                $model instanceof Agent => route('agents.edit', $model->id),
                $model instanceof OtherCompany => route('other-companies.edit', $model->id),
                $model instanceof Supplier => route('suppliers.edit', $model->id),
                $model instanceof Customer => route('customers.edit', $model->id),
                $model instanceof CustomerVessel => route('customers.vessels.edit', $model->id),
                $model instanceof Contact && $model->customer_id => route('contacts.edit', $model->id),
                $model instanceof Contact && $model->hub_id => route('hub.contacts.edit', [$model->hub_id, $model->id]),
                $model instanceof Contact && $model->agent_id => route('agents.contacts.edit', $model->id),
                $model instanceof Contact && $model->supplier_id => route('suppliers.contacts.edit', [$model->supplier_id, $model->id]),
                $model instanceof Contact && $model->other_company_id => route('other-companies.contacts.edit', [$model->other_company_id, $model->id]),
                $model instanceof HubUser => route('hub.users.edit', [$model->hub_id, $model->id]),
                $model instanceof AgentUser => route('agents.users.edit', $model->id),
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }
}
