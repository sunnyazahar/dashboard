<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Crr;
use App\Models\Hub;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\User;
use App\Services\OperationsDashboardService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OperationsDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('role')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('office_name');
            $table->timestamps();
        });
        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->string('hub_name');
            $table->string('code')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('agent_name');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('office_id')->nullable();
            $table->timestamps();
        });
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->timestamps();
        });
        Schema::create('customer_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('account_manager_id')->nullable();
            $table->foreignId('accounting_user_id')->nullable();
        });
        Schema::create('customer_vessels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->string('vessel')->nullable();
            $table->timestamps();
        });
        Schema::create('crrs', function (Blueprint $table) {
            $table->id();
            $table->string('stock_number')->unique();
            $table->string('vessel_name')->nullable();
            $table->string('content')->default('Shipspares');
            $table->string('supplier')->nullable();
            $table->string('hub_agent')->nullable();
            $table->string('priority')->nullable();
            $table->unsignedTinyInteger('status')->default(Crr::STATUS_NEW);
            $table->json('flags')->nullable();
            $table->boolean('accept')->default(false);
            $table->timestamps();
        });
        Schema::create('crr_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crr_id');
            $table->timestamps();
        });
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();
            $table->string('departure')->nullable();
            $table->string('consignee')->nullable();
            $table->string('service')->nullable();
            $table->date('deadline_arrival')->nullable();
            $table->date('pre_alert_reminder')->nullable();
            $table->foreignId('account_manager_id')->nullable();
            $table->string('status')->default('Draft');
            $table->timestamps();
        });
        Schema::create('shipment_crr', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->foreignId('crr_id');
        });
        Schema::create('shipment_irregularities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->string('status')->nullable();
            $table->timestamps();
        });
        Schema::create('shipment_pre_alert_reminder_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });

        foreach ([
            'office' => 'offices',
            'hub' => 'hubs',
            'agent' => 'agents',
            'supplier' => 'suppliers',
        ] as $entity => $tableName) {
            Schema::create("user_{$entity}_assignments", function (Blueprint $table) use ($entity, $tableName) {
                $table->foreignId('user_id');
                $table->foreignId("{$entity}_id");
                $table->unique(['user_id', "{$entity}_id"]);
            });
        }
    }

    protected function tearDown(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ([
            'user_supplier_assignments', 'user_agent_assignments', 'user_hub_assignments',
            'user_office_assignments', 'shipment_pre_alert_reminder_sends',
            'shipment_irregularities', 'shipment_crr', 'shipments', 'crr_packages', 'crrs',
            'customer_vessels', 'customer_responsibles', 'customers', 'contacts',
            'suppliers', 'agents', 'hubs', 'offices', 'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();

        parent::tearDown();
    }

    public function test_admin_receives_global_accurate_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'Admin', 'is_active' => true]);
        $activeStock = $this->createCrr('STK-001', ['status' => Crr::STATUS_ACTIVE, 'accept' => false]);
        $this->createCrr('STK-002', ['status' => Crr::STATUS_COMPLETED, 'accept' => true]);
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-001',
            'status' => 'In transit',
            'deadline_arrival' => today()->subDay(),
            'pre_alert_reminder' => today(),
            'service' => 'Courier',
        ]);
        $shipment->crrs()->attach($activeStock);
        $shipment->irregularities()->createMany([
            ['status' => 'Open'],
            ['status' => null],
        ]);

        $dashboard = app(OperationsDashboardService::class)->build($admin);

        $this->assertSame(1, $dashboard['kpis']['activeStocks']);
        $this->assertSame(1, $dashboard['kpis']['unacceptedStocks']);
        $this->assertSame(1, $dashboard['kpis']['activeShipments']);
        $this->assertSame(1, $dashboard['kpis']['overdueArrivals']);
        $this->assertSame(1, $dashboard['kpis']['preAlertsDue']);
        $this->assertSame(2, $dashboard['kpis']['openIrregularities']);
        $this->assertCount(1, $dashboard['overdueShipments']);
    }

    public function test_non_admin_without_assignments_fails_closed(): void
    {
        $user = User::factory()->create(['role' => 'Operations', 'is_active' => true]);
        $this->createCrr('STK-HIDDEN', ['status' => Crr::STATUS_ACTIVE]);
        Shipment::create(['shipment_number' => 'SHIP-HIDDEN', 'status' => 'In transit']);

        $dashboard = app(OperationsDashboardService::class)->build($user);

        $this->assertFalse($dashboard['hasAssignments']);
        $this->assertSame(0, $dashboard['kpis']['activeStocks']);
        $this->assertSame(0, $dashboard['kpis']['activeShipments']);
    }

    public function test_operations_agent_and_supplier_roles_only_receive_assigned_records(): void
    {
        $hub = Hub::withoutEvents(fn () => Hub::create(['hub_name' => 'Assigned Hub', 'code' => 'HUB-A']));
        $otherHub = Hub::withoutEvents(fn () => Hub::create(['hub_name' => 'Other Hub', 'code' => 'HUB-B']));
        $agent = Agent::withoutEvents(fn () => Agent::create(['agent_name' => 'Assigned Agent', 'code' => 'AG-A']));
        $supplier = Supplier::withoutEvents(fn () => Supplier::create(['supplier_name' => 'Assigned Supplier']));

        $hubStock = $this->createCrr('STK-HUB', ['hub_agent' => $hub->code, 'status' => Crr::STATUS_ACTIVE]);
        $this->createCrr('STK-OTHER', ['hub_agent' => $otherHub->code, 'status' => Crr::STATUS_ACTIVE]);
        $agentStock = $this->createCrr('STK-AGENT', ['hub_agent' => $agent->code, 'status' => Crr::STATUS_ACTIVE]);
        $supplierStock = $this->createCrr('STK-SUPPLIER', ['supplier' => $supplier->supplier_name, 'status' => Crr::STATUS_ACTIVE]);

        $hubShipment = Shipment::create(['shipment_number' => 'SHIP-HUB', 'status' => 'In transit']);
        $hubShipment->crrs()->attach($hubStock);
        Shipment::create(['shipment_number' => 'SHIP-OTHER', 'status' => 'In transit']);
        $agentShipment = Shipment::create([
            'shipment_number' => 'SHIP-AGENT',
            'status' => 'In transit',
            'departure' => 'agent:' . $agent->id,
        ]);
        $supplierShipment = Shipment::create(['shipment_number' => 'SHIP-SUPPLIER', 'status' => 'In transit']);
        $supplierShipment->crrs()->attach($supplierStock);

        $operations = User::factory()->create(['role' => 'Operations']);
        $operations->hubs()->attach($hub);
        $accounts = User::factory()->create(['role' => 'Accounts']);
        $accounts->hubs()->attach($hub);
        $agentUser = User::factory()->create(['role' => 'Agents']);
        $agentUser->agents()->attach($agent);
        $supplierUser = User::factory()->create(['role' => 'Supplier']);
        $supplierUser->suppliers()->attach($supplier);

        $service = app(OperationsDashboardService::class);

        $this->assertEquals(['STK-HUB'], $service->visibleCrrs($operations)->pluck('stock_number')->all());
        $this->assertEquals(['SHIP-HUB'], $service->visibleShipments($operations)->pluck('shipment_number')->all());
        $this->assertEquals(['STK-HUB'], $service->visibleCrrs($accounts)->pluck('stock_number')->all());
        $this->assertEquals(['SHIP-HUB'], $service->visibleShipments($accounts)->pluck('shipment_number')->all());
        $this->assertEquals(['STK-AGENT'], $service->visibleCrrs($agentUser)->pluck('stock_number')->all());
        $this->assertEquals(['SHIP-AGENT'], $service->visibleShipments($agentUser)->pluck('shipment_number')->all());
        $this->assertEquals(['STK-SUPPLIER'], $service->visibleCrrs($supplierUser)->pluck('stock_number')->all());
        $this->assertEquals(['SHIP-SUPPLIER'], $service->visibleShipments($supplierUser)->pluck('shipment_number')->all());
    }

    public function test_dashboard_renders_live_data_and_action_links(): void
    {
        $admin = User::factory()->create(['role' => 'Admin', 'is_active' => true]);
        $stock = $this->createCrr('STK-RENDER', [
            'status' => Crr::STATUS_ACTIVE,
            'accept' => false,
        ]);
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-RENDER',
            'status' => 'In transit',
            'deadline_arrival' => today()->subDay(),
        ]);
        $shipment->crrs()->attach($stock);

        $this->actingAs($admin);
        $dashboard = app(OperationsDashboardService::class)->build($admin, 7);
        $html = view('home', compact('dashboard'))->render();

        $this->assertStringContainsString('Operations Dashboard', $html);
        $this->assertStringContainsString('SHIP-RENDER', $html);
        $this->assertStringContainsString('STK-RENDER', $html);
        $this->assertStringContainsString(route('shipments.edit', $shipment->id), $html);
        $this->assertStringContainsString(route('stocks.edit', $stock->id), $html);
    }

    private function createCrr(string $stockNumber, array $attributes = []): Crr
    {
        return Crr::create(array_merge([
            'stock_number' => $stockNumber,
            'content' => 'Shipspares',
            'status' => Crr::STATUS_NEW,
            'accept' => false,
            'flags' => [],
        ], $attributes));
    }
}
