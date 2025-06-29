<?php

namespace App\Events;

use App\Models\SiteUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAccountActivated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SiteUser $user;

    public function __construct(SiteUser $user)
    {
        $this->user = $user;
    }
}
