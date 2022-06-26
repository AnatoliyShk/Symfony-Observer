<?php

namespace App\Observers;

use SplObserver;
use SplSubject;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class InventoryObserver implements SplObserver
{

    public function update(SplSubject $subject, string $event = null, $data = null)
    {
//        $itemData = $data->toArray();
//        $qoh = $itemData['_data']['qoh'];
//        $sku = $itemData['_data']['sku'];
//        if($itemData['_data']['qoh'] < 5) {
//            $email = new Email();
//            $email->from('test@example.com')
//                ->to('receiver@example.com')
//                ->subject('QOH for an inventory item dips below 5')
//                ->text("QOH for an inventory item {$sku} are {$qoh}");
//            try {
//                $subject->getMailer()->send($email);
//            } catch (TransportExceptionInterface $e) {
//                $myfile = fopen("log.txt", "a") or die("Unable to open file!");
//                fwrite($myfile, 'Error low QOH notification gor {$sku}. Message: {$message}');
//                fclose($myfile);
//            }
//        }
    }
}