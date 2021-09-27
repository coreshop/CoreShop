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

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Resource\Model\ToggleableTrait;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;
use Pimcore\Model\Asset\Image;

abstract class Product extends AbstractPimcoreModel implements ProductInterface
{
    use ToggleableTrait;

    public function getImage(): ?Image
    {
        if (count($this->getImages()) > 0) {
            return $this->getImages()[0];
        }

        return null;
    }

    public function hasUnitDefinitions(): bool
    {
        return $this->getUnitDefinitions() instanceof ProductUnitDefinitionsInterface && $this->getUnitDefinitions()->getUnitDefinitions()->count() > 0;
    }

    public function hasDefaultUnitDefinition(): bool
    {
        return $this->hasUnitDefinitions() && $this->getUnitDefinitions()->getDefaultUnitDefinition() instanceof ProductUnitDefinitionInterface;
    }

    public function hasAdditionalUnitDefinitions(): bool
    {
        return $this->hasUnitDefinitions() && $this->getUnitDefinitions()->getAdditionalUnitDefinitions()->count() > 0;
    }

    public function getNameForSlug(string $language = null): ?string
    {
        return $this->getName($language);
    }
}
