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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PayumBundle\Exception;

use Payum\Core\Reply\ReplyInterface;
use Symfony\Component\HttpFoundation\Response;

if (false) {
    //This is just for the IDE
    class BaseCoreShopReplayToSymfonyResponseConverter {
        public function convert(ReplyInterface $reply): Response {
            throw new \RuntimeException('Not implemented');
        }
    }
}


if (class_exists('Payum\Bundle\PayumBundle\ReplyToSymfonyResponseConverter')) {
    \class_alias(\Payum\Bundle\PayumBundle\ReplyToSymfonyResponseConverter::class, 'CoreShop\Bundle\PayumBundle\Exception\BaseCoreShopReplayToSymfonyResponseConverter');
}
elseif (class_exists('Payum\Core\Bridge\Symfony\ReplyToSymfonyResponseConverter')) {
    \class_alias(\Payum\Core\Bridge\Symfony\ReplyToSymfonyResponseConverter::class, 'CoreShop\Bundle\PayumBundle\Exception\BaseCoreShopReplayToSymfonyResponseConverter');
}
else {
    throw new \RuntimeException('Cannot find Payum ReplyToSymfonyResponseConverter class');
}

class ReplyToSymfonyResponseConverter extends BaseCoreShopReplayToSymfonyResponseConverter
{
    public function convert(ReplyInterface $reply): Response
    {
        if ($reply instanceof ReplyException && null !== $reply->getPrevious()) {
            throw $reply->getPrevious();
        }

        return parent::convert($reply);
    }
}
