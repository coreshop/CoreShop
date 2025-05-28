import React from 'react';
import { Select, Spin, message } from 'antd';
import { useFetch } from '../../hooks/useFetch';

const { Option } = Select;

export const CurrencySelect: React.FC<{ value?: number; onChange?: (val: number) => void }> = ({ value, onChange }) => {
    const { data, loading, error } = useFetch('/admin/coreshop/currencies/list');

    if (loading) return <Spin />;
    if (error) {
        message.error('Failed to load currencies');
        return <p>Error loading currencies</p>;
    }

    return (
        <Select
            value={value}
            onChange={onChange}
            placeholder="Select a currency"
            allowClear
        >
            {data.map((currency: { id: number; name: string }) => (
                <Option key={currency.id} value={currency.id}>
                    {currency.name}
                </Option>
            ))}
        </Select>
    );
};
