<?php

namespace Kematjaya\HiddenTypeBundle\Tests;

use Doctrine\Persistence\ManagerRegistry;
use Kematjaya\HiddenTypeBundle\HiddenTypeBundle;
use Kematjaya\HiddenTypeBundle\Type\HiddenDateTimeType;
use Kematjaya\HiddenTypeBundle\Type\HiddenEntityType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class HiddenTypeBundleTest extends TestCase
{
    public function testServicesAreRegistered(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition(ManagerRegistry::class, (new Definition(ManagerRegistry::class))->setSynthetic(true));
        $bundle = new HiddenTypeBundle();
        $bundle->build($container);
        $bundle->getContainerExtension()->load([], $container);

        foreach ([HiddenEntityType::class, HiddenDateTimeType::class] as $type) {
            $this->assertTrue($container->hasDefinition($type));
            $this->assertTrue($container->getDefinition($type)->isAutowired());
            $this->assertTrue($container->getDefinition($type)->isAutoconfigured());
        }
    }
}
