<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Booking\UpdateBookingFooterLocationAction;
use App\Actions\Booking\UpdateBookingServiceFeeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingFooterLocationRequest;
use App\Http\Requests\Admin\UpdateBookingServiceFeeRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class BookingSettingController extends Controller
{
    public function updateServiceFee(
        UpdateBookingServiceFeeRequest $request,
        UpdateBookingServiceFeeAction $updateBookingServiceFee,
    ): RedirectResponse {
        $updateBookingServiceFee->handle((int) $request->integer('service_fee'));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Biaya layanan booking berhasil diperbarui.',
        ]);

        return back();
    }

    public function updateFooterLocation(
        UpdateBookingFooterLocationRequest $request,
        UpdateBookingFooterLocationAction $updateBookingFooterLocation,
    ): RedirectResponse {
        $updateBookingFooterLocation->handle([
            'address' => trim($request->string('footer_address')->toString()),
            'latitude' => trim($request->string('footer_latitude')->toString()),
            'longitude' => trim($request->string('footer_longitude')->toString()),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Lokasi footer bengkel berhasil diperbarui.',
        ]);

        return back();
    }
}
