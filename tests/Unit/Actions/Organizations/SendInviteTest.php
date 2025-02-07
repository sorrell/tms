<?php

use App\Actions\Organizations\SendInvite;
use App\Mail\Organizations\UserInvite;
use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationInvite;
use App\Models\Organizations\OrganizationUser;
use App\Models\User; // Assuming your User model is here
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Illuminate\Support\Carbon;
use Mockery;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Any additional global test setup can go here.
});

afterEach(function () {
    // Close any created Mockery mocks
    \Mockery::close();
});

it('sends an invite successfully', function () {
    Mail::fake();

    // Create an organization using its factory.
    $organization = current_organization();

    $email = 'invitee@example.com';

    // Execute the action.
    $action = new SendInvite();
    $invite = $action->handle($email, $organization);

    // Verify that the returned invite is valid.
    expect($invite)->toBeInstanceOf(OrganizationInvite::class);
    expect($invite->code)->toHaveLength(6);
    expect($invite->code)->toEqual(strtoupper($invite->code)); // Code should be uppercase.
    expect($invite->email)->toBe(strtolower($email));

    // Check that the expiration is set approximately 7 days in the future.
    $expectedExpire = now()->addDays(7)->format('Y-m-d');
    $actualExpire = Carbon::parse($invite->expire_at)->format('Y-m-d');
    expect($actualExpire)->toBe($expectedExpire);

    // Assert that the invite email was sent.
    Mail::assertSent(UserInvite::class, function ($mail) use ($email, $invite) {
        return $mail->hasTo($email) && $mail->invite->id === $invite->id;
    });
});

it('throws exception if an open invite already exists for this email', function () {
    Mail::fake();

    $organization = current_organization();
    $email = 'duplicate@example.com';

    // Pre-create an invite for this email.
    OrganizationInvite::create([
        'organization_id' => $organization->id,
        'email' => strtolower($email),
        'code' => 'ABCDEF',
        'expire_at' => now()->addDays(7),
    ]);

    $action = new SendInvite();

    // Expect an exception about an already open invite.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('An open invite already exists for this email address.');

    $action->handle($email, $organization);
});

it('throws exception if user is already a member of the organization', function () {
    Mail::fake();

    $organization = current_organization();
    $email = 'member@example.com';

    // Create a user with the given email.
    $user = User::factory()->create(['email' => strtolower($email)]);

    // Create an organization-user record to simulate organization membership.
    OrganizationUser::create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
    ]);

    $action = new SendInvite();

    // Expect an exception stating the user is already a member.
    $this->expectException(BadRequestException::class);
    $this->expectExceptionMessage('This user is already a member of this organization.');

    $action->handle($email, $organization);
});
