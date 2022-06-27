<?php


namespace App;


//This class managed in-memory entities and commmunicates with the storage class (DataStore in our case).
use App\Observers\InventoryObserver;
use App\Observers\UpdateObserver;
use SplObjectStorage;
use SplSubject;
use Symfony\Component\Mailer\MailerInterface;

class EntityManager implements SplSubject
{

    protected $_entities = array();

    protected $_entityPrimaryToId = array();

    protected $_entitySaveList = array();

    protected $_nextId = null;

    protected $_dataStore = null;

    private $_observers = null;

    public function __construct(string $storePath)
    {
        $this->_observers = new SplObjectStorage();

        $this->_dataStore = new DataStore($storePath);

        $this->_nextId = 1;

        $this->attach(new UpdateObserver());

        $this->attach(new InventoryObserver());

        $itemTypes = $this->_dataStore->getItemTypes();
        foreach ($itemTypes as $itemType)
        {
            $itemKeys = $this->_dataStore->getItemKeys($itemType);
            foreach ($itemKeys as $itemKey) {
                $entity = $this->create($itemType, $this->_dataStore->get($itemType, $itemKey), true);
            }
        }
    }

    public function attach(\SplObserver $observer): void
    {
        $this->_observers->attach($observer);
    }

    public function detach(\SplObserver $observer): void
    {
        $this->_observers->detach($observer);
    }

    public function notify(string $event = "*", $data = null): void
    {
        foreach ($this->_observers as $observer) {
            $observer->update($this, $event, $data);
        }
    }
    //create an entity
    public function create(string $entityName, array $data, bool $fromStore = false): Entity
    {
        $entity = new $entityName;
        $entity->_entityName = $entityName;
        $entity->_data = $data;
        $id = $entity->_id = $this->_nextId++;
        $this->_entities[$id] = $entity;
        $primary = $data[$entity->getPrimary()];
        $this->_entityPrimaryToId[$primary] = $id;
        if ($fromStore !== true) {
            $this->_entitySaveList[] = $id;
        }

        return $entity;
    }

    //update
    public function update(?Entity $entity): ?Entity
    {
        $this->notify('update', $entity);
        return $entity;
    }

    //Delete
    public function delete(Entity $entity): bool
    {
        $id = $entity->_id;
        $this->_entities[$id] = null;
        $primary = $entity->{$entity->getPrimary()};
        $this->_dataStore->delete(get_class($entity),$primary);
        unset($this->_entityPrimaryToId[$primary]);
        return isset($this->_entityPrimaryToId[$primary]) && $this->_entities[$id] === null;
    }

    public function findByPrimary(string $primary): ?Entity
    {
        if (isset($this->_entityPrimaryToId[$primary])) {
            $id = $this->_entityPrimaryToId[$primary];
            return $this->_entities[$id];
        }
        return null;
    }

    //Update the datastore to update itself and save.
    public function updateStore(): void {
        foreach($this->_entitySaveList as $id) {
            $entity = $this->_entities[$id];
            $this->update($entity);
            if($entity !== null) {
                $this->_dataStore->set(get_class($entity), $entity->{$entity->getPrimary()}, $entity->_data);
            }
        }
        $this->_dataStore->save();
    }

    public function getEntities() {
        $func = function($obj) {
            if($obj !== null) {
                $obj = $obj->toArray();
            }
            return $obj;
        };
        return array_map($func, $this->_entities);
    }

}