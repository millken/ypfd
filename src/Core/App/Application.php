<?php

declare (strict_types = 1);

namespace Ypfd\Core\App;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Yaml\Yaml;

class Application
{

    protected $booted = false;
    protected $root;
    protected $classLoader;
    private $app = null;
    protected $workers;
    protected $routers = [];
    protected static $instances = null;
    public function __construct(array $services)
    {
        $this->root = static::getApplicationRoot();
        $this->classLoader = new ClassLoader();
        $this->app = new \Ypf\Application($services);
    }

    public static function getApplicationRoot()
    {
        return substr(__DIR__, 0, -strlen(__NAMESPACE__));
    }

    protected function initializeContainer()
    {

        $this->initializeServiceProviders();

        if (!empty($this->serviceYamls)) {
            foreach ($this->serviceYamls as $module => $services) {
                if (!empty($services['services'])) {
                    foreach ($services['services'] as $name => $service) {
                        if (class_exists($service['class'])) {
                            if (isset($service['arguments'])) {
                                $container->share($service['class'])->withArguments($service['arguments']);
                            } else {
                                $container->share($service['class']);
                            }
                        }
                    }
                }

                if (!empty($services['providers'])) {
                    foreach ($services['providers'] as $name => $provider) {
                        if (class_exists($provider['class'])) {
                            $container->addServiceProvider($provider['class']);
                        }
                    }
                }

                if (!empty($services['commands'])) {
                    foreach ($services['commands'] as $name => $command) {
                        if (class_exists($command['class'])) {
                            $this->commands[$name] = $command['class'];
                        }
                    }
                    $container->add('commands', $this->commands);
                }
            }
        }

    }

    protected function initializeModules()
    {
        $it = new \DirectoryIterator($this->root . '/module');
        foreach ($it as $fi) {
            if ($fi->isFile() || $fi->isDot()) {
                continue;
            }

            $router_filename = $fi->getPath() . "/" . $fi->getFilename() . "/router.yml";
            if (file_exists($router_filename)) {
                $this->routers[$fi->getFilename()] = Yaml::parseFile($router_filename);
            }
            // $service_filename = dirname($filename['pathname']) . "/$module.services.yml";
            // if (file_exists($service_filename) || file_exists($this->root . '/' . $service_filename)) {
            //     $this->serviceYamls[$module] = Yaml::decode(file_get_contents($this->root . '/' . $service_filename));
            // }

        }
    }

    protected function initializeServiceProviders()
    {

        // // Load each module's serviceProvider class.
        // foreach ($this->moduleList as $module => $filename) {
        //   $service_filename = dirname($filename['pathname']) . "/$module.services.yml";
        //   if (file_exists($service_filename) || file_exists($this->root.'/'.$service_filename)) {
        //     $this->serviceYamls[$module] = Yaml::decode(file_get_contents($this->root.'/'.$service_filename));
        //   }
        // }
    }

    protected function buildRouters()
    {
        foreach ($this->routers as $module => $route) {
            $router->map($route['method'], $route['pattern'], $route['controller']);
        }
    }

    public function boot()
    {
        if (!$this->booted) {

            $this->initializeModules();

            // Initialize the container.
            $this->initializeContainer();

            // // Initialize all module list.
            // $this->initializeModuleList();

            // // Initialize all plugin list.
            // $this->initializePluginList();

            // Initialize all routes.
            $this->buildRouters();

            // // set App service.
            // $this->container->add('App', $this);

            $this->booted = true;
        }

        return $this;
    }

    public function run()
    {
        if (!$this->booted) {
            $this->boot();
        }
        $this->app->run();
    }
}
