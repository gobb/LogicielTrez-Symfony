<?php
namespace Trez\LogicielTrezBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Trez\LogicielTrezBundle\Entity\Config;

class LoadConfigData implements FixtureInterface
{
    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager)
    {
        $config = new Config();
        $config->setCle('currency');
        $config->setValeur('€');

        $manager->persist($config);
        $manager->flush();

        $config2 = new Config();
        $config2->setCle('currentBudget');
        $config2->setValeur('');

        $manager->persist($config2);
        $manager->flush();
    }
}