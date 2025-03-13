<?php

namespace App\View\Components\Customer;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class Navbar extends Component
{
    public $totalUnreadMessages;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Calculate total unread messages for the authenticated user
        if (Auth::check()) {
            $this->totalUnreadMessages = Conversation::where('receiver_id', Auth::id())
                ->where('receiver_type', 'customer')
                ->where('is_read', 0)
                ->count();
        } else {
            $this->totalUnreadMessages = 0;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.customer.navbar', [
            'totalUnreadMessages' => $this->totalUnreadMessages,
        ]);
    }
}