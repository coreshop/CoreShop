import React from 'react';
import { IconButton } from '@pimcore/studio-ui-bundle/components'
import {useWidgetManager, WidgetManagerTabConfig} from '@pimcore/studio-ui-bundle/modules/widget-manager';


export const CoreShopButton = (): React.JSX.Element => {
    // SET MAIN TAB
    const widgetManager = useWidgetManager();
    const widgets = [
        {
            name: 'CoreShopMainPage',
            component: 'CoreShopMainPage',
            config: {
                icon: {
                    type: 'name',
                    value: 'coreshop-icon'
                }
            }
        }
    ]

    // GET MAIN TAB
    const selected = widgets.find(widget => widget.name === 'CoreShopMainPage');

    const handleClick = () => {
        if (selected) {
            widgetManager.openMainWidget(selected as WidgetManagerTabConfig);
        } else {
            console.warn('CoreShopMainPage widget not found.');
        }
    };

    return (
        <IconButton
            icon={{ value: 'coreshop-icon' }}
            onClick={handleClick}
            type="text"
        />
    );
}