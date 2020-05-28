<?php


declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Form\DataTransformer;


use Pimcore\Model\Asset;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PimcoreAssetDataTransformer implements DataTransformerInterface
{

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if ($value instanceof Asset) {
            return $value->getId();
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
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
