import { Content, Header, Icon } from 'pimcore-studio-ui/components';
import React from 'react';

export const CoreShopMainPage = (): React.JSX.Element => {
    return (
        <Content padded>
            <Header title='CoreShop'/>
            <Icon value={ 'coreshop-logo' }  options={{ width: 400, height: 104 }} />
        </Content>
    )
}