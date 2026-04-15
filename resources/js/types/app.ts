export type AppSharedData = {
    app: {
        name: string;
        url: string;
        timezone: string;
        locale: string;
    };
    workshop: {
        brandName: string;
        contactPhone: string;
        contactWhatsapp: string;
        serviceAreas: string[];
    };
};
