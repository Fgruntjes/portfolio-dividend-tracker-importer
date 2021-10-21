<?php
declare(strict_types = 1);

namespace App\DependencyInjection\Compiler;

use App\Broker\ExportConverter\ExportConverterRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BrokerExportConverterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ExportConverterRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(ExportConverterRegistry::class);

        $taggedServices = $container->findTaggedServiceIds('app.broker.export_converter');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
