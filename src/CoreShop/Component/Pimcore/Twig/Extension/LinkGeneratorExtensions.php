<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\Twig\Extension;

use CoreShop\Component\Pimcore\Templating\Helper\LinkGeneratorHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LinkGeneratorExtensions extends AbstractExtension
{
    /**
     * @var LinkGeneratorHelperInterface
     */
    private $helper;

    /**
     * @param LinkGeneratorHelperInterface $helper
     */
    public function __construct(LinkGeneratorHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('coreshop_url', [$this->helper, 'getUrl']),
            new TwigFunction('coreshop_path', [$this->helper, 'getPath']),
        ];
    }
}
