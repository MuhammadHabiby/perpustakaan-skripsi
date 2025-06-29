<?php

namespace App\Events;

use App\Models\LostReport;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LostReportResolved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LostReport $lostReport;

    public function __construct(LostReport $lostReport)
    {
        $this->lostReport = $lostReport;
    }
}
