<?php

namespace App\Controller;

use App\EntityManager;
use App\Entity;
use App\InventoryItem;
use App\Observers\InventoryObserver;
use App\Observers\UpdateObserver;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{

    private $updateObserver;
    private $inventoryObserver;

    public function __construct(UpdateObserver $updateObserver, InventoryObserver $inventoryObserver)
    {
        $this->updateObserver = $updateObserver;
        $this->inventoryObserver = $inventoryObserver;
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        $entityManager = new EntityManager('/var/www/public/test.txt');
        $inventoryItem  = $entityManager->create(InventoryItem::class, ["sku" => 1, "qoh" => 1, "cost" => 1, "salePrice" => 1]);
        $entityManager->attach($this->updateObserver);
        $entityManager->attach($this->inventoryObserver);
        Entity::setDefaultEntityManager($entityManager);
        $entityManager->update($inventoryItem, ["sku" => 1, "qoh" => 1, "cost" => 555, "salePrice" => 1]);
        $item1 = Entity::getEntity(InventoryItem::class,
                                   array('sku' => 'abc-4589', 'qoh' => 0, 'cost' => '5.67', 'salePrice' => '7.27'));
        $item2 = Entity::getEntity(InventoryItem::class,
                                   array('sku' => 'hjg-3821', 'qoh' => 0, 'cost' => '7.89', 'salePrice' => '12.00'));
        $item3 = Entity::getEntity(InventoryItem::class,
                                   array('sku' => 'xrf-3827', 'qoh' => 0, 'cost' => '15.27', 'salePrice' => '19.99'));
        $item4 = Entity::getEntity(InventoryItem::class,
                                   array('sku' => 'eer-4521', 'qoh' => 0, 'cost' => '8.45', 'salePrice' => '1.03'));
        $item5 = Entity::getEntity(InventoryItem::class,
                                   array('sku' => 'qws-6783', 'qoh' => 0, 'cost' => '3.00', 'salePrice' => '4.97'));

        $item1->itemsReceived(4);
        $item2->itemsReceived(2);
        $item3->itemsReceived(12);
        $item4->itemsReceived(20);
        $item5->itemsReceived(1);

        $item3->itemsHaveShipped(5);
        $item4->itemsHaveShipped(16);

        $item4->changeSalePrice(0.87);

        $entityManager->updateStore();
        exit();
        return new Response();
    }
}