import { Header, Content } from '@pimcore/studio-ui-bundle/components';
import React, { useState } from 'react';
import { useFetch } from '../../hooks/useFetch';
import { Collapse, List, Typography, Tag, Spin, Button, Layout, Row, Col, Flex, Dropdown } from 'antd';
import { CoreShopStateDetailPage } from './coreshop-state-detail-page';
import { State } from './types';
import { useStateActions } from '../../hooks/useStateActions';
import { useEntityActions } from '../../hooks/useEntityActions';

const { Panel } = Collapse;

export const CoreShopStatesPage = (): React.JSX.Element => {
    const { data: states, loading, error, refetch } = useFetch<State[]>('/admin/coreshop/states/list');
    const { create: createState, remove: deleteState } = useStateActions();

    const [selectedState, setSelectedState] = useState<State | null>(null);

    const {
        handleDelete,
        openCreateModal,
    } = useEntityActions<State>({
        createEndpoint: '/admin/coreshop/states/add',
        deleteEndpoint: '/admin/coreshop/states/delete',
        createFn: createState,
        deleteFn: deleteState,
        refetch,
        getSelected: () => selectedState,
        clearSelected: () => setSelectedState(null),
    });

    const handleStateClick = (state: State) => {
        setSelectedState(state);
    };

    const groupedByCountry = (Array.isArray(states) ? states : []).reduce(
        (acc: Record<string, State[]>, state) => {
            if (!acc[state.countryName]) {
                acc[state.countryName] = [];
            }
            acc[state.countryName].push(state);
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
                        Create State
                    </Button>
                </Flex>
                <Row gutter={24}>
                    <Col span={5}>
                        <Collapse accordion>
                            {Object.entries(groupedByCountry).map(([countryName, states]) => (
                                <Panel header={`${countryName} (${states.length})`} key={countryName}>
                                    <List
                                        dataSource={states}
                                        renderItem={(state, index) => (
                                            <Dropdown
                                                menu={{
                                                    items: [
                                                        {
                                                            key: 'delete',
                                                            label: 'Delete',
                                                            onClick: () => handleDelete(state.id),
                                                        },
                                                    ],
                                                }}
                                                trigger={['contextMenu']}
                                                key={state.id}
                                            >
                                                <List.Item
                                                    onClick={() => handleStateClick(state)}
                                                    style={{
                                                        cursor: 'pointer',
                                                        backgroundColor: index % 2 === 0 ? '#ffffff' : '#f7f7f7',
                                                    }}
                                                    className={
                                                        selectedState?.id === state.id
                                                            ? 'ant-list-item-selected'
                                                            : ''
                                                    }
                                                >
                                                    <Typography.Text>{state.name}</Typography.Text>
                                                    {state.active ? (
                                                        <Tag color="green">Active</Tag>
                                                    ) : (
                                                        <Tag color="red">Inactive</Tag>
                                                    )}
                                                </List.Item>
                                            </Dropdown>
                                        )}
                                    />
                                </Panel>
                            ))}
                        </Collapse>
                    </Col>
                    <Col span={19} style={{ background: '#fff', minHeight: 400 }}>
                        {selectedState ? (
                            <CoreShopStateDetailPage
                                key={selectedState.id}
                                id={selectedState.id}
                                onAfterSave={refetch}
                            />
                        ) : (
                            <div style={{ padding: 16 }}>
                                <Typography.Text type="secondary">
                                    Select a state to view details.
                                </Typography.Text>
                            </div>
                        )}
                    </Col>
                </Row>
            </Content>
        </Layout>
    );
};
