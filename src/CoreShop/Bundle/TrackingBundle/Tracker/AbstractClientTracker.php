<?php

namespace CoreShop\Bundle\TrackingBundle\Tracker;

use CoreShop\Bundle\TrackingBundle\TrackerInterface;
use Symfony\Component\Templating\EngineInterface;

abstract class AbstractClientTracker implements TrackerInterface
{
    /**
     * @var EngineInterface
     */
    protected $renderer;

    /**
     * @param EngineInterface $renderer
     */
    public function __construct(EngineInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param array $config
     * @return string
     */
    public function track($config)
    {
        $viewName = $config['viewName'];
        $data = $config['data'];

        $class = get_class($this);
        $class = explode('\\', $class);
        $class = array_pop($class);
        $class = strtolower(preg_replace('/(?<=[a-z])([A-Z]+)/', "-$1", $class));
        $class = strtolower($class);

        $viewName = sprintf('CoreShopTrackingBundle:Tracking/%s:%s.html.twig', $class, $viewName);

        return $this->renderer->render($viewName, $data);
    }

    /**
     * Remove null values from an object, keep protected keys in any case
     *
     * @param $data
     * @param array $protectedKeys
     * @return array
     */
    protected function filterNullValues($data, $protectedKeys = [])
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