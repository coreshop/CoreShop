<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\InventoryBundle\Validator\Constraints;

use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class InStockValidator extends ConstraintValidator
{
    /**
     * @var AvailabilityCheckerInterface
     */
    private $availabilityChecker;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @param AvailabilityCheckerInterface $availabilityChecker
     */
    public function __construct(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->availabilityChecker = $availabilityChecker;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var InStock $constraint */
        Assert::isInstanceOf($constraint, InStock::class);

        $stockable = $this->accessor->getValue($value, $constraint->stockablePath);
        if (null === $stockable) {
            return;
        }

        if (!$stockable instanceof StockableInterface) {
            return;
        }

        $quantity = $this->accessor->getValue($value, $constraint->quantityPath);
        if (null === $quantity) {
            return;
        }

        if (!$this->availabilityChecker->isStockSufficient($stockable, $quantity)) {
            $this->context->addViolation(
                $constraint->message,
                ['%stockable%' => $stockable->getInventoryName()]
            );
        }
    }
}
