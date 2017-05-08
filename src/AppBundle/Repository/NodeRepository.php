<?php

namespace AppBundle\Repository;

use AppBundle\Model\Node;
use Gaufrette\Filesystem;

/**
 * Class NodeRepository
 */
class NodeRepository 
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $id
     *
     * @return Node|null
     */
    public function find($id)
    {
        if ($this->filesystem->has($id)) {
            return $this->createFromJSON($this->filesystem->get($id)->getContent());
        }

        return null;
    }

    /**
     * @return array<Node>
     */
    public function findAll()
    {
        $listKeys = $this->filesystem->listKeys();
        $nodes = [];
        foreach ($listKeys['keys'] as $id) {
            $nodes[] = $this->createFromJSON($this->filesystem->get($id)->getContent());
        }

        return $nodes;
    }

    /**
     * @param Node $node
     */
    public function save(Node $node)
    {
        if (!$node->getId()) {
            $node->setId(uniqid());
        }

        $this->filesystem->write($node->getId(), json_encode(array(
            'id'   => $node->getId(),
            'name' => $node->getName(),
            'ip'   => $node->getIp()
        )));
    }

    /**
     * @param string $json
     *
     * @return Node
     */
    private function createFromJSON($json)
    {
        $data = json_decode($json, true);

        return new Node($data['id'], $data['name'], $data['id']);
    }
}
