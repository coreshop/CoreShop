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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

trait TranslatableTrait
{
    /**
     * @var ArrayCollection|PersistentCollection|TranslationInterface[]
     * @psalm-var Collection
     */
    protected $translations;

    /**
     * @var array|TranslationInterface[]
     */
    protected array $translationsCache = [];
    protected ?string $currentLocale = null;

    /**
     * Cache current translation. Useful in Doctrine 2.4+.
     */
    protected ?TranslationInterface $currentTranslation = null;
    protected ?string $fallbackLocale = null;

    public function __construct()
    {
        $this->initializeTranslationCollection();
    }

    protected function initializeTranslationCollection()
    {
        $this->translations = new ArrayCollection();
    }

    public function getTranslation(string $locale = null, bool $useFallbackTranslation = true): TranslationInterface
    {
        $locale = $locale ?: $this->currentLocale;
        if (null === $locale) {
            throw new \RuntimeException('No locale has been set and current locale is undefined.');
        }

        if (isset($this->translationsCache[$locale])) {
            return $this->translationsCache[$locale];
        }

        $translation = $this->translations->get($locale);
        if (null !== $translation) {
            $this->translationsCache[$locale] = $translation;

            return $translation;
        }

        if ($useFallbackTranslation) {
            $fallbackTranslation = $this->translations->get($this->fallbackLocale);
            if (null !== $fallbackTranslation) {
                $this->translationsCache[$this->fallbackLocale] = $fallbackTranslation;

                return $fallbackTranslation;
            }
        }

        $translation = $this->createTranslation();
        $translation->setLocale($locale);

        $this->addTranslation($translation);

        $this->translationsCache[$locale] = $translation;

        return $translation;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function hasTranslation(TranslationInterface $translation): bool
    {
        return isset($this->translationsCache[$translation->getLocale()]) || $this->translations->containsKey($translation->getLocale());
    }

    public function addTranslation(TranslationInterface $translation): void
    {
        if (!$this->hasTranslation($translation)) {
            $this->translationsCache[$translation->getLocale()] = $translation;

            $this->translations->set($translation->getLocale(), $translation);
            $translation->setTranslatable($this);
        }
    }

    public function removeTranslation(TranslationInterface $translation): void
    {
        if ($this->translations->removeElement($translation)) {
            unset($this->translationsCache[$translation->getLocale()]);

            $translation->setTranslatable(null);
        }
    }

    public function setCurrentLocale(string $locale): void
    {
        $this->currentLocale = $locale;
    }

    public function setFallbackLocale(string  $locale): void
    {
        $this->fallbackLocale = $locale;
    }

    /**
     * Create resource translation model.
     *
     * @return TranslationInterface
     */
    abstract protected function createTranslation(): TranslationInterface;
}
