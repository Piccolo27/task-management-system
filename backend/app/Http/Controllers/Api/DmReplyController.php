<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\DmReplyServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateDmReplyRequest;
use App\Models\DmReply;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DmReplyController extends Controller
{
    private DmReplyServiceInterface $dmReplyService;

    /**
     * To create new instance of dm controller
     *
     *@param DmReplyServiceInterface $dmReplyService
     */
    public function __construct(DmReplyServiceInterface $dmReplyService)
    {
        $this->middleware("auth:api");
        $this->dmReplyService = $dmReplyService;
    }

    /**
     * To create direct message reply
     *
     * @param CreateDmReplyRequest $request
     * @return JsonResponse
     */
    public function createDmReply(CreateDmReplyRequest $request): JsonResponse
    {
        return $this->dmReplyService->createDmReply($request->validated());
    }

    /**
     * To update dm reply by id
     *
     * @param CreateDmReplyRequest $request
     * @param DmReply $dmReply
     * @return JsonResponse
     */
    public function updateDmReply(CreateDmReplyRequest $request, DmReply $dmReply): JsonResponse
    {
        try {
            $this->authorize('update', $dmReply);

            return $this->dmReplyService->updateDmReply($request->validated(), $dmReply);
        } catch (\Exception $e) {
            Log::error("An exception occurred in updating dm reply: " . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'error' => config('constants.errors.UNAUTHORIZED_REQUEST')
            ], JsonResponse::HTTP_FORBIDDEN);
        }
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
            $this->authorize('delete', $dmReply);

            return $this->dmReplyService->deleteDmReply($dmReply);
        } catch (\Exception $e) {
            Log::error("An exception occurred in deleting dm reply: " . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'error' => config('constants.errors.UNAUTHORIZED_REQUEST')
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }
}
