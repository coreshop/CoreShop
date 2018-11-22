<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\AddressBundle\Templating\Helper;

use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use Symfony\Component\Templating\Helper\Helper;

class FormatAddressHelper extends Helper implements FormatAddressHelperInterface
{
    /**
     * @var AddressFormatterInterface
     */
    private $addressFormatter;

    /**
     * @param AddressFormatterInterface $addressFormatter
     */
    public function __construct(AddressFormatterInterface $addressFormatter)
    {
        $this->addressFormatter = $addressFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAddress($address, $asHtml = true)
    {
        return $this->addressFormatter->formatAddress($address, $asHtml);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_format_address';
    }
}
