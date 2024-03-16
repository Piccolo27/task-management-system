<?php

namespace App\Broadcasting;

use App\Contracts\Services\DmServiceInterface;
use App\Models\DmThread;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class DmThreadChannel
{
    private DmServiceInterface $dmService;

    /**
     * Create a new channel instance.
     *
     * @param DmServiceInterface $dmService
     */
    public function __construct(DmServiceInterface $dmService)
    {
        $this->dmService = $dmService;
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param Employee $user
     * @param int $dmThreadId
     * @return array|bool
     */
    public function join(Employee $user, int $dmThreadId): array|bool
    {
        $currentUser = Auth::user();

        if ($currentUser->position == config('constants.ADMIN')) {
            return true;
        }

        $threadUsers = $this->dmService->getDmThreadById($dmThreadId);
        $threadUserIds =$threadUsers->flatMap(function ($thread) {
            return collect($thread['members'])->pluck('employee_id');
        });

        return $threadUserIds->contains($currentUser->employee_id);
    }
}
