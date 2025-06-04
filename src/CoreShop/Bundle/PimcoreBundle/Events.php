<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PimcoreBundle;

final class Events
{
    /**
     * Fired before a order mail will be sent.
     */
    public const PRE_MAIL_SEND = 'coreshop.mail.pre_send';

    /**
     * Fired after a order mail has been sent.
     */
    public const POST_MAIL_SEND = 'coreshop.mail.post_send';
}
