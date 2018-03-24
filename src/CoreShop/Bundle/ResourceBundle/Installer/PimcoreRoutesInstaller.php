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

namespace CoreShop\Bundle\ResourceBundle\Installer;

use CoreShop\Bundle\ResourceBundle\Installer\Configuration\RouteConfiguration;
use Pimcore\Model\Staticroute;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class PimcoreRoutesInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**<
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, $applicationName = null, $options = [])
    {
        $parameter = $applicationName ? sprintf('%s.pimcore.admin.install.routes', $applicationName) : 'coreshop.all.pimcore.admin.install.routes';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $routeFilesToInstall = $this->kernel->getContainer()->getParameter($parameter);
            $routesToInstall = [];

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            $processor = new Processor();
            $configurationDefinition = new RouteConfiguration();

            foreach ($routeFilesToInstall as $file) {
                $file = $this->kernel->locateResource($file);

                if (file_exists($file)) {
                    $routes = Yaml::parse(file_get_contents($file));
                    $routes = $processor->processConfiguration($configurationDefinition, ['staticroutes' => $routes]);
                    $routes = $routes['routes'];

                    foreach ($routes as $name => $routeData) {
                        if (isset($options['allowed']) && is_array($options['allowed']) && !in_array($name, $options['allowed'])) {
                            continue;
                        }
                        $routesToInstall[$name] = $routeData;
                    }
                }
            }

            $progress->start(count($routesToInstall));

            foreach ($routesToInstall as $name => $routeData) {
                $progress->setMessage(sprintf('<error>Install Route %s</error>', $name));

                $this->installRoute($name, $routeData);

                $progress->advance();
            }

            $progress->finish();
        }
    }

    /**
     * Check if route is already installed
     *
     * @param $name
     * @param $properties
     * @return Staticroute
     */
    private function installRoute($name, $properties)
    {
        $route = new Staticroute();

        try {
            $route->getDao()->getByName($name, null);
        } catch (\Exception $e) {
            //Route does not exist, so we install it
            $route = Staticroute::create();
            $route->setName($name);
            $route->setPattern($properties['pattern']);
            $route->setReverse($properties['reverse']);
            $route->setModule($properties['module']);
            $route->setController($properties['controller']);
            $route->setAction($properties['action']);
            $route->setVariables($properties['variables']);
            $route->setPriority($properties['priority']);
            $route->save();
        }

        return $route;
    }
}