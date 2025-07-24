@php
    // Determine if the message is from the currently logged-in admin/staff or the other user
    $messageClass = $message->sender_id == auth()->id() ? 'admin' : 'user';
@endphp

<div class="message {{ $messageClass }}" data-message-id="{{ $message->id }}">
    <div class="message-content shadow-sm">

        {{-- Display the text message if it exists --}}
        @if($message->message)
            <p class="mb-1">{{ $message->message }}</p>
        @endif

        {{-- Display the file if it exists --}}
        @if ($message->file_path)
            @php
                // The controller already converted this to a full URL
                $fileUrl = $message->file_path;
                $fileExt = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
            @endphp

            <div class="message-media">
                @if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
                    <a href="{{ $fileUrl }}" target="_blank">
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