<?php
namespace App\View\Components\Customer;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    public $totalUnreadMessages;

    public function __construct()
{
    if (Auth::check()) {
        $this->totalUnreadMessages = Conversation::where('receiver_id', Auth::id())
            ->where('receiver_type', 'customer')
            ->where('is_read', 0)
            ->count();
    } else {
        $this->totalUnreadMessages = 0;
    }

    // Debugging: Tingnan kung may value ang $totalUnreadMessages
    // dd($this->totalUnreadMessages);
}


public function render(): View|Closure|string
{
    return view('components.customer.header', [
        'totalUnreadMessages' => $this->totalUnreadMessages,
    ]);
}

}
