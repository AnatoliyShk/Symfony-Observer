<?php


namespace App;


use Exception;

class DataStore
{
    protected $_storePath = null;

    protected $_dataStore = array();

    public function __construct($storePath)
    {
        $this->_storePath = $storePath;
        if (!file_exists($storePath)) {
            if (!touch($storePath)) {
                throw new Exception("Could not create data store file $storePath. Details:" . Helper::getLastError());
            }
            if (!chmod($storePath, 0777)) {
                throw new Exception("Could not set read/write on data store file $storePath. " .
                    "Details:" .  Helper::getLastError());
            }
        }
        if (!is_readable($storePath) || !is_writable($storePath)) {
            throw new Exception("Data store file $storePath must be readable/writable. Details:" .  Helper::getLastError());
        }
        $rawData = file_get_contents($storePath);

        if ($rawData === false) {
            throw new Exception("Read of data store file $storePath failed.  Details:" .  Helper::getLastError());
        }
        if (strlen($rawData > 0)) {
            $this->_dataStore = unserialize($rawData);
        } else {
            $this->_dataStore = null;
        }
    }

    //update the store with information
    public function set($item, $primary, $data)
    {
        $foundItem = null;
        $this->_dataStore[$item][$primary] = $data;
    }

    //get information
    public function get($item, $primary)
    {
        if (isset($this->_dataStore[$item][$primary])) {
            return $this->_dataStore[$item][$primary];
        } else {
            return null;
        }
    }

    //delete an item.
    public function delete($item, $primary)
    {
        if (isset($this->_dataStore[$item][$primary])) {
            unset($this->_dataStore[$item][$primary]);
        }
    }

    //save everything
    public function save()
    {
        $result = file_put_contents($this->_storePath, serialize($this->_dataStore));
        if ($result === null) {
            throw new Exception("Write of data store file $this->_storePath failed.  Details:" .  Helper::getLastError());
        }
    }

    //Which types of items do we have stored
    public function getItemTypes()
    {
        if (is_null($this->_dataStore)) {
            return array();
        }
        return array_keys($this->_dataStore);
    }

    //get keys for an item-type, so we can loop over.
    public function getItemKeys($itemType)
    {
        return array_keys($this->_dataStore[$itemType]);
    }
}