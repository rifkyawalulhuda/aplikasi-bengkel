<?php

namespace App\Http\Controllers\Public;

use App\Actions\Booking\GetPublicBookingPageDataAction;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class LandingPageController extends Controller
{
    public function index(GetPublicBookingPageDataAction $getPublicBookingPageData): Response
    {
        $bookingPageData = $getPublicBookingPageData->handle();

        return Inertia::render('public/LandingPage', [
            'seo' => [
                ...config('workshop.landing.seo'),
                'canonicalUrl' => route('home'),
            ],
            'tagline' => config('workshop.tagline'),
            'serviceAreas' => config('workshop.service_areas'),
            'highlights' => config('workshop.landing.highlights'),
            'howItWorks' => config('workshop.landing.how_it_works'),
            'coverageContent' => config('workshop.landing.coverage'),
            'faqs' => config('workshop.landing.faqs'),
            'testimonials' => config('workshop.landing.testimonials'),
            'cta' => config('workshop.landing.cta'),
            'packages' => $bookingPageData['packages'],
            'customItems' => $bookingPageData['customItems'],
            'serviceFee' => $bookingPageData['serviceFee'],
            'availableSlots' => $bookingPageData['availableSlots'],
        ]);
    }

    public function __invoke(): Response
    {
        return $this->index();
    }
}
