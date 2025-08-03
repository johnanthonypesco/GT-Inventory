@php
    // Determine if the message is from the currently logged-in admin/staff or the other user
    $messageClass = $message->sender_id == auth()->id() ? 'admin' : 'user';

    // Get the full URL for the file. 
    // Note: If you used the Model Accessor method, you can just use $message->file_path directly.
    // Using asset() here is a safe fallback.
    $fileUrl = $message->file_path ? asset($message->file_path) : null;
    $fileExt = $fileUrl ? strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION)) : null;
@endphp

<div class="message {{ $messageClass }}" data-message-id="{{ $message->id }}">
    <div class="message-content shadow-sm">

        {{-- Display the text message if it exists --}}
        @if($message->message)
            <p class="mb-1">{{ $message->message }}</p>
        @endif

        {{-- Display the file if it exists --}}
        @if ($fileUrl)
            <div class="message-media">
                @if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
                    {{-- This link now has the 'chat-image' class to trigger the modal --}}
                    <a href="{{ $fileUrl }}" class="chat-image">
                        <img src="{{ $fileUrl }}" alt="Image attachment">
                    </a>

                @elseif(in_array($fileExt, ['mp4', 'mov', 'avi']))
                    <video controls style="width: 200px; border-radius: 8px;">
                        <source src="{{ $fileUrl }}" type="video/{{ $fileExt }}">
                        Your browser does not support the video tag.
                    </video>

                @else
                    <a href="{{ $fileUrl }}" download class="file-download">
                        <i class="fas fa-file-download"></i>
                        <span>Download File</span>
                    </a>
                @endif
            </div>
        @endif

        {{-- Timestamp --}}
        <div class="message-time">
            {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}
        </div>
    </div>
</div>
