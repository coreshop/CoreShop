import React from 'react';
import { Select, Spin, message } from 'antd';
import { useFetch } from '../../hooks/useFetch';

const { Option } = Select;

export const ZoneSelect: React.FC<{ value?: number; onChange?: (val: number) => void }> = ({ value, onChange }) => {
    const { data, loading, error } = useFetch('/admin/coreshop/zones/list');

    if (loading) return <Spin />;
    if (error) {
        message.error('Failed to load zones');
        return <p>Error loading zones</p>;
    }

    return (
        <Select
            value={value}
            onChange={onChange}
            placeholder="Select a zone"
            allowClear
        >
            {data.map((zone: { id: number; name: string }) => (
                <Option key={zone.id} value={zone.id}>
                    {zone.name}
                </Option>
            ))}
        </Select>
    );
};
