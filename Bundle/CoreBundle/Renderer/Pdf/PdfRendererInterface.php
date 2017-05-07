<?php

namespace CoreShop\Bundle\CoreBundle\Renderer\Pdf;

interface PdfRendererInterface
{
    /**
     * @param $string
     * @param string $header
     * @param string $footer
     * @param array $config
     * @return string
     */
    public function fromString($string, $header = '', $footer = '', $config = []);
}