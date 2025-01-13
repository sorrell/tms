<x-mail::message>
# You've been invited to join organization "{{ $organization->name }}" on {{ config('app.name') }}!

Click the button below to accept the invitation:

<x-mail::button :url="$invite->inviteUrl()">
Join {{ $organization->name }}
</x-mail::button>

</x-mail::message>
