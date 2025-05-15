import React from 'react';
import { IconButton } from 'pimcore-studio-ui/components'
import { useWidgetManager } from 'pimcore-studio-ui/modules/widget-manager';


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
    const selected = widgets.find((widget) => widget.name === 'CoreShopMainPage');

    return (
        <IconButton
            icon={ { value: 'coreshop-icon' } }
            onClick={ () => { widgetManager.openMainWidget(selected) } }
            type={ 'text' }
        />
)
}