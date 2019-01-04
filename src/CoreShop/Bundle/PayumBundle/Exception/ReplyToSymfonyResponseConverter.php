<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PayumBundle\Exception;

use Payum\Core\Bridge\Symfony\ReplyToSymfonyResponseConverter as BaseReplyToSymfonyResponseConverter;
use Payum\Core\Reply\ReplyInterface;

class ReplyToSymfonyResponseConverter extends BaseReplyToSymfonyResponseConverter
{
    /**
     * {@inheritdoc}
     */
    public function convert(ReplyInterface $reply)
    {
        if ($reply instanceof ReplyException) {
            throw $reply->getPrevious();
        }

        return parent::convert($reply);
    }
}
