<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Booking\GetBookingServiceFeeAction;
use App\Actions\ServicePackage\ActivateServicePackageAction;
use App\Actions\ServicePackage\DeactivateServicePackageAction;
use App\Actions\ServicePackage\DeleteServicePackageAction;
use App\Actions\ServicePackage\UpsertServicePackageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServicePackageRequest;
use App\Http\Requests\Admin\UpdateServicePackageRequest;
use App\Models\ServicePackage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ServicePackageController extends Controller
{
    public function index(GetBookingServiceFeeAction $getBookingServiceFee): Response
    {
        return Inertia::render(
            'admin/ServicePackagesPage',
            $this->pageData(serviceFee: $getBookingServiceFee->handle()),
        );
    }

    public function edit(
        ServicePackage $servicePackage,
        GetBookingServiceFeeAction $getBookingServiceFee,
    ): Response {
        $servicePackage->load('items');

        return Inertia::render(
            'admin/ServicePackagesPage',
            $this->pageData(
                editingPackage: $servicePackage,
                serviceFee: $getBookingServiceFee->handle(),
            ),
        );
    }

    public function store(StoreServicePackageRequest $request, UpsertServicePackageAction $upsertServicePackage): RedirectResponse
    {
        $upsertServicePackage->handle($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Paket servis berhasil dibuat.',
        ]);

        return to_route('admin.service-packages.index');
    }

    public function update(
        UpdateServicePackageRequest $request,
        ServicePackage $servicePackage,
        UpsertServicePackageAction $upsertServicePackage,
    ): RedirectResponse {
        $upsertServicePackage->handle(
            attributes: $request->validated(),
            servicePackage: $servicePackage,
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Paket servis berhasil diperbarui.',
        ]);

        return to_route('admin.service-packages.edit', $servicePackage);
    }

    public function deactivate(
        ServicePackage $servicePackage,
        DeactivateServicePackageAction $deactivateServicePackage,
    ): RedirectResponse {
        $deactivateServicePackage->handle($servicePackage);

        Inertia::flash('toast', [
            'type' => 'info',
            'message' => 'Paket servis dinonaktifkan dan tidak tampil lagi di halaman publik.',
        ]);

        return to_route('admin.service-packages.index');
    }

    public function activate(
        ServicePackage $servicePackage,
        ActivateServicePackageAction $activateServicePackage,
    ): RedirectResponse {
        $activateServicePackage->handle($servicePackage);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Paket servis diaktifkan kembali dan tampil lagi di halaman publik.',
        ]);

        return to_route('admin.service-packages.index');
    }

    public function destroy(
        ServicePackage $servicePackage,
        DeleteServicePackageAction $deleteServicePackage,
    ): RedirectResponse {
        if ($servicePackage->is_active) {
            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => 'Nonaktifkan paket terlebih dulu sebelum menghapus permanen.',
            ]);

            return to_route('admin.service-packages.index');
        }

        $deleteServicePackage->handle($servicePackage);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Paket servis berhasil dihapus permanen.',
        ]);

        return to_route('admin.service-packages.index');
    }

    private function pageData(
        ?ServicePackage $editingPackage = null,
        int $serviceFee = 0,
    ): array {
        $packages = ServicePackage::query()
            ->with(['items:id,service_package_id,name,description,display_order'])
            ->withCount('bookings')
            ->ordered()
            ->get()
            ->map(fn (ServicePackage $servicePackage): array => [
                'id' => $servicePackage->id,
                'name' => $servicePackage->name,
                'slug' => $servicePackage->slug,
                'shortDescription' => $servicePackage->short_description,
                'description' => $servicePackage->description,
                'price' => $servicePackage->price,
                'durationEstimateMinutes' => $servicePackage->duration_estimate_minutes,
                'isActive' => $servicePackage->is_active,
                'isFeatured' => $servicePackage->is_featured,
                'displayOrder' => $servicePackage->display_order,
                'bookingsCount' => $servicePackage->bookings_count,
                'itemsCount' => $servicePackage->items->count(),
                'items' => $servicePackage->items->map(fn ($item): array => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'displayOrder' => $item->display_order,
                ])->all(),
            ])
            ->all();

        return [
            'serviceFee' => $serviceFee,
            'packages' => $packages,
            'editingPackage' => $editingPackage
                ? [
                    'id' => $editingPackage->id,
                    'name' => $editingPackage->name,
                    'shortDescription' => $editingPackage->short_description,
                    'description' => $editingPackage->description,
                    'price' => $editingPackage->price,
                    'durationEstimateMinutes' => $editingPackage->duration_estimate_minutes,
                    'isActive' => $editingPackage->is_active,
                    'isFeatured' => $editingPackage->is_featured,
                    'displayOrder' => $editingPackage->display_order,
                    'items' => $editingPackage->items
                        ->sortBy('display_order')
                        ->values()
                        ->map(fn ($item): array => [
                            'name' => $item->name,
                            'description' => $item->description,
                        ])
                        ->all(),
                ]
                : null,
        ];
    }
}
