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
        $definition = $container->findDefinition(CommissionCalculatorChain::class);
        $references = [];

        foreach ($container->findTaggedServiceIds('app.commission_calculator') as $id => $tags) {
            $references[] = new Reference($id);
        }

        $definition->setArgument(0, $references);
    }
}
