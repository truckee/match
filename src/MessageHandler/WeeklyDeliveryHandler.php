<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/MessageHandler/WeeklyDeliveryHandler.php

namespace App\MessageHandler;

use App\Message\WeeklyDelivery;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class WeeklyDeliveryHandler implements MessageHandlerInterface
{
    public function __invoke(WeeklyDelivery $message)
    {
        dump($message);
    }
}
