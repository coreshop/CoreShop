import { Header, Content } from '@pimcore/studio-ui-bundle/components';
import React, { useState } from 'react';
import { useFetch } from '../hooks/useFetch';
import { Collapse, List, Typography, Tag, Spin, Button, Layout, Row, Col, Flex, Dropdown, Menu } from 'antd';
import { useFormModal } from '@pimcore/studio-ui-bundle/components';
import { useCountryActions } from '../hooks/useCountryActions';
import { CoreShopCountryDetailPage } from './coreshop-country-detail-page';

const { Panel } = Collapse;

type Country = {
    id: number;
    name: string;
    isoCode: string;
    zoneName: string;
    active: boolean;
};

export const CoreShopCountriesPage = (): React.JSX.Element => {
    const { data: countries, loading, error, refetch } = useFetch<Country[]>('/admin/coreshop/countries/list');
    const { input } = useFormModal();
    const { createCountry, deleteCountry } = useCountryActions();

    const [selectedCountry, setSelectedCountry] = useState<Country | null>(null);

    const handleCountryClick = (country: Country) => {
        setSelectedCountry(country);
    };

    const groupedByZone = (Array.isArray(countries) ? countries : []).reduce(
        (acc: Record<string, Country[]>, country) => {
            if (!acc[country.zoneName]) {
                acc[country.zoneName] = [];
            }
            acc[country.zoneName].push(country);
            return acc;
        },
        {}
    );

    if (loading) return <Spin />;
    if (error) return <p style={{ color: 'red' }}>Error: {error}</p>;

    const handleCountryCreation = async (value: string) => {
        await createCountry(value, '/admin/coreshop/countries/add');
        refetch();
    };

    const handleDelete = async (id: number) => {
        await deleteCountry(id, '/admin/coreshop/countries/delete');
        if (selectedCountry?.id === id) {
            setSelectedCountry(null);
        }
        refetch();
    };

    const openNewCountry = () => {
        input({
            title: 'New Country',
            label: 'Country Name',
            rule: {
                required: true,
                message: 'Please enter a country name'
            },
            okText: 'Create',
            onOk: async (value: string) => {
                await handleCountryCreation(value);
            }
        });
    };

    return (
        <Layout>
            <Content padded overflow={{ x: 'hidden', y: 'auto' }}>
                <Header title="Regions" />
                <Flex gap="small" wrap>
                    <Button
                        type="primary"
                        onClick={openNewCountry}
                        style={{ display: 'inline-block', marginBottom: 16 }}
                    >
                        Create Country
                    </Button>
                </Flex>
                <Row gutter={24}>
                    <Col span={5}>
                        <Collapse accordion>
                            {Object.entries(groupedByZone).map(([zoneName, countries]) => (
                                <Panel header={`${zoneName} (${countries.length})`} key={zoneName}>
                                    <List
                                        dataSource={countries}
                                        renderItem={(country) => {
                                            const menu = (
                                                <Menu>
                                                    <Menu.Item
                                                        key="delete"
                                                        onClick={() => handleDelete(country.id)}
                                                    >
                                                        Delete
                                                    </Menu.Item>
                                                </Menu>
                                            );

                                            return (
                                                <Dropdown
                                                    overlay={menu}
                                                    trigger={['contextMenu']}
                                                    key={country.id}
                                                >
                                                    <List.Item
                                                        onClick={() => handleCountryClick(country)}
                                                        style={{ cursor: 'pointer' }}
                                                        className={
                                                            selectedCountry?.id === country.id
                                                                ? 'ant-list-item-selected'
                                                                : ''
                                                        }
                                                    >
                                                        <Typography.Text>
                                                            {country.name} ({country.isoCode})
                                                        </Typography.Text>
                                                        {country.active ? (
                                                            <Tag color="green">Active</Tag>
                                                        ) : (
                                                            <Tag color="red">Inactive</Tag>
                                                        )}
                                                    </List.Item>
                                                </Dropdown>
                                            );
                                        }}
                                    />
                                </Panel>
                            ))}
                        </Collapse>
                    </Col>
                    <Col span={19} style={{ background: '#fff', minHeight: 400 }}>
                        {selectedCountry ? (
                            <CoreShopCountryDetailPage id={selectedCountry.id} />
                        ) : (
                            <div style={{ padding: 16 }}>
                                <Typography.Text type="secondary">
                                    Select a country to view details.
                                </Typography.Text>
                            </div>
                        )}
                    </Col>
                </Row>
            </Content>
        </Layout>
    );

};
