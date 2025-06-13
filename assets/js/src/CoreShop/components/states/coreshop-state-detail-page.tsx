import React from 'react';
import { useFetch } from '../../hooks/useFetch';
import { Typography, Spin, Tabs, Form, Input, Checkbox, Button } from 'antd';
import { CountrySelect } from "../../fields/CountrySelect";
import { useStateActions } from "../../hooks/useStateActions";
import { useFetchGetValidLanguages } from '../../hooks/useFetchGetValidLanguages';
import { State } from "./types";

const { Title } = Typography;
const { TabPane } = Tabs;

type StateDetailProps = {
    id: number;
    onAfterSave?: () => void;
};

export const CoreShopStateDetailPage: React.FC<StateDetailProps> = ({ id, onAfterSave }) => {
    const { data, loading, error} = useFetch(`/admin/coreshop/states/get?id=${id}`);
    const { languages, loading: langLoading } = useFetchGetValidLanguages();
    const [form] = Form.useForm();
    const { update: updateState } = useStateActions();

    if (loading) return <Spin />;
    if (error) return <p>Error loading country</p>;
    if (!data || !data.data) return <p>No country data</p>;

    const state: State = data.data;
    const translationKeys = Object.keys(state.translations);

    // init values
    const initialValues = {
        countryName: state.countryName,
        isoCode: state.isoCode,
        country: state.country,
        active: state.active,
        translations: Object.fromEntries(
            translationKeys.map((lang) => [lang, { name: state.translations[lang].name }])
        ),
    };

    const onFinish = async (values: any) => {
        await updateState(values, state.id, '/admin/coreshop/states/save');
        onAfterSave?.();
    };

    return (
        <div style={{ paddingLeft: 24, paddingRight: 24 }}>
            <Title level={2}>{state.name}</Title>
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

                <Form.Item name="active" valuePropName="checked" label="Active">
                    <Checkbox />
                </Form.Item>

                <Form.Item
                    label="Country"
                    name="country"
                    rules={[{ required: true, message: 'Please select a country' }]}
                >
                    <CountrySelect value={state.country}/>
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
