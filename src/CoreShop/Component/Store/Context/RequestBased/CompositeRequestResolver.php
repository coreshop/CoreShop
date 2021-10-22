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

declare(strict_types=1);

namespace CoreShop\Component\Store\Context\RequestBased;

use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;
use Laminas\Stdlib\PriorityQueue;
use Symfony\Component\HttpFoundation\Request;

final class CompositeRequestResolver implements RequestResolverInterface
{
    private PriorityQueue $requestResolvers;

    public function __construct()
    {
        $this->requestResolvers = new PriorityQueue();
    }

    public function addResolver(RequestResolverInterface $requestResolver, int $priority = 0): void
    {
        $this->requestResolvers->insert($requestResolver, $priority);
    }

    public function findStore(Request $request): ?StoreInterface
    {
        foreach ($this->requestResolvers as $requestResolver) {
            try {
                return $requestResolver->findStore($request);
            } catch (StoreNotFoundException) {
                //Ignore and continue
            }
        }

        throw new StoreNotFoundException();
    }
}
