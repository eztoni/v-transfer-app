<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Arr;

class ReservationAlertEvent
{
    use Dispatchable;

    // THIS PARAMETER DETERMINES IF THE MAIL WILL BE SENT
    public const SEND_MAIL_CONFIG_PARAM = 'send_mail';

    public function __construct(public array $alert_list = [], public array $config = [])
    {  }

    public function shouldSendMail():bool
    {
        return Arr::get($this->config,self::SEND_MAIL_CONFIG_PARAM) ?? false;
    }
}
