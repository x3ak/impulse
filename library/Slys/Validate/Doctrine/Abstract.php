<?php

abstract class Slys_Validate_Doctrine_Abstract extends Zend_Validate_Abstract
{

    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching '%value%' was found",
        self::ERROR_RECORD_FOUND    => "A record matching '%value%' was found",
    );

    /**
     *
     * @var Doctrine_Record
     */
    protected $_mapper;
    protected $_field;
    protected $_value;

    public function __construct(Doctrine_Record $mapper, $field)
    {
        $this->_mapper = $mapper;
        $this->_field = $field;
        $this->setConnection($mapper->getTable()->getConnection());
    }


    /**
     * Returns the set exclude clause
     *
     * @return string|array
     */
    public function getExclude()
    {
        return $this->_exclude;
    }

    /**
     * Sets a new exclude clause
     *
     * @param string|array $exclude
     * @return Slys_Validate_Doctrine_Abstract
     */
    public function setExclude($exclude)
    {
        $this->_exclude = $exclude;
        return $this;
    }

    /**
     * Returns the set field
     *
     * @return string|array
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Sets a new field
     *
     * @param string $field
     * @return Slys_Validate_Doctrine_Abstract
     */
    public function setField($field)
    {
        $this->_field = (string) $field;
        return $this;
    }


    /**
     * Returns the set schema
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Sets a new schema
     *
     * @param string $schema
     * @return Slys_Validate_Doctrine_Abstract
     */
    public function setConnection($schema)
    {
        $this->_connection = $schema;
        return $this;
    }

    /**
     * Sets the select object to be used by the validator
     *
     * @param Doctrine_Query $select
     * @return Slys_Validate_Doctrine_Abstract
     */
    public function setQuery($query)
    {
        if (!$query instanceof Doctrine_Query) {
            throw new Zend_Validate_Exception('Query option must be a valid ' .
                                              'Doctrine_Query object');
        }
        $this->_query = $query;
        return $this;
    }

    /**
     * Gets the select object to be used by the validator.
     * If no select object was supplied to the constructor,
     * then it will auto-generate one from the given table,
     * schema, field, and adapter options.
     *
     * @return Doctrine_Query
     */
    public function getQuery()
    {
        if(empty($this->_query)) {
            $table = $this->_mapper->getTable();
            $this->_query = $table->createQuery()
                                  ->addWhere("{$this->_field} = ?", array($this->_value));
        }
        return $this->_query;
    }

    /**
     * Run query and returns matches, or null if no matches are found.
     *
     * @param  String $value
     * @return Array when matches are found.
     */
    protected function _query($value)
    {
        $query = $this->getQuery();
        /**
         * Run query
         */
        $result = $query->execute();
        $count = $result->count();

        if($count > 0) {
            return true;
        } else {
            return false;
        }

    }
}
