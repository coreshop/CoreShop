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

namespace CoreShop\Bundle\PimcoreBundle;

final class Events
{
    /**
     * Fired before a order mail will be sent.
     */
    public const string PRE_MAIL_SEND = 'coreshop.mail.pre_send';

    /**
     * Fired after a order mail has been sent.
     */
    public const string POST_MAIL_SEND = 'coreshop.mail.post_send';
}
