import React from 'react';
import { useFetch } from '../../hooks/useFetch';
import { useCountryActions } from '../../hooks/useCountryActions';
import { ZoneSelect } from '../../fields/ZoneSelect';
import { CurrencySelect } from '../../fields/CurrencySelect';
import { Typography, Spin, Tabs, Form, Input, Select, Checkbox, Button } from 'antd';
import {useFetchGetValidLanguages} from "../../hooks/useFetchGetValidLanguages";
import { Country } from "./types";

const { Title } = Typography;
const { TabPane } = Tabs;

type CountryDetailProps = {
    id: number;
    onAfterSave?: () => void;
};

export const CoreShopCountryDetailPage: React.FC<CountryDetailProps> = ({ id, onAfterSave }) => {
    const { data, loading, error, refetch } = useFetch(`/admin/coreshop/countries/get?id=${id}`);
    const { languages, loading: langLoading } = useFetchGetValidLanguages();
    const [form] = Form.useForm();
    const { update: updateCountry } = useCountryActions();

    if (loading) return <Spin />;
    if (error) return <p>Error loading country</p>;
    if (!data || !data.data) return <p>No country data</p>;

    const country: Country = data.data;
    const translationKeys = Object.keys(country.translations);

    // init values
    const initialValues = {
        zone: country.zone,
        currency: country.currency,
        isoCode: country.isoCode,
        active: country.active,
        addressFormat: country.addressFormat,
        salutations: country.salutations ,
        translations: Object.fromEntries(
            translationKeys.map((lang) => [lang, { name: country.translations[lang].name }])
        ),
    };

    const onFinish = async (values: any) => {
        await updateCountry(values, country.id, '/admin/coreshop/countries/save');
        onAfterSave?.();
    };

    return (
        <div style={{ paddingLeft: 24, paddingRight: 24 }}>
            <Title level={2}>{country.name}</Title>
            <Form
                form={form}
                layout="vertical"
                initialValues={initialValues}
                onFinish={onFinish}
            >
                <Tabs defaultActiveKey={translationKeys[0]}>
                    {languages.map((langCode) => (
                        <TabPane tab={langCode.toUpperCase()} key={langCode} forceRender> {/* we need the forceRender because by default the ANtDesign not mount the non visible tabs */}
                            <Form.Item
                                label="Name"
                                name={['translations', langCode, 'name']}
                                rules={[{ required: true, message: 'Please input the name' }]}
                            >
                                <Input />
                            </Form.Item>
                        </TabPane>
                    ))}
                </Tabs>

                <Form.Item
                    label="ISO Code"
                    name="isoCode"
                    rules={[{ required: true, message: 'Please input the ISO Code' }]}
                >
                    <Input />
                </Form.Item>

                <Form.Item
                    label="Zone"
                    name="zone"
                    rules={[{ required: true, message: 'Please select a zone' }]}
                >
                    <ZoneSelect />
                </Form.Item>

                <Form.Item name="active" valuePropName="checked" label="Active">
                    <Checkbox />
                </Form.Item>


                <Form.Item
                    label="Address Format"
                    name="addressFormat"
                    rules={[{ required: true, message: 'Please insert Address Format' }]}
                >
                    <Input.TextArea rows={6} />
                </Form.Item>

                <Form.Item name="salutations" label="Salutations">
                    <Select mode="tags" style={{ width: '100%' }} placeholder="Add salutations" />
                </Form.Item>

                <Form.Item
                    label="Currency"
                    name="currency"
                    rules={[{ required: true, message: 'Please select a currency' }]}
                >
                    <CurrencySelect />
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
