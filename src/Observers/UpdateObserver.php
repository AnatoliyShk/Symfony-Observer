<?php

namespace App\Observers;

use App\Entity;
use App\EntityManager;
use SplObserver;
use SplSubject;

class UpdateObserver implements SplObserver
{


    public function update(SplSubject $subject, string $event = null, $data = null)
    {
        $myfile = fopen("log.txt", "a") or die("Unable to open file!");
        if ($data !== null) {
            $txt = $event . " " . serialize($data->toArray()) . PHP_EOL;
            fwrite($myfile, $txt);
            fclose($myfile);
        }
    }
}