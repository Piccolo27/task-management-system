<?php

namespace App\Services;

use App\Models\DirectMessage;
use App\Models\DmThread;
use App\Repositories\DmRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Contracts\Repositories\DmRepositoryInterface;
use App\Contracts\Services\DmServiceInterface;
use Illuminate\Support\Facades\Queue;

class DmService implements DmServiceInterface
{
    private DmRepository $dmRepository;

    /**
     * To create an instance of dm service
     *
     * @param DmRepositoryInterface $dmRepository
     */
    public function __construct(DmRepositoryInterface $dmRepository)
    {
        $this->dmRepository = $dmRepository;
    }

    /**
     * To create direct message
     *
     * @param array $data
     * @return JsonResponse
     */
    public function createDm(array $data): JsonResponse
    {
        if (!$data['reservationAndTransmissionDT']) {
            $startAtDateTime = Carbon::now();
        } else {
            $startAtDateTime = Carbon::parse($data["reservationAndTransmissionDT"]);
        }
        $currentUserID = Auth::user()->employee_id;

        return $this->processDmCreateTranscation($data, $startAtDateTime, $currentUserID);
    }

    /**
     * To process database transcation of inserting direct message
     *
     * @param array $data
     * @param Carbon $startAtDateTime
     * @param integer $currentUserID
     * @return JsonResponse
     */
    private function processDmCreateTranscation(array $data, Carbon $startAtDateTime, int $currentUserID): JsonResponse
    {
        DB::beginTransaction();
        try {
            $dmMessageData = [
                'owner_id' => $currentUserID,
                'title' => $data['title'],
                'body' => $data['text'],
                'replyable' => $data['replyable'],
                'start_at' => $startAtDateTime,
                'created_by' => $currentUserID,
                'updated_by' => $currentUserID,
            ];
            $dmMessage = $this->dmRepository->createDm($dmMessageData);

            $dmThreadData = [
                'direct_message_id' => $dmMessage->direct_message_id,
                'owner_unread' => true,
                'dm_updated' => false,
                'created_by' => $currentUserID,
            ];
            $dmThread = $this->dmRepository->createDmThread($dmThreadData);

            foreach ($data['selectedPerson'] as $targetPersonID) {
                $threadEmployeeData = [
                    'dm_thread_id' => $dmThread->dm_thread_id,
                    'employee_id' => $targetPersonID
                ];
                $this->dmRepository->createThreadEmployee($threadEmployeeData);
            }
            DB::commit();
            $response = response()->json(['message' => config('constants.success.DM_CREATE')], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("An exception occurred in creating new direct message: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.DM_CREATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To get direct messages according to user position
     *
     * @return JsonResponse
     */
    public function getDms(): JsonResponse
    {
        try {
            if (Auth::user()->position == config('constants.ADMIN')) {
                $dms = $this->dmRepository->getDmsForAdmin();
            } else {
                $dms = $this->dmRepository->getDmsForMember();
            }

            $response = response()->json(['dms' => $dms], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting direct messages: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.DMS_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To get direct message by id
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getDmById(int $id): JsonResponse
    {
        try {
            $dm = $this->dmRepository->getDmById($id);
            $response = response()->json(['dm'=> $dm], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting direct message: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.DM_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To get direct message thread by id
     *
     * @param int $threadId
     * @return Collection
     */
    public function getDmThreadById(int $threadId): Collection
    {
        return $this->dmRepository->getDmThreadById($threadId);
    }

    /**
     * To update direct message by id
     *
     * @param DirectMessage $dm
     * @param array $data
     * @return JsonResponse
     */
    public function updateDmById(DirectMessage $dm, array $data): JsonResponse
    {
        if (!$data['reservationAndTransmissionDT']) {
            $startAtDateTime = Carbon::now();
        } else {
            $startAtDateTime = Carbon::parse($data["reservationAndTransmissionDT"]);
        }

        return $this->processDmUpdateTranscation($data, $startAtDateTime, $dm);
    }

    /**
     * To process database transcation of updating direct message
     *
     * @param array $data
     * @param Carbon $startAtDateTime
     * @param DirectMessage $dm
     * @return JsonResponse
     */
    private function processDmUpdateTranscation(array $data, Carbon $startAtDateTime, DirectMessage $dm): JsonResponse
    {
        DB::beginTransaction();
        try {
            $dmMessageData = [
                'title' => $data['title'],
                'body' => $data['text'],
                'replyable' => $data['replyable'],
                'start_at' => $startAtDateTime,
                'updated_at' => Carbon::now()
            ];
            $this->dmRepository->updateDm($dm, $dmMessageData);

            $dmThreadData = [
                'dm_thread_id' => $dm->dmThread->dm_thread_id,
                'dm_updated' => true,
            ];
            $this->dmRepository->updateDmThread($dmThreadData);
            $this->dmRepository->updateThreadEmployeeByThreadId($dm->dmThread->dm_thread_id, $data['selectedPerson']);

            DB::commit();
            $response = response()->json(['message' => config('constants.success.DM_CREATE')], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("An exception occurred in updating direct message: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.DM_CREATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
