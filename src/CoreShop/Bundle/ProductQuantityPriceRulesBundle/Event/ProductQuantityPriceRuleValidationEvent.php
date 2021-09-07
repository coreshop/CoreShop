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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Event;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ProductQuantityPriceRuleValidationEvent extends GenericEvent
{
    private Concrete $object;
    private array $data;

    public function __construct(Concrete $object, array $data)
    {
        parent::__construct($object);

        $this->object = $object;
        $this->data = $data;
    }

    public function getObject(): Concrete
    {
        return $this->object;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
