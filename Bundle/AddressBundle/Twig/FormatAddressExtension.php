<?php

namespace CoreShop\Bundle\AddressBundle\Twig;

use CoreShop\Bundle\AddressBundle\Templating\Helper\FormatAddressHelperInterface;

final class FormatAddressExtension extends \Twig_Extension
{
    /**
     * @var FormatAddressHelperInterface
     */
    private $helper;

    /**
     * @param FormatAddressHelperInterface $helper
     */
    public function __construct(FormatAddressHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('coreshop_format_address', [$this->helper, 'formatAddress'], array('is_safe' => array('html'))),
        ];
    }
}
