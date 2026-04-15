<?php

use App\Support\Enums\BookingStatus;

return [
    'code_prefix' => 'BMS',
    'max_per_slot' => 3,
    'default_service_fee' => 0,
    'rate_limit' => [
        'max_attempts' => 5,
        'decay_seconds' => 600,
    ],
    'messages' => [
        'rate_limited' => 'Terlalu banyak percobaan booking dari perangkat ini.',
        'submission_failed' => 'Booking belum berhasil dikirim. Silakan coba lagi beberapa saat lagi atau hubungi admin lewat WhatsApp.',
    ],
    'slot_interval_minutes' => 60,
    'available_hours' => [
        '08:00',
        '09:00',
        '10:00',
        '11:00',
        '13:00',
        '14:00',
        '15:00',
        '16:00',
    ],
    'capacity_statuses' => [
        BookingStatus::Pending->value,
        BookingStatus::Confirmed->value,
        BookingStatus::OnTheWay->value,
        BookingStatus::Rescheduled->value,
    ],
];
