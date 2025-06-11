import { Header, Content } from '@pimcore/studio-ui-bundle/components';
import React, { useState } from 'react';
import { useFetch } from '../hooks/useFetch';
import { Collapse, List, Typography, Tag, Spin, Button, Layout, Row, Col, Flex, Dropdown, Menu } from 'antd';
import { useFormModal } from '@pimcore/studio-ui-bundle/components';
import { useStateActions } from '../hooks/useStateActions';
import { CoreShopStateDetailPage } from './coreshop-state-detail-page';

const { Panel } = Collapse;

type State = {
    id: number;
    name: string;
    countryName: string;
    active: boolean;
};

export const CoreShopStatesPage = (): React.JSX.Element => {
    const { data: states, loading, error, refetch } = useFetch<State[]>('/admin/coreshop/states/list');
    const { input } = useFormModal();
    const { createState, deleteState} = useStateActions();

    const [selectedState, setSelectedState] = useState<State | null>(null);

    const handleStateClick = (state: State) => {
        setSelectedState(state);
    };

    const groupedByZone = (Array.isArray(states) ? states : []).reduce(
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

    const handleStateCreation = async (value: string) => {
        await createState(value, '/admin/coreshop/states/add');
        refetch();
    };

    const handleDelete = async (id: number) => {
        await deleteState(id, '/admin/coreshop/states/delete');
        if (selectedState?.id === id) {
            setSelectedState(null);
        }
        refetch();
    };

    const openNewState = () => {
        input({
            title: 'New State',
            label: 'State Name',
            rule: {
                required: true,
                message: 'Please enter a state name'
            },
            okText: 'Create',
            onOk: async (value: string) => {
                await handleStateCreation(value);
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
                        onClick={openNewState}
                        style={{ display: 'inline-block', marginBottom: 16 }}
                    >
                        Create State
                    </Button>
                </Flex>
                <Row gutter={24}>
                    <Col span={5}>
                        <Collapse accordion>
                            {Object.entries(groupedByZone).map(([zoneName, states]) => (
                                <Panel header={`${zoneName} (${states.length})`} key={zoneName}>
                                    <List
                                        dataSource={states}
                                        renderItem={(state) => {
                                            const menu = (
                                                <Menu>
                                                    <Menu.Item
                                                        key="delete"
                                                        onClick={() => handleDelete(state.id)}
                                                    >
                                                        Delete
                                                    </Menu.Item>
                                                </Menu>
                                            );

                                            return (
                                                <Dropdown
                                                    overlay={menu}
                                                    trigger={['contextMenu']}
                                                    key={state.id}
                                                >
                                                    <List.Item
                                                        onClick={() => handleStateClick(state)}
                                                        style={{ cursor: 'pointer' }}
                                                        className={
                                                            selectedState?.id === state.id
                                                                ? 'ant-list-item-selected'
                                                                : ''
                                                        }
                                                    >
                                                        <Typography.Text>
                                                            {state.name}
                                                        </Typography.Text>
                                                        {state.active ? (
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
                        {selectedState ? (
                            <CoreShopStateDetailPage key={selectedState.id} id={selectedState.id} onAfterSave={refetch} />
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
