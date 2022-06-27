<?php


namespace App;


class InventoryItem extends Entity
{
    //Update the number of items, because we have shipped some.
    public function itemsHaveShipped(int $numberShipped): self
    {
        $this->_data['qoh'] = $this->qoh - $numberShipped;

        return $this;
    }

    //We received new items, update the count.
    public function itemsReceived(int $numberReceived): self
    {
        $this->_data['qoh'] += $numberReceived;

        return $this;
    }

    public function changeSalePrice(string $salePrice): self
    {
        $this->_data['salePrice'] = $salePrice;

        return $this;
    }

    public function getMembers(): array
    {
        //These are the field in the underlying data array
        return ["sku" => 1, "qoh" => 1, "cost" => 1, "salePrice" => '1'];
    }

    public function getPrimary(): string
    {
        //Which field constitutes the primary key in the storage class?
        return "sku";
    }

    public function toArray(): array {
        return get_object_vars($this);
    }
}