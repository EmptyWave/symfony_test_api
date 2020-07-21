<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class MyEntityManager
{

    /**
     * @return EntityManager
     * @throws ORMException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    static public function get()
    {
        $paths = array(__DIR__ . "/src/Entity");
        $isDevMode = true;

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);

        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        $dbParams = array(
            'driver' => 'pdo_mysql',
            'user' => 'root',
            'password' => '',
            'dbname' => 'symfony_tests',
        );

        return EntityManager::create($dbParams, $config);
    }
}