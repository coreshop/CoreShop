import { Content, Header } from '@pimcore/studio-ui-bundle/components';
import React from 'react';
import { useFetch } from '../hooks/useFetch';
import {Collapse, List, Typography, Tag, Spin, Button} from 'antd';
const { Panel } = Collapse;
import { useWidgetManager } from '@pimcore/studio-ui-bundle/modules/widget-manager';
import { useFormModal } from '@pimcore/studio-ui-bundle/components';
import { useCountryActions } from '../hooks/useCountryActions';

type Country = {
    id: number;
    name: string;
    isoCode: string;
    zoneName: string;
    active: boolean;
};

export const CoreShopCountriesPage = (): React.JSX.Element => {
    const widgetManager = useWidgetManager();
    const { data: countries, loading, error } = useFetch<Country[]>('/admin/coreshop/countries/list');
    const { input } = useFormModal();
    const { createCountry } = useCountryActions();

    const handleCountryClick = (country: any) => {
        widgetManager.openMainWidget({
            name: 'CoreShopCountryDetailPage',
            component: 'CoreShopCountryDetailPage',
            config: {
                id: country.id,
                icon: {
                    type: 'name',
                    value: 'coreshop-icon'
                },
            },

        });
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

    const handleCountryCreation = (value: string) => {
        console.log(value);
        createCountry(value, '/admin/coreshop/countries/add').then(r => {});
    }

    const openNewCountry = () => {
        console.log("Creating new country...");
        input({
            title: 'new country',
            label: 'label country',
            rule: {
                required: true,
                message: 'Please enter a country name'
            },
            okText: 'created',
            onOk: async (value: string) => {
                handleCountryCreation(value);
            }
        })
    };

    return (
        <Content padded>
            <Header title='Regions' />
            <Button type={'primary'}  onClick={openNewCountry} >Create Country</Button>
            <Collapse accordion>
                {Object.entries(groupedByZone).map(([zoneName, countries]) => (
                    <Panel header={`${zoneName} (${countries.length})`} key={zoneName}>
                        <List
                            dataSource={countries}
                            renderItem={(country) => (
                                <List.Item
                                    key={country.id}
                                    onClick={() => handleCountryClick(country)}
                                    style={{ cursor: 'pointer' }}
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
                            )}
                        />
                    </Panel>
                ))}
            </Collapse>
        </Content>
    )
}