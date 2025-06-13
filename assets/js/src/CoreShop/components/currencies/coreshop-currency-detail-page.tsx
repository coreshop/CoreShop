import React from 'react';
import { useFetch } from '../../hooks/useFetch';
import { useCountryActions } from '../../hooks/useCountryActions';
import { Typography, Spin, Form, Input, Button } from 'antd';
import { Currency } from "./types";

const { Title } = Typography;

type CurrencyDetailProps = {
    id: number;
    onAfterSave?: () => void;
};

export const CoreShopCurrencyDetailPage: React.FC<CurrencyDetailProps> = ({ id, onAfterSave }) => {
    const { data, loading, error, refetch } = useFetch(`/admin/coreshop/currencies/get?id=${id}`);
    const [form] = Form.useForm();
    const { update: updateCountry } = useCountryActions();

    if (loading) return <Spin />;
    if (error) return <p>Error loading country</p>;
    if (!data || !data.data) return <p>No country data</p>;

    const currency: Currency = data.data;

    // init values
    const initialValues = {
        name: currency.name,
        symbol: currency.symbol,
        isoCode: currency.isoCode,
        numericIsoCode: currency.numericIsoCode,
    };

    const onFinish = async (values: any) => {
        await updateCountry(values, currency.id, '/admin/coreshop/currencies/save');
        onAfterSave?.();
    };

    return (
        <div style={{ paddingLeft: 24, paddingRight: 24 }}>
            <Title level={2}>{currency.name}</Title>
            <Form
                form={form}
                layout="vertical"
                initialValues={initialValues}
                onFinish={onFinish}
            >
                <Form.Item
                    label="Name"
                    name="name"
                    rules={[{ required: true, message: 'Please input the name' }]}
                >
                    <Input />
                </Form.Item>

                <Form.Item
                    label="IsoCode"
                    name="isoCode"
                    rules={[{ required: true, message: 'Please input the name' }]}
                >
                    <Input />
                </Form.Item>

                <Form.Item
                    label="Numeric ISO Code"
                    name="numericIsoCode"
                    rules={[{ required: true, message: 'Please input the name' }]}
                >
                    <Input type={'number'} />
                </Form.Item>

                <Form.Item
                    label="Symbol"
                    name="symbol"
                    rules={[{ required: true, message: 'Please input the ISO Code' }]}
                >
                    <Input />
                </Form.Item>

                <Form.Item>
                    <Button type="primary" htmlType="submit">
                        Save
                    </Button>
                </Form.Item>
            </Form>
        </div>
    );
};
