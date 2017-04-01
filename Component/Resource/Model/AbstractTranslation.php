<?php

namespace CoreShop\Component\Resource\Model;

class AbstractTranslation implements TranslationInterface
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var TranslatableInterface
     */
    protected $translatable;

    /**
     * {@inheritdoc}
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslatable(TranslatableInterface $translatable = null)
    {
        if ($translatable === $this->translatable) {
            return;
        }

        $previousTranslatable = $this->translatable;
        $this->translatable = $translatable;

        if (null !== $previousTranslatable) {
            $previousTranslatable->removeTranslation($this);
        }

        if (null !== $translatable) {
            $translatable->addTranslation($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
