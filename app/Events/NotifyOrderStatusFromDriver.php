<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;


class NotifyOrderStatusFromDriver implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driverId;
    public $orderId;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($driverId , $orderId , $userId)
    {
        $this->driverId = $driverId;
        $this->orderId  = $orderId;
        $this->userId   = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('order.'.$this->driverId);
        return new Channel('orderNotifyStatus.'.$this->userId);
    }

    public function broadcastWith()
    {
        return Order::with(['restaurant' , 'driver'])->findOrFail($this->orderId)->toArray();
    }
}
