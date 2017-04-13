<?php

namespace CoreShop\Component\Core\Transformer;

use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;

class PimcoreKeyTransformer implements ItemKeyTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($string)
    {
        return \Pimcore\File::getValidFilename($string);
    }
}