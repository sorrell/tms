<?php

namespace App\Actions\Organizations;

use App\Mail\Organizations\UserInvite;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationInvite;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class SendInvite
{
    use AsAction;

    public function handle(string $email, Organization $organization) : OrganizationInvite
    {
        // Human readable letters and numbers without overlap only, uppercase, 6 characters
        $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXY23456789'), 0, 6));

        $maxTries = 5;
        while(OrganizationInvite::where('code', $code)->open()->exists() && $maxTries > 0) {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXY23456789'), 0, 6));
            $maxTries--;
        }

        if($maxTries === 0) {
            throw new \Exception('Could not generate a unique invite code. Please contact support.');
        }

        $email = strtolower($email);

        if(OrganizationInvite::where('email', $email)->open()->exists()) {
            throw new \Symfony\Component\HttpFoundation\Exception\BadRequestException('An open invite already exists for this email address.');
        }

        $invite = OrganizationInvite::create([
            'organization_id' => $organization->id,
            'email' => $email,
            'expire_at' => now()->addDays(7),
            'code' => $code,
        ]);

        Mail::to($email)->send(new UserInvite($invite));

        return $invite;
    }
}
