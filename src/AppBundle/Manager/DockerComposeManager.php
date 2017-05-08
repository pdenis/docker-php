<?php

namespace AppBundle\Manager;

use Docker\API\Model\Container;
use Docker\Docker;

/**
 * Class DockerComposeManager
 */
class DockerComposeManager 
{
    /**
     * @var Docker
     */
    private $docker;

    /**
     * @param Docker $docker
     */
    public function __construct(Docker $docker)
    {
        $this->docker = $docker;
    }

    /**
     * @return array<Container>
     */
    public function findAll()
    {
        $containers = $this->docker->getContainerManager()->findAll();
        $composeContainers = [];

        foreach ($containers as $container) {
            $names = $container->getNames();
            $name = array_pop($names);
            $projectName = str_replace('/', '', substr($name, 0, strpos($name, '_')));
            if ($projectName) {
                $composeContainers[$projectName][] = $container;
            }
        }

        // Only return compose containers with more than one container
        return array_filter($composeContainers, function($containers) {
            return count($containers) > 1;
        });
    }

    /**
     * @return array<Container>
     */
    public function findByName($projectName)
    {
        $containers = $this->docker->getContainerManager()->findAll();
        $composeContainers = [];

        foreach ($containers as $container) {
            $names = $container->getNames();
            $name = array_pop($names);
            $retrievedProjectName = str_replace('/', '', substr($name, 0, strpos($name, '_')));
            if ($projectName === $retrievedProjectName) {
                $composeContainers[$projectName][] = $container;
            }
        }

        // Only return compose containers with more than one container
        return array_pop($composeContainers);
    }

    /**
     * @param $projectName
     */
    public function stop($projectName)
    {
        $containers = $this->findByName($projectName);

        if (!$containers) {
            return;
        }

        /** @var Container $container */
        foreach ($containers as $container) {
            $this->docker->getContainerManager()->stop($container->getId());
        }
    }
}
