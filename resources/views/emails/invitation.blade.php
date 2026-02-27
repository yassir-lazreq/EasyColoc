<x-mail::message>
# You're Invited!

Hello!

**{{ $inviterName }}** has invited you to join the colocation **{{ $colocationName }}** on EasyColoc.

EasyColoc helps you track shared expenses and manage payments with your roommates easily and fairly.

<x-mail::button :url="$acceptUrl">
Accept Invitation
</x-mail::button>

This invitation will expire on **{{ $expiresAt }}**.

If you don't want to join this colocation, you can simply ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
