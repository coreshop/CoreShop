<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Bundle\CoreBundle\Templating\Helper\AddressAllocatorHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

final class AddressAllocatorExtension extends AbstractExtension
{
    /**
     * @var AddressAllocatorHelperInterface
     */
    private $helper;

    /**
     * @param AddressAllocatorHelperInterface $helper
     */
    public function __construct(AddressAllocatorHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function getTests()
    {
        return [
            new TwigTest('coreshop_address_owner_of', [$this->helper, 'isOwnerOfAddress'])
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('coreshop_allocate_valid_addresses', [$this->helper, 'allocateAddresses']),
        ];
    }
}
