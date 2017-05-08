<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\NodeType;
use AppBundle\Model\Node;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class NodeController
 *
 * @Route("nodes")
 */
class NodeController extends Controller
{
    /**
     * @Route("/list", name="snide_docker_node_list")
     * @Template("node/list.html.twig")
     */
    public function listAction()
    {
        $nodes = $this->get('snide_docker.repository.node')->findAll();

        return array('nodes' => $nodes);
    }

    /**
     *
     * @Route("/new", name="snide_docker_node_new")
     * @Template("node/new.html.twig")
     *
     * @param Request $request
     *
     * @return array
     *
     */
    public function newAction(Request $request)
    {
        return $this->handleForm($request, new Node());
    }

    /**
     * @Route("/edit/{id}", name="snide_docker_node_edit")
     * @Template("node/edit.html.twig")
     *
     * @param Request $request
     * @param string  $id
     *
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        $node = $this->get('snide_docker.repository.node')->find($id);

        if (!$node) {
            throw new NotFoundHttpException(sprintf('Node %s does not exist', $id));
        }

        return $this->handleForm($request, $node);
    }

    /**
     * @param Request $request
     * @param Node    $node
     *
     * @return array|Response
     */
    public function handleForm(Request $request, Node $node)
    {
        $form = $this->createForm(
            NodeType::class,
            $node
        );
        // Handle form
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->get('snide_docker.repository.node')->save($form->getNormData());

            return $this->redirectToRoute('snide_docker_node_list');
        }

        return array(
            'form' => $form->createView(),
        );
    }
}
