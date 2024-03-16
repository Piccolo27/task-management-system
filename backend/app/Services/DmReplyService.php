<?php

namespace App\Services;

use App\Contracts\Repositories\DmReplyRepositoryInterface;
use App\Contracts\Repositories\DmRepositoryInterface;
use App\Contracts\Services\DmReplyServiceInterface;
use App\Events\DmReply\DmReplyDeleted;
use App\Events\DmReply\DmReplySent;
use App\Events\DmReply\DmReplyUpdated;
use App\Models\DmReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DmReplyService implements DmReplyServiceInterface
{
    private DmRepositoryInterface $dmRepository;
    private DmReplyRepositoryInterface $dmReplyRepository;

    /**
     * To create new instance for dm reply service
     *
     * @param DmReplyRepositoryInterface $dmReplyRepository
     * @param DmRepositoryInterface $dmRepository
     */
    public function __construct(
        DmReplyRepositoryInterface $dmReplyRepository,
        DmRepositoryInterface      $dmRepository
    ) {
        $this->dmReplyRepository = $dmReplyRepository;
        $this->dmRepository = $dmRepository;
    }

    /**
     * To create dm reply
     *
     * @param array $data
     * @return JsonResponse
     */
    public function createDmReply(array $data): JsonResponse
    {
        try {
            $data['created_by'] = Auth::user()->employee_id;
            $data['updated_by'] = Auth::user()->employee_id;

            $this->checkDmThreadReplyable($data['dm_thread_id']);
            $dmReply = $this->dmReplyRepository->createDmReply($data);
            broadcast(new DmReplySent($dmReply));
            $response = response()->json([
                'message' => config('constants.success.DM_RP_CREATE')
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error("An exception occurred in creating new direct message reply: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.DM_RP_CREATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To check the dm thread is replyable or not
     * If not, throw a business logic exception
     *
     * @param int $dmThreadId
     * @return void
     */
    public function checkDmThreadReplyable(int $dmThreadId): void
    {
        $dmThread = $this->dmRepository->getDmThreadById($dmThreadId);

        if ($dmThread[0]->dm->replyable !== config('constants.REPLYABLE')) {
            throw new \LogicException('This thread is not replyable. It is readonly thread message.');
        }
    }


    /**
     * To update direct message reply by id
     *
     * @param array $data
     * @param DmReply $dmReply
     * @return JsonResponse
     */
    public function updateDmReply(array $data, DmReply $dmReply): JsonResponse
    {
        try {
            $newDmReply = $this->dmReplyRepository->updateDmReply($data, $dmReply);
            broadcast(new DmReplyUpdated($newDmReply));
            $response = response()->json([
                'message' => 'success'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in updating direct message reply: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.DM_RP_UPDATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * To delete dm reply by id
     *
     * @param DmReply $dmReply
     * @return JsonResponse
     */
    public function deleteDmReply(DmReply $dmReply): JsonResponse
    {
        try {
            $this->dmReplyRepository->deleteDmReply($dmReply);
            broadcast(new DmReplyDeleted([
                'dm_reply_id' => $dmReply->dm_reply_id,
                'dm_thread_id' => $dmReply->dm_thread_id
            ]));

            $response = response()->json([
                'message' => 'success'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in deleting direct message reply: " . $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.DM_RP_DELETE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
