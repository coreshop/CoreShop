/**
 * CoreShop StoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */
import React from 'react';
import { container } from '@pimcore/studio-ui-bundle';
import { DynamicForm } from '@coreshop/studio-form/src/form-builder';
export const StoreForm = ({ data, onChange }) => {
    const builder = container.get('CoreShop/Store/Store/FormBuilder');
    const config = React.useMemo(() => builder.build({ data }), [builder, data]);
    return (React.createElement("div", { style: { padding: 12 } },
        React.createElement(DynamicForm, { config: config, data: data, onChange: onChange })));
};
