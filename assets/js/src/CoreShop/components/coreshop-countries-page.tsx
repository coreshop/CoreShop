import { Content, Header } from 'pimcore-studio-ui/components';
import React from 'react';
import { useFetch } from '../hooks/useFetch';
import {Collapse, List, Typography, Tag, Spin} from 'antd';
const { Panel } = Collapse;
import { useWidgetManager } from 'pimcore-studio-ui/modules/widget-manager';


type Country = {
    id: number;
    name: string;
    isoCode: string;
    zoneName: string;
    active: boolean;
};

export const CoreShopCountriesPage = (): React.JSX.Element => {
    const widgetManager = useWidgetManager();
    const { data: countries, loading, error } = useFetch<Country[]>('/admin/coreshop/countries/list?_dc=1747397572196&page=1&start=0&limit=25');

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

    return (
        <Content padded>
            <Header title='Regions' />
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