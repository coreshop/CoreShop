<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition;

final class OrderShippingStateConfigurationType extends AbstractWorkflowPlaceConfigurationType
{
    protected function getFieldName(): string
    {
        return 'orderShippingState';
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_notification_condition_order_shipping_state';
    }
}
