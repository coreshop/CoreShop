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

namespace CoreShop\Component\Pimcore\Slug;

use CoreShop\Component\Pimcore\Exception\SlugNotPossibleException;
use Symfony\Component\String\Slugger\SluggerInterface;

class SluggableSlugger implements SluggableSluggerInterface
{
    public function __construct(protected SluggerInterface $slugger)
    {
    }

    public function slug(SluggableInterface $sluggable, string $locale, string $suffix = null): string
    {
        $name = $sluggable->getNameForSlug($locale);

        if (!$name) {
            throw new SlugNotPossibleException('name is empty');
        }

        if ($suffix !== null) {
            return sprintf(
                '/%s/%s-%s',
                $locale,
                strtolower($this->slugger->slug($name, '-', $locale)->toString()),
                $suffix
            );
        }

        return sprintf(
            '/%s/%s',
            $locale,
            strtolower($this->slugger->slug($name, '-', $locale)->toString())
        );
    }
}
