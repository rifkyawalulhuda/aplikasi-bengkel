export type SeoMetadata = {
    title: string;
    description: string;
    keywords?: string[];
    canonicalUrl?: string;
};

export type SelectOption = {
    value: string;
    label: string;
};

export type LandingHighlight = {
    title: string;
    description: string;
};

export type LandingStep = {
    title: string;
    description: string;
};

export type CoverageContent = {
    title: string;
    description: string;
    note: string;
};

export type FaqItem = {
    question: string;
    answer: string;
};

export type TestimonialItem = {
    name: string;
    vehicle: string;
    quote: string;
};

export type LandingCta = {
    title: string;
    description: string;
    points: string[];
};

export type PackageItem = {
    id: number;
    name: string;
    description: string | null;
};

export type ServicePackageSummary = {
    id: number;
    name: string;
    slug: string;
    shortDescription: string | null;
    price: number;
    durationEstimateMinutes: number;
    isFeatured: boolean;
    items: PackageItem[];
};

export type CustomServiceItemSummary = {
    id: number;
    name: string;
    category: string;
    description: string | null;
    price: number;
    unitLabel: string | null;
};

export type BookingPackageType = 'fixed_package' | 'custom_package';

export type BookingCustomerForm = {
    name: string;
    email: string;
    phone: string;
};

export type BookingMotorcycleForm = {
    type: string;
    brand: string;
    model: string;
    year: string;
    plateNumber: string;
};

export type BookingLocationForm = {
    addressText: string;
    houseLandmark: string;
    latitude: string;
    longitude: string;
};

export type BookingScheduleForm = {
    serviceDate: string;
    serviceTime: string;
    notes: string;
};

export type BookingCustomItemSelection = {
    id: number;
    qty: number;
};

export type BookingPagePrefill = {
    packageSlug: string | null;
    startsInCustomMode: boolean;
};

export type DashboardStats = {
    bookingsToday: number;
    pendingBookings: number;
    confirmedBookings: number;
    completedBookings: number;
    visitorsToday: number;
};

export type VisitorTrendPoint = {
    date: string | null;
    totalVisits: number;
    uniqueVisits: number;
};

export type VisitorAnalyticsSummary = {
    todayTotalVisits: number;
    todayUniqueVisitors: number;
    pageViews: number;
    trackedPaths: number;
};

export type VisitorDailyAnalyticsPoint = {
    date: string | null;
    totalVisits: number;
    uniqueVisitors: number;
    pageViews: number;
};

export type VisitorTopPath = {
    path: string;
    totalViews: number;
    uniqueVisitors: number;
};

export type PaginationMeta<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    prev_page_url: string | null;
    next_page_url: string | null;
};

export type AdminBookingListFilters = {
    search: string;
    status: string;
    date: string;
};

export type AdminBookingListItem = {
    id: number;
    bookingCode: string;
    customerName: string;
    customerPhone: string;
    packageName: string;
    serviceDate: string | null;
    serviceDateLabel: string | null;
    serviceTime: string;
    status: string;
    statusLabel: string;
    requiresManualReview: boolean;
    totalPrice: number;
};

export type AdminBookingStatusOption = {
    value: string;
    label: string;
};

export type AdminBookingCustomItem = {
    id: number;
    name: string;
    price: number;
    qty: number;
    subtotal: number;
};

export type AdminBookingStatusHistoryItem = {
    id: number;
    oldStatus: string | null;
    oldStatusLabel: string | null;
    newStatus: string;
    newStatusLabel: string;
    note: string | null;
    changedAt: string | null;
    changedBy: string | null;
};

export type AdminBookingDetail = {
    bookingCode: string;
    status: string;
    statusLabel: string;
    confirmedAt: string | null;
    completedAt: string | null;
    requiresManualReview: boolean;
    customer: {
        name: string;
        email: string;
        phone: string;
    };
    motorcycle: {
        type: string;
        typeLabel: string;
        brand: string;
        model: string;
        year: string | null;
        plateNumber: string | null;
    };
    service: {
        packageType: string;
        packageTypeLabel: string;
        packageName: string;
        serviceDate: string | null;
        serviceDateLabel: string | null;
        serviceTime: string;
        notes: string | null;
        customItems: AdminBookingCustomItem[];
    };
    pricing: {
        packagePrice: number;
        subtotal: number;
        serviceFee: number;
        total: number;
    };
    location: {
        addressText: string;
        houseLandmark: string;
        latitude: string;
        longitude: string;
        mapUrl: string;
    };
    adminNotes: string | null;
    statusHistory: AdminBookingStatusHistoryItem[];
};

export type AdminServicePackageItem = {
    id?: number;
    name: string;
    description: string | null;
    displayOrder?: number;
};

export type AdminServicePackageSummary = {
    id: number;
    name: string;
    slug: string;
    shortDescription: string | null;
    description: string | null;
    price: number;
    durationEstimateMinutes: number;
    isActive: boolean;
    isFeatured: boolean;
    displayOrder: number;
    bookingsCount: number;
    itemsCount: number;
    items: AdminServicePackageItem[];
};

export type AdminServicePackageFormData = {
    id: number;
    name: string;
    shortDescription: string | null;
    description: string | null;
    price: number;
    durationEstimateMinutes: number;
    isActive: boolean;
    isFeatured: boolean;
    displayOrder: number;
    items: AdminServicePackageItem[];
};

export type AdminCustomServiceItemSummary = {
    id: number;
    name: string;
    slug: string;
    category: string;
    description: string | null;
    price: number;
    unitLabel: string | null;
    isActive: boolean;
    displayOrder: number;
    bookingsCount: number;
};

export type AdminCustomServiceItemFormData = {
    id: number;
    name: string;
    category: string;
    description: string | null;
    price: number;
    unitLabel: string | null;
    isActive: boolean;
    displayOrder: number;
};
