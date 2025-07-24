@php
    // Determine if the message is from the currently logged-in customer
    $isYou = $message->sender_type === 'customer' && $message->sender_id == auth()->id();
    
    // Set alignment class based on who sent the message
    $justifyClass = $isYou ? 'justify-end' : 'justify-start';

    // Set background color class based on the sender's role
    $bgColorClass = 'bg-gray-300'; // Default for others
    if ($isYou) {
        $bgColorClass = 'bg-blue-500 text-white';
    } elseif ($message->sender_type === 'super_admin') {
        $bgColorClass = 'bg-red-500 text-white';
    } elseif ($message->sender_type === 'admin') {
        $bgColorClass = 'bg-blue-700 text-white';
    } elseif ($message->sender_type === 'staff') {
        $bgColorClass = 'bg-green-500 text-white';
    }
@endphp

<div class="mb-4 flex {{ $justifyClass }}" data-message-id="{{ $message->id }}">
    <div class="max-w-xs p-3 rounded-lg shadow-md {{ $bgColorClass }}">
        
        {{-- Display the text message if it exists --}}
        @if($message->message)
            <p class="text-sm" style="word-wrap: break-word;">{{ $message->message }}</p>
        @endif

        {{-- Display the file if it exists --}}
        @if ($message->file_path)
            <div class="mt-2">
                @php
                    $fileExt = strtolower(pathinfo($message->file_path, PATHINFO_EXTENSION));
                @endphp
                
                @if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
                    <a href="{{ $message->file_path }}" target="_blank">
                        <img src="{{ $message->file_path }}" class="w-40 rounded-lg mt-1">
                    </a>
                @elseif (in_array($fileExt, ['mp4', 'mov', 'avi']))
                    <video controls class="w-40 rounded-lg mt-1">
                        <source src="{{ $message->file_path }}" type="video/{{ $fileExt }}">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <a href="{{ $message->file_path }}" download class="text-white underline block mt-1">
                        📎 Download File
                    </a>
                @endif
            </div>
        @endif

        {{-- Timestamp --}}
        <p class="text-xs opacity-70 mt-1 text-right">
            {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}
        </p>
    </div>
</div>