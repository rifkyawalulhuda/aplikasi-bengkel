<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Booking\UpdateBookingServiceFeeAction;
use App\Http\Controllers\Controller;
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
}
