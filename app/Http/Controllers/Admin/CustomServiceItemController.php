<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Booking\GetBookingServiceFeeAction;
use App\Actions\CustomServiceItem\DeactivateCustomServiceItemAction;
use App\Actions\CustomServiceItem\DeleteCustomServiceItemAction;
use App\Actions\CustomServiceItem\UpsertCustomServiceItemAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomServiceItemRequest;
use App\Http\Requests\Admin\UpdateCustomServiceItemRequest;
use App\Models\CustomServiceItem;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CustomServiceItemController extends Controller
{
    public function index(GetBookingServiceFeeAction $getBookingServiceFee): Response
    {
        return Inertia::render(
            'admin/CustomServiceItemsPage',
            $this->pageData(serviceFee: $getBookingServiceFee->handle()),
        );
    }

    public function edit(
        CustomServiceItem $customServiceItem,
        GetBookingServiceFeeAction $getBookingServiceFee,
    ): Response {
        return Inertia::render(
            'admin/CustomServiceItemsPage',
            $this->pageData(
                editingItem: $customServiceItem,
                serviceFee: $getBookingServiceFee->handle(),
            ),
        );
    }

    public function store(
        StoreCustomServiceItemRequest $request,
        UpsertCustomServiceItemAction $upsertCustomServiceItem,
    ): RedirectResponse {
        $upsertCustomServiceItem->handle($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Item servis custom berhasil dibuat.',
        ]);

        return to_route('admin.custom-service-items.index');
    }

    public function update(
        UpdateCustomServiceItemRequest $request,
        CustomServiceItem $customServiceItem,
        UpsertCustomServiceItemAction $upsertCustomServiceItem,
    ): RedirectResponse {
        $upsertCustomServiceItem->handle(
            attributes: $request->validated(),
            customServiceItem: $customServiceItem,
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Item servis custom berhasil diperbarui.',
        ]);

        return to_route('admin.custom-service-items.edit', $customServiceItem);
    }

    public function deactivate(
        CustomServiceItem $customServiceItem,
        DeactivateCustomServiceItemAction $deactivateCustomServiceItem,
    ): RedirectResponse {
        $deactivateCustomServiceItem->handle($customServiceItem);

        Inertia::flash('toast', [
            'type' => 'info',
            'message' => 'Item servis custom dinonaktifkan dan tidak tampil lagi di form booking publik.',
        ]);

        return to_route('admin.custom-service-items.index');
    }

    public function destroy(
        CustomServiceItem $customServiceItem,
        DeleteCustomServiceItemAction $deleteCustomServiceItem,
    ): RedirectResponse {
        if ($customServiceItem->is_active) {
            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => 'Nonaktifkan item terlebih dulu sebelum menghapus permanen.',
            ]);

            return to_route('admin.custom-service-items.index');
        }

        $deleteCustomServiceItem->handle($customServiceItem);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Item servis custom berhasil dihapus permanen.',
        ]);

        return to_route('admin.custom-service-items.index');
    }

    private function pageData(
        ?CustomServiceItem $editingItem = null,
        int $serviceFee = 0,
    ): array {
        $items = CustomServiceItem::query()
            ->withCount('bookingCustomItems')
            ->ordered()
            ->get()
            ->map(fn (CustomServiceItem $item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'category' => $item->category,
                'description' => $item->description,
                'price' => $item->price,
                'unitLabel' => $item->unit_label,
                'isActive' => $item->is_active,
                'displayOrder' => $item->display_order,
                'bookingsCount' => $item->booking_custom_items_count,
            ])
            ->all();

        return [
            'serviceFee' => $serviceFee,
            'items' => $items,
            'editingItem' => $editingItem
                ? [
                    'id' => $editingItem->id,
                    'name' => $editingItem->name,
                    'category' => $editingItem->category,
                    'description' => $editingItem->description,
                    'price' => $editingItem->price,
                    'unitLabel' => $editingItem->unit_label,
                    'isActive' => $editingItem->is_active,
                    'displayOrder' => $editingItem->display_order,
                ]
                : null,
        ];
    }
}
