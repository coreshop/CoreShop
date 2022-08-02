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

namespace CoreShop\Component\Resource\Model;

use Doctrine\Common\Collections\Collection;

interface TranslatableInterface
{
    /**
     * @return Collection|TranslatableInterface[]
     * @psalm-return Collection
     */
    public function getTranslations(): Collection;

    public function getTranslation(string $locale = null): TranslationInterface;

    public function hasTranslation(TranslationInterface $translation): bool;

    public function addTranslation(TranslationInterface $translation): void;

    public function removeTranslation(TranslationInterface $translation): void;

    public function setCurrentLocale(string $locale): void;

    public function setFallbackLocale(string $locale): void;
}
