<?php
namespace App\Events;

use App\Models\Dette;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DetteSoldee
{
    use Dispatchable, SerializesModels;

    public $dette;

    public function __construct(Dette $dette)
    {
        $this->dette = $dette;
    }
}