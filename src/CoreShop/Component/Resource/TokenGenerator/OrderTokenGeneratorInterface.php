<?php

namespace CoreShop\Component\Resource\TokenGenerator;

interface OrderTokenGeneratorInterface
{
    public function generate(int $length);
}