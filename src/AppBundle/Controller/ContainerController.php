<?php

namespace AppBundle\Controller;

use AppBundle\Manager\DockerComposeManager;
use Docker\Docker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContainerController
 *
 * @Route("containers")
 */
class ContainerController extends Controller
{
    /**
     * @Route("/list", name="snide_docker_container_list")
     * @Template("container/list.html.twig")
     */
    public function listAction()
    {
        $docker = new Docker();
        $containers = $docker->getContainerManager()->findAll(array('all' => true));
        $manager = new DockerComposeManager($docker);
        return array(
            'containers'        => $containers,
            'composeContainers' => $manager->findAll()
        );
    }

    /**
     * @Route("/info/{id}", name="snide_docker_container_info")
     * @Template("container/info.html.twig")
     */
    public function infoAction($id)
    {
        $docker = new Docker();
        $container = $docker->getContainerManager()->find($id);

        return array('container' => $container);
    }

    /**
     * @Route("/stop/{id}", name="snide_docker_container_stop")
     *
     * @param string $id
     *
     * @return Response
     */
    public function stopAction($id)
    {
        $docker = new Docker();
        $docker->getContainerManager()->stop($id);

        return $this->redirectToRoute('snide_docker_container_list');
    }

    /**
     * @Route("/stop-all/{name}", name="snide_docker_container_stop_by_project_name")
     *
     * @param string $name
     *
     * @return Response
     */
    public function stopAllAction($name)
    {
        $docker = new Docker();
        $manager = new DockerComposeManager($docker);
        $manager->stop($name);

        return $this->redirectToRoute('snide_docker_container_list');
    }

    /**
     * @Route("/start/{id}", name="snide_docker_container_start")
     *
     * @param string $id
     *
     * @return Response
     */
    public function startAction($id)
    {
        $docker = new Docker();
        $docker->getContainerManager()->start($id);

        return $this->redirectToRoute('snide_docker_container_list');
    }

    /**
     * @Route("/restart/{id}", name="snide_docker_container_restart")
     * @Template("container/info.html.twig")
     *
     * @param string $id
     *
     * @return Response
     */
    public function restartAction($id)
    {
        $docker = new Docker();
        $docker->getContainerManager()->restart($id);

        return $this->redirectToRoute('snide_docker_container_list');
    }
}
