<?php

namespace CoreShop\Component\Resource\Transformer;

interface ItemKeyTransformerInterface
{
    /**
     * @param $string
     * @return mixed
     */
    public function transform($string);
}