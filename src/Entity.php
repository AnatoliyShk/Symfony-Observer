<?php


namespace App;


abstract class Entity
{
    static protected $_defaultEntityManager = null;


    protected $_data = null;

    protected $_entityName = null;
    protected $_id = null;

    public function init() {}

    abstract public function getMembers();

    abstract public function getPrimary();

    //setter for properies and items in the underlying data array
    public function __set($variableName, $value)
    {
        if (array_key_exists($variableName, array_change_key_case($this->getMembers()))) {
            $newData = $this->_data;
            $newData[$variableName] = $value;
            $this->_update($newData);
            $this->_data = $newData;
        } else {
            if (property_exists($this, $variableName)) {
                $this->$variableName = $value;
            } else {
                throw new Exception("Set failed. Class " . get_class($this) .
                    " does not have a member named " . $variableName . ".");
            }
        }
    }

    //getter for properies and items in the underlying data array
    public function __get($variableName)
    {
        if (array_key_exists($variableName, array_change_key_case($this->getMembers()))) {
            return $this->_data[$variableName];
        } else {
            if (property_exists($this, $variableName)) {
                return $this->$variableName;
            } else {
                throw new Exception("Get failed. Class " . get_class($this) .
                    " does not have a member named " . $variableName . ".");
            }
        }
    }

}