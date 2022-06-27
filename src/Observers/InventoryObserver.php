<?php

namespace App\Observers;

use App\Helper;
use SplObserver;
use SplSubject;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class InventoryObserver implements SplObserver
{

    public function update(SplSubject $subject, string $event = null, $data = null)
    {
        $transport = Transport::fromDsn(Helper::$mailerDSN);
        $mailer = new Mailer($transport);
        if($data !== null) {
            $itemData = $data->toArray();
            $qoh = $itemData['_data']['qoh'];
            $sku = $itemData['_data']['sku'];
            if ($itemData['_data']['qoh'] < 5) {
                $email = new Email();
                $email->from('test@example.com')
                    ->to('receiver@example.com')
                    ->subject('QOH for an inventory item dips below 5')
                    ->text("QOH for an inventory item {$sku} are {$qoh}");
                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                    $message = $e->getMessage();
                    $myfile = fopen("log.txt", "a") or die("Unable to open file!");
                    fwrite($myfile, "Error low QOH notification gor {$sku}. Message: {$message}");
                    fclose($myfile);
                }
            }
        }
    }
}