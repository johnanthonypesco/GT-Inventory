<x-mail::message>
# ⚠️ Security Alert: Brute-Force Attempt Detected

Hello,

Ten failed login attempts have been detected
<x-mail::table>
| Detail | Information |
|:--- |:--- |
| **IP Address** | {{ $ipAddress }} |
| **Approximate Location** | {{ $location }} |
| **Attempted Email** | {{ $attemptedEmail }} |
| **Time of Alert** | {{ now()->format('F d, Y - h:i:s A T') }} |
</x-mail::table>

This IP address has been automatically blocked due to excessive failed login attempts. Please review the activity or contact the system administrator. If you believe this was a mistake, you can unblock the IP from the history log.
<x-mail::button :url="route('superadmins.login')" color="error">
View History Log
</x-mail::button>

Thanks,<br>
RMPOIMS
</x-mail::message>