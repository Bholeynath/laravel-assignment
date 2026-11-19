<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendInvitationRequest;
use App\Services\InvitationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\Api\AcceptInvitationRequest;

class InvitationController extends Controller
{
    use ApiResponse;

    protected InvitationService $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    /**
     * Send invitation to Admin or Member.
     */
    public function sendInvitation(SendInvitationRequest $request)
    {
        $result = $this->invitationService->sendInvitation( $request->user(),$request->validated());

        return $this->successResponse('Invitation sent successfully.',$result);
    }

    public function acceptInvitation(AcceptInvitationRequest $request): JsonResponse
    {
        $user = $this->invitationService->acceptInvitation(
            $request->validated()
        );

        return $this->successResponse('Invitation accepted successfully. Account created.', new UserResource($user));
    }
}
