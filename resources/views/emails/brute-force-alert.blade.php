<x-mail::message>
# ⚠️ Security Alert: Brute-Force Attempt Detected

Hello,

Five failed login attempts have been detected
<x-mail::table>
| Detail | Information |
|:--- |:--- |
| **IP Address** | {{ $ipAddress }} |
| **Approximate Location** | {{ $location }} |
| **Attempted Email** | {{ $attemptedEmail }} |
| **Time of Alert** | {{ now()->format('F d, Y - h:i:s A T') }} |
</x-mail::table>

You may want to investigate this activity in the history log or consider temporarily blocking this IP address at your firewall.

<x-mail::button :url="route('superadmins.login')" color="error">
View History Log
</x-mail::button>

Thanks,<br>
RMPOIMS
</x-mail::message>