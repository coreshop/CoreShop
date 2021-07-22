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

namespace CoreShop\Bundle\ResourceBundle\Form\DataTransformer;


use Pimcore\Model\Asset;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PimcoreAssetDataTransformer implements DataTransformerInterface
{

    public function transform($value)
    {
        if ($value instanceof Asset) {
            return $value->getId();
        }

        return null;
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        $asset = Asset::getById($value);

        if (null === $asset) {
            throw new TransformationFailedException(sprintf(
                "An asset with the ID %d does not exist.",
                $value
            ));
        }

        return $asset;
    }
}
