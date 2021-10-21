<?php

namespace App;

use App\DependencyInjection\Compiler\BrokerExportConverterPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../../config/{packages}/*.yaml');
        $container->import('../../config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file(\dirname(__DIR__, 2).'/config/services.yaml')) {
            $container->import('../../config/services.yaml');
            $container->import('../../config/{services}_'.$this->environment.'.yaml');
        } else {
            $container->import('../../config/{services}.php');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../../config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('../../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__, 2).'/config/routes.yaml')) {
            $routes->import('../../config/routes.yaml');
        } else {
            $routes->import('../../config/{routes}.php');
        }
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new BrokerExportConverterPass());
    }
}
