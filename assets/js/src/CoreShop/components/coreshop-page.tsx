import { Content, Header } from 'pimcore-studio-ui/components';
import React from 'react';

export const CoreShopMainPage = (): React.JSX.Element => {
    return (
        <Content padded>
            <Header title='Another example widget' />

            <div>
                This is another example widget.
            </div>
        </Content>
    )
}