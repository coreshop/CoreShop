type Translation = {
    locale: string;
    name: string;
};

 export type State = {
    id: number;
    isoCode: string;
    country: number;
    countryName: string;
    active: boolean;
    name: string;
    translations: Record<string, Translation>;
};