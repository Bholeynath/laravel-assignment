<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvitationService
{
    // Send invitation to a user.
    
    public function sendInvitation(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $company = $this->getCompany($user, $data);

            return $this->createInvitation($user, $company, $data);
        });
    }

    // Get or create company according to user role.

    private function getCompany(User $user, array $data)
    {
        if ($user->role === User::ROLE_SUPER_ADMIN) {
            return Company::create([
                'name' => $data['company_name'],
            ]);
        }

        if ($user->role === User::ROLE_ADMIN && $user->company) {
            return $user->company;
        }

        throw ValidationException::withMessages([
            'company' => 'Company not found for this user.',
        ]);
    }

    // Create invitation record.
    
    private function createInvitation(User $user,Company $company,array $data) 
    {
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'token'      => Str::random(32),
            'invited_by' => $user->id,
        ]);

        return [
            'invitation' => $invitation,
            'invitation_url' => url('/accept-invitation?token=' . $invitation->token),
        ];
    }

    //accept invitation 

    public function acceptInvitation(array $data)
    {
        return DB::transaction(function () use ($data) {

            $invitation = Invitation::where('token', $data['token'])->whereNull('accepted_at')->first();

            if (!$invitation) {
                throw ValidationException::withMessages([
                    'token' => 'Invalid or already accepted invitation.',
                ]);
            }

            $user = User::create([
                'name' => $invitation->name,
                'email' => $invitation->email,
                'password' => $data['password'],
                'company_id' => $invitation->company_id,
                'role' => $invitation->role,
            ]);

            $invitation->update([
                'accepted_at' => now(),
            ]);

            return $user;
        });
    }

    public function validateInvitation(string $token): bool
    {
        return Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->exists();
    }
}
