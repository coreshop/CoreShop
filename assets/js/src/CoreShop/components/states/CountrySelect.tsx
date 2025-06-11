import React from 'react';
import { Select, Spin, message } from 'antd';
import { useFetch } from '../../hooks/useFetch';

const { Option } = Select;

export const CountrySelect: React.FC<{ value?: number; onChange?: (val: number) => void }> = ({ value, onChange }) => {
    const { data, loading, error } = useFetch('/admin/coreshop/countries/list');

    if (loading) return <Spin />;
    if (error) {
        message.error('Failed to load countries');
        return <p>Error loading countries</p>;
    }

    return (
        <Select
            value={value}
            onChange={onChange}
            placeholder="Select a country"
            allowClear
        >
            {data.map((country: { id: number; name: string }) => (
                <Option key={country.id} value={country.id}>
                    {country.name}
                </Option>
            ))}
        </Select>
    );
};
