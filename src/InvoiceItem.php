<?php


namespace App;


class InventoryItem extends Entity
{
    //Update the number of items, because we have shipped some.
    public function itemsHaveShipped($numberShipped)
    {
        $current = $this->qoh;
        $current -= $numberShipped;
        $newData = $this->_data;
        $newData['qoh'] = $current;
        $this->update($newData);

    }

    //We received new items, update the count.
    public function itemsReceived($numberReceived)
    {

        $newData = $this->_data;
        $current = $this->qoh;

        for($i = 1; $i <= $numberReceived; $i++) {
            //notifyWareHouse();  //Not implemented yet.
            $newData['qoh'] = $current++;
        }
        $this->update($newData);
    }

    public function changeSalePrice($salePrice)
    {
        $newData = $this->_data;
        $newData['salePrice'] = $this->update($newData);
    }

    public function getMembers()
    {
        //These are the field in the underlying data array
        return array("sku" => 1, "qoh" => 1, "cost" => 1, "salePrice" => 1)    ;
    }

    public function getPrimary()
    {
        //Which field constitutes the primary key in the storage class?
        return "sku";
    }
}