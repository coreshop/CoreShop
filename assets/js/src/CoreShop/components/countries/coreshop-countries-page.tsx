import { Header, Content } from '@pimcore/studio-ui-bundle/components';
import React, { useState } from 'react';
import { useFetch } from '../../hooks/useFetch';
import { Collapse, List, Typography, Tag, Spin, Button, Layout, Row, Col, Flex, Dropdown, Menu } from 'antd';
import { useCountryActions } from '../../hooks/useCountryActions';
import { useEntityActions } from '../../hooks/useEntityActions';
import { CoreShopCountryDetailPage } from './coreshop-country-detail-page';
import { Country } from './types';

const { Panel } = Collapse;

export const CoreshopCountriesPage = (): React.JSX.Element => {
    const { data: countries, loading, error, refetch } = useFetch<Country[]>('/admin/coreshop/countries/list');
    const { create: createCountry, remove: deleteCountry } = useCountryActions();

    const [selectedCountry, setSelectedCountry] = useState<Country | null>(null);

    const { handleDelete, openCreateModal } = useEntityActions<Country>({
        createEndpoint: '/admin/coreshop/countries/add',
        deleteEndpoint: '/admin/coreshop/countries/delete',
        createFn: createCountry,
        deleteFn: deleteCountry,
        refetch,
        getSelected: () => selectedCountry,
        clearSelected: () => setSelectedCountry(null),
    });

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

    return (
        <Layout>
            <Content padded overflow={{ x: 'hidden', y: 'auto' }}>
                <Header title="Regions" />
                <Flex gap="small" wrap>
                    <Button
                        type="primary"
                        onClick={openCreateModal}
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
                                            return (
                                                <Dropdown
                                                    menu={{
                                                        items: [
                                                            {
                                                                key: 'delete',
                                                                label: 'Delete',
                                                                onClick: () => handleDelete(country.id),
                                                            },
                                                        ],
                                                    }}
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
                            <CoreShopCountryDetailPage key={selectedCountry.id} id={selectedCountry.id} onAfterSave={refetch} />
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
