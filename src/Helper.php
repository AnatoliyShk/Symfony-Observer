<?php


namespace App;
use App\EntityManager;
use App\Entity;
use App\InventoryItem;
use App\Observers\InventoryObserver;
use App\Observers\UpdateObserver;
use http\Env\Response;

class Helper
{

    public static $mailerDSN;

    //Helper function for printing out error information
    public static function getLastError()
    {
        $errorInfo = error_get_last();
        $errorString = "";
        if($errorInfo !== null) {
            $errorString = " Error type {$errorInfo['type']}, {$errorInfo['message']} on line {$errorInfo['line']} of " .
                "{$errorInfo['file']}. ";
        }
        return $errorString;
    }

    public static function driver(string $mailerDSN = "")
    {
        if ($mailerDSN === "") {
            self::$mailerDSN = 'smtp://064b487cad761a:d343c077c3d17c@smtp.mailtrap.io:2525/?encryption=ssl&auth_mode=login';
        } else {
            self::$mailerDSN = $mailerDSN;
        }
        $dataStorePath = "/var/www/public/test.txt";
        $entityManager = new EntityManager($dataStorePath);
        EntityFactory::setEntityManager($entityManager);

        //create five new Inventory items
        $item1 = EntityFactory::getEntity(InventoryItem::class,
            array('sku' => 'abc-4589', 'qoh' => 0, 'cost' => '5.67', 'salePrice' => '7.27'));
        $item2 = EntityFactory::getEntity(InventoryItem::class,
            array('sku' => 'hjg-3821', 'qoh' => 0, 'cost' => '7.89', 'salePrice' => '12.00'));
        $item3 = EntityFactory::getEntity(InventoryItem::class,
            array('sku' => 'xrf-3827', 'qoh' => 0, 'cost' => '15.27', 'salePrice' => '19.99'));
        $item4 = EntityFactory::getEntity(InventoryItem::class,
            array('sku' => 'eer-4521', 'qoh' => 0, 'cost' => '8.45', 'salePrice' => '1.03'));
        $item5 = EntityFactory::getEntity(InventoryItem::class,
            array('sku' => 'qws-6783', 'qoh' => 0, 'cost' => '3.00', 'salePrice' => '4.97'));

        $item1->itemsReceived(4);
        $item2->itemsReceived(2);
        $item3->itemsReceived(12)->itemsHaveShipped(11);
        $item4->itemsReceived(20)->itemsHaveShipped(16)->changeSalePrice((string)0.333);
        $item5->itemsReceived(1);

        $entityManager->delete($item1);

        $entityManager->updateStore();

        var_dump($entityManager->getEntities());

        var_dump($entityManager->findByPrimary('qws-6783'));
    }
}