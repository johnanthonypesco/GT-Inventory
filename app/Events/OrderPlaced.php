<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderDetails;

    /**
     * Create a new event instance.
     *
     * @param array $orderDetails
     * @return void
     */
    public function __construct(array $orderDetails)
    {
        $this->orderDetails = $orderDetails;
    }
}