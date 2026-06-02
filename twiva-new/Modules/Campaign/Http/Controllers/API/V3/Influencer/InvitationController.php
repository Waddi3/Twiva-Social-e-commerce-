<?php

namespace Modules\Campaign\Http\Controllers\API\V3\Influencer;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Campaign\Http\Requests\ApplyInvitationBroadcastRequest;
use Modules\Campaign\Http\Requests\ApplyInvitationRequest;
use Modules\Campaign\Http\Requests\DeclineInvitationRequest;
use Modules\Campaign\Http\Resources\Influencer\IndexBroadcastInvitationsCollection;
use Modules\Campaign\Http\Resources\Influencer\IndexInvitationsCollection;
use Modules\Campaign\Http\Resources\Influencer\ShowBroadcastInvitationResource;
use Modules\Campaign\Http\Resources\Influencer\ShowInvitationResource;
use Modules\Campaign\Repository\Influencer\CampaignRepository;
use Modules\Campaign\Repository\Influencer\InvitationRepository;
use Modules\Home\Http\Controllers\API\V2\BaseTrait;
use Modules\Notification\Notifications\Business\InfluencerAppliedForCampaignNotification;
use Modules\Notification\Notifications\Business\InfluencerRejectedForCampaignNotification;
use Modules\Notification\Notifications\Influencer\CampaignApplicationSentToBusinessNotification;
use Modules\Platform\Entities\WhatsAppGroup;
use Modules\User\Entities\User;

class InvitationController extends Controller
{
    use BaseTrait;

    private $invitationRepository;

    public function __construct(InvitationRepository $invitationRepository)
    {
        $this->invitationRepository = $invitationRepository;
    }

    public function index(): JsonResponse
    {
        $invitations = $this->invitationRepository->getInvitationsToIndex(request('keyword'), request('status'));
        return response()->json(['data' => IndexInvitationsCollection::collection($invitations->paginate(15))->resource]);
    }

    public function indexBroadcast(): JsonResponse
    {
        $invitations = $this->invitationRepository->getInvitationsToIndexBroadcast(request('keyword'), request('status'));
        return response()->json(['data' => IndexBroadcastInvitationsCollection::collection($invitations->paginate(15))->resource]);
    }

    public function show(): JsonResponse
    {
        $invitation = $this->invitationRepository->getInvitationToShow(request('invitation_id'));
        return response()->json(['data' => new ShowInvitationResource($invitation), 'status' => 200]);
    }

    public function showBroadcast(): JsonResponse
    {
        $invitation = $this->invitationRepository->getInvitationToShowBroadcast(request('invitation_id'));
        return response()->json(['data' => new ShowBroadcastInvitationResource($invitation), 'status' => 200]);
    }

    public function applyInvitation(ApplyInvitationRequest $request)
    {
        $invitation = $this->invitationRepository->applyInvitation($request->validated());
        $business = $invitation->post->business;
        $business->notify(new InfluencerAppliedForCampaignNotification($invitation));
        $influencer = auth('api')->user();
        // if ($invitation->user_type == (new User())->getMorphClass()) {
        //     $influencer = User::find($influencer['id']);
        // } elseif ($invitation->user_type == (new WhatsAppGroup())->getMorphClass()) {
        //     $influencer = WhatsAppGroup::with('user')->find($influencer['id'])?->user;
        // }
        $influencer->notify(new CampaignApplicationSentToBusinessNotification($invitation));
        return response()->json(['data' => 'Invitation Applied']);
    }

    public function applyInvitationBroadcast(ApplyInvitationBroadcastRequest $request)
    {
        $invitation = $this->invitationRepository->applyInvitationBroadcast($request->validated());
        $business = $invitation->post->business;
        $business->notify(new InfluencerAppliedForCampaignNotification($invitation));
        $influencer = auth('api')->user();
        $influencer->notify(new CampaignApplicationSentToBusinessNotification($invitation));
        return response()->json(['data' => 'Invitation Applied']);
    }

    public function declineInvitation(DeclineInvitationRequest $request)
    {
        $invitation = $this->invitationRepository->declineInvitation($request->validated());
        $business = $invitation->post->business;
        $business->notify(new InfluencerRejectedForCampaignNotification($invitation));
        return response()->json(['data' => 'Invitation declined']);
    }
}
