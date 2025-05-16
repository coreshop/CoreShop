import React from 'react';
import { useFetch } from '../hooks/useFetch'; // your hook
import { Typography, Descriptions, Spin } from 'antd';

type CountryDetailProps = {
    id: number;
};

export const CoreShopCountryDetailPage: React.FC<CountryDetailProps> = ({ id}) => {
    const { data, loading, error } = useFetch(`/admin/coreshop/countries/get?id=${id}`);
    if (loading) return <Spin />;
    if (error) return <p>Error loading country</p>;
    const country = data.data;
    console.log(country);
    return (
        <div style={{padding: 24}}>
            <Typography.Title level={2}>{country.name}</Typography.Title>
            <Descriptions bordered column={1}>
                <Descriptions.Item label="ID">{country.id}</Descriptions.Item>
                <Descriptions.Item label="ISO Code">{country.isoCode}</Descriptions.Item>
                <Descriptions.Item label="Zone">{country.zoneName}</Descriptions.Item>
                <Descriptions.Item label="Active">{country.active ? 'Yes' : 'No'}</Descriptions.Item>
            </Descriptions>
        </div>
    );
};