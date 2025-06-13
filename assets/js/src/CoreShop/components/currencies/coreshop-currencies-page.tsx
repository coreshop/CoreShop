import { Header, Content } from '@pimcore/studio-ui-bundle/components';
import React, { useState } from 'react';
import { useFetch } from '../../hooks/useFetch';
import { List, Typography, Spin, Button, Layout, Row, Col, Flex, Dropdown } from 'antd';
import { useCurrencyActions } from '../../hooks/useCurrencyActions';
import { useEntityActions } from '../../hooks/useEntityActions';
import { CoreShopCurrencyDetailPage } from './coreshop-currency-detail-page';
import { Currency } from './types';
export const CoreshopCurrenciesPage = (): React.JSX.Element => {
    const { data: currencies, loading, error, refetch } = useFetch<Currency[]>('/admin/coreshop/currencies/list');
    const { create: createCurrency, remove: deleteCurrency } = useCurrencyActions();

    const [selectedCurrency, setSelectedCurrency] = useState<Currency | null>(null);

    const { handleDelete, openCreateModal } = useEntityActions<Currency>({
        createEndpoint: '/admin/coreshop/currencies/add',
        deleteEndpoint: '/admin/coreshop/currencies/delete',
        createFn: createCurrency,
        deleteFn: deleteCurrency,
        refetch,
        getSelected: () => selectedCurrency,
        clearSelected: () => setSelectedCurrency(null),
    });

    const handleCurrencyClick = (currency: Currency) => {
        setSelectedCurrency(currency);
    };


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
                        Add Currency
                    </Button>
                </Flex>
                <Row gutter={24}>
                    <Col span={5}>
                        <List
                            dataSource={Array.isArray(currencies) ? currencies : []}
                            renderItem={(currency, index) => {
                                const isEven = index % 2 === 0;
                                return (
                                    <Dropdown
                                        menu={{
                                            items: [
                                                {
                                                    key: 'delete',
                                                    label: 'Delete',
                                                    onClick: () => handleDelete(currency.id),
                                                },
                                            ],
                                        }}
                                        trigger={['contextMenu']}
                                        key={currency.id}
                                    >
                                        <List.Item
                                            onClick={() => handleCurrencyClick(currency)}
                                            style={{
                                                cursor: 'pointer',
                                                backgroundColor: isEven ? '#ffffff' : '#f5f5f5',
                                                paddingLeft: 8,
                                                paddingRight: 8
                                            }}
                                            className={
                                                selectedCurrency?.id === currency.id
                                                    ? 'ant-list-item-selected'
                                                    : ''
                                            }
                                        >
                                            <Typography.Text>
                                                {currency.name}
                                            </Typography.Text>
                                        </List.Item>
                                    </Dropdown>
                                );
                            }}
                        />
                    </Col>
                    <Col span={19} style={{ background: '#fff', minHeight: 400 }}>
                        {selectedCurrency ? (
                            <CoreShopCurrencyDetailPage key={selectedCurrency.id} id={selectedCurrency.id} onAfterSave={refetch} />
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
