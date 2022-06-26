<?php


namespace App;


//This class managed in-memory entities and commmunicates with the storage class (DataStore in our case).
use SplObjectStorage;
use SplSubject;
use App\InventoryItem;
use Symfony\Component\Mailer\MailerInterface;

class EntityManager implements SplSubject
{

    protected $_entities = array();

    protected $_entityIdToPrimary = array();

    protected $_entityPrimaryToId = array();

    protected $_entitySaveList = array();

    protected $_nextId = null;

    protected $_dataStore = null;

    private $_observers = null;

    private $_mailer = null;

    public function __construct(string $storePath)
    {
        $this->_observers = new SplObjectStorage();

        $this->_dataStore = new DataStore($storePath);

        $this->_nextId = 1;

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
        $entity->_em = Entity::getDefaultEntityManager();
        $id = $entity->_id = $this->_nextId++;
        $this->_entities[$id] = $entity;
        $primary = $data[$entity->getPrimary()];
        $this->_entityIdToPrimary[$id] = $primary;
        $this->_entityPrimaryToId[$primary] = $id;
        if ($fromStore !== true) {
            $this->_entitySaveList[] = $id;
        }

        return $entity;
    }

    //update
    public function update(Entity $entity, array $newData): Entity
    {
        if ($newData === $entity->_data) {
            //Nothing to do
            return $entity;
        }

        $this->_entitySaveList[] = $entity->_id;
        $oldPrimary = $entity->{$entity->getPrimary()};
        $newPrimary = $newData[$entity->getPrimary()];
        if ($oldPrimary != $newPrimary)
        {
            $this->_dataStore->delete(get_class($entity),$oldPrimary);
            unset($this->_entityPrimaryToId[$oldPrimary]);
            $this->_entityIdToPrimary[$entity->$id] = $newPrimary;
            $this->_entityPrimaryToId[$newPrimary] = $entity->$id;
        }
        $entity->_data = $newData;
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
        unset($this->_entityIdToPrimary[$id], $this->_entityPrimaryToId[$primary]);
        return isset($this->_entityIdToPrimary[$id], $this->_entityPrimaryToId[$primary]);
    }

    public function findByPrimary(Entity $entity, string $primary): ?string
    {
        if (isset($this->_entityPrimaryToId[$primary])) {
            $id = $this->_entityPrimaryToId[$primary];
            return $this->_entities[$id];
        } else {
            return null;
        }
    }

    //Update the datastore to update itself and save.
    public function updateStore(): void {
        foreach($this->_entitySaveList as $id) {
            $entity = $this->_entities[$id];
            $this->_dataStore->set(get_class($entity),$entity->{$entity->getPrimary()},$entity->_data);
        }
        $this->_dataStore->save();
    }

    public function getMailer()
    {
        return $this->_mailer;
    }
}