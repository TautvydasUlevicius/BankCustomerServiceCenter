<?php
declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Service\CommissionCalculatorChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CommissionCalculatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        /*if (!$container->has(CommissionCalculatorChain::class)) {
            return;
        }

        $definition = $container->findDefinition(CommissionCalculatorChain::class);

        $taggedServices = $container->findTaggedServiceIds('app.commission_calculator');

        foreach ($taggedServices as $id => $tags) {
            var_dump($id);
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addServices',
                    [
                        new Reference($id),
                        $attributes['alias']
                    ]
                );
            }
        }*/

        $definition = $container->findDefinition(CommissionCalculatorChain::class);
        $references = [];

        foreach ($container->findTaggedServiceIds('app.commission_calculator') as $id => $tags) {
            $references[] = new Reference($id);
        }

        $definition->setArgument(0, $references);
    }
}
