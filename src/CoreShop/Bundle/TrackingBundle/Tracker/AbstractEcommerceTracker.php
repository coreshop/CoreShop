<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\TrackingBundle\Tracker;

use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractEcommerceTracker implements TrackerInterface
{
    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @var string
     */
    protected $templatePrefix;

    /**
     * @var string
     */
    protected $templateExtension;

    /**
     * @param EngineInterface $templatingEngine
     * @param array           $options
     */
    public function __construct(
        EngineInterface $templatingEngine,
        array $options = []
    ) {
        $this->templatingEngine = $templatingEngine;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->processOptions($resolver->resolve($options));
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
    
    /**
     * @param array $options
     */
    protected function processOptions(array $options)
    {
        $this->templatePrefix = $options['template_prefix'];
        $this->templateExtension = $options['template_extension'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
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
     * @return string
     */
    protected function getTemplatePath(string $name)
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
     * @return string
     */
    protected function renderTemplate(string $name, array $parameters)
    {
        return $this->templatingEngine->render(
            $this->getTemplatePath($name),
            $parameters
        );
    }

    /**
     * Remove null values from an object, keep protected keys in any case
     *
     * @param array $data
     * @param array $protectedKeys
     *
     * @return array
     */
    protected function filterNullValues(array $data, array $protectedKeys = [])
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
