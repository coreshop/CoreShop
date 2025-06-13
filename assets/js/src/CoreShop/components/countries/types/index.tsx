type Translation = {
    locale: string;
    name: string;
};

export type Country = {
    id: number;
    isoCode: string;
    zoneName: string;
    active: boolean;
    name: string;
    addressFormat: string;
    zone: number;
    currency:  number;
    salutations: string[];
    translations: Record<string, Translation>;
};