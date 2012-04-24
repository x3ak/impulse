<?php
class Slys_Paginator_Adapter_Doctrine implements Zend_Paginator_Adapter_Interface
{
    /**
     * @var Doctrine_Query
     */
    protected $_dql;

    /**
     * @var int
     */
    protected $_rowsAmount;

    /**
     * @param Doctrine_Query $query
     * @param int $hydrationMode
     */
    public function __construct(Doctrine_Query $query, $hydrationMode = null)
    {
        $this->_dql = $query;

        if ($hydrationMode !== null) {
            $this->_dql->setHydrationMode($hydrationMode);
        }
    }

    /**
     * Get items
     *
     * @param int $offset
     * @param int $itemsPerPage
     * @return Doctrine_Collection
     */
    public function getItems($offset, $itemsPerPage)
    {
        if ($itemsPerPage !== null) {
            $this->_dql->limit($itemsPerPage);
        }
        if ($offset !== null) {
            $this->_dql->offset($offset);
        }

        return $this->_dql->execute();
    }

    /**
     * Count results
     *
     * @return int
     */
    public function count()
    {
        if ($this->_rowsAmount === null) {
            $this->_rowsAmount = $this->_dql->count();
        }

        return $this->_rowsAmount;
    }

    /**
     * Set the row count
     *
     * @param int $rowCount
     */
    public function setRowCount($rowsAmount)
    {
        $this->_rowsAmount = $rowsAmount;
        return $this;
    }
}