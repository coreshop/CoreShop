import React from 'react';
import { Button } from 'antd';
import { useWidgetManager } from 'pimcore-studio-ui';

export const MyFirstTabComponent = (): React.JSX.Element => {
    const widgetManager = useWidgetManager();

    function onClick(): void {
        widgetManager.openBottomWidget({
            name: 'My first widget',
            component: 'my-first-widget',
        });
    }

    return (
        <div>
            <h1>My First Tab</h1>
            <p>This is a simple tab component.</p>

            <Button type="primary" onClick={onClick}>Open up my first widget</Button>
        </div>
    );
}