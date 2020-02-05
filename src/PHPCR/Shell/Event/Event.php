<?php

namespace PHPCR\Shell\Event;

use Symfony\Component\EventDispatcher\Event as OldEvent;
use Symfony\Contracts\EventDispatcher\Event as ContractEvent;

if (class_exists(ContractEvent::class)) {
    class Event extends ContractEvent
    {
    }
} else {
    class Event extends OldEvent
    {
    }
}
