<?php
namespace PHPSegmentTree;
require_once __DIR__ . '/Node.php';
abstract class AbstractSegmentTree extends Node {
    /**
     * Get all existing keys.
     * Some keys maybe deleted or override by others.
     *
     * @return array
     */
    public function getAllExistingKeys(): array {
        $ret = [];
        foreach ($this->data as $key=>&$tmp)
            $ret[] = $key;
        return $key;
    }
    protected $use_double_equal_sign = true;
    protected function isEqual($x, $y): bool {
        if ($x instanceof MixtureFlag || $y instanceof MixtureFlag)
            return false;
        if ($this->use_double_equal_sign)
            return $x == $y;
        else
            return $x === $y;
    }
    /**
     * fill $key of the whole tree by $value
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function fill(string $key, $value): self {
        $this->_setValue($this->l, $this->r, $key, $value);
        return $this;
    }
    /**
     * Clear all values and child nodes.
     * Make it a new tree.
     *
     * @return self
     */
    public function clear(): self {
        $this->data = new \stdClass();
        $this->node_l = null;
        $this->node_r = null;
        return $this;
    }
    /**
     * Remove all redundant nodes in the tree to reduce memory cost.
     *
     * @return self
     */
    public function optimize(): self {
        if (! $this->hasChildNode())
            return $this;
        $queue = new \SplQueue();
        $queue->enqueue($this);
        do {
            /**
             *
             * @var Node $node
             */
            $node = $queue->dequeue();
            foreach ($node->data as $value)
                if ($value instanceof MixtureFlag) {
                    if ($node->node_l->hasChildNode())
                        $queue->enqueue($node->node_l);
                    if ($node->node_r->hasChildNode())
                        $queue->enqueue($node->node_r);
                    continue 2;
                }

            $node->node_l = null;
            $node->node_r = null;
        } while ( ! $queue->isEmpty() );
        return $this;
    }
    /**
     * Get number of nodes in the tree
     *
     * @return int
     */
    public function getNodesCount(): int {
        $count = 0;
        $queue = new \SplQueue();
        $queue->enqueue($this);
        do {
            ++ $count;
            /**
             *
             * @var Node $node
             */
            $node = $queue->dequeue();
            if ($node->hasChildNode()) {
                $queue->enqueue($node->node_l);
                $queue->enqueue($node->node_r);
            }
        } while ( ! $queue->isEmpty() );
        return $count;
    }
}