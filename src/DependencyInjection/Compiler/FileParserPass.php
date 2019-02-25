<?php
declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\DependencyInjection\FileParserChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FileParserPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(FileParserChain::class)) {
            return;
        }

        $definition = $container->findDefinition(FileParserChain::class);

        $taggedServices = $container->findTaggedServiceIds('app.file_parser');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addFileParser', [
                    new Reference($id),
                    $attributes["alias"]
                ]);
            }
        }
    }
}
