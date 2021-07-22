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

namespace CoreShop\Bundle\TrackingBundle\Tracker;

use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

abstract class AbstractEcommerceTracker implements TrackerInterface
{
    protected bool $enabled = false;
    protected Environment $twig;
    protected ?string $templatePrefix = null;
    protected ?string $templateExtension = null;

    public function __construct(
        Environment $twig,
        array $options = []
    ) {
        $this->twig = $twig;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->processOptions($resolver->resolve($options));
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @param array $options
     */
    protected function processOptions(array $options): void
    {
        $this->templatePrefix = $options['template_prefix'];
        $this->templateExtension = $options['template_extension'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['template_prefix', 'template_extension']);
        $resolver->setDefaults(
            [
                'template_extension' => 'twig',
            ]
        );

        $resolver->setAllowedTypes('template_prefix', 'string');
        $resolver->setAllowedTypes('template_extension', 'string');
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getTemplatePath(string $name): string
    {
        return sprintf(
            '%s/%s.js.%s',
            $this->templatePrefix,
            $name,
            $this->templateExtension
        );
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     */
    protected function renderTemplate(string $name, array $parameters): string
    {
        return $this->twig->render(
            $this->getTemplatePath($name),
            $parameters
        );
    }

    /**
     * Remove null values from an object, keep protected keys in any case.
     *
     * @param array $data
     * @param array $protectedKeys
     *
     * @return array
     */
    protected function filterNullValues(array $data, array $protectedKeys = []): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $isProtected = in_array($key, $protectedKeys);
            if (null !== $value || $isProtected) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
