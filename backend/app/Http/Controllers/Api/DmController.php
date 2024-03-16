<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UpdateDmRequest;
use App\Models\DirectMessage;
use App\Services\DmService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateDmRequest;
use App\Contracts\Services\DmServiceInterface;
use Illuminate\Support\Facades\Log;

class DmController extends Controller
{
    private DmService $dmService;

    /**
     * To create new instance of dm controller
     *
     * @param DmServiceInterface $dmService
     */
    public function __construct(DmServiceInterface $dmService)
    {
        $this->middleware("auth:api");
        $this->dmService = $dmService;
    }

    /**
     * To create new direct message
     *
     * @param CreateDmRequest $request
     * @return JsonResponse
     */
    public function createDm(CreateDmRequest $request): JsonResponse
    {
        return $this->dmService->createDm($request->validated());
    }

    /**
     * To get all direct messages
     *
     * @return JsonResponse
     */
    public function getDms(): JsonResponse
    {
        return $this->dmService->getDms();
    }

    /**
     * To get direct message by id
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getDm(int $id): JsonResponse
    {
        return $this->dmService->getDmById($id);
    }

    /**
     * To update direct message by id
     *
     * @param UpdateDmRequest $request
     * @param DirectMessage $dm
     * @return JsonResponse
     */
    public function updateDm(UpdateDmRequest $request, DirectMessage $dm): JsonResponse
    {
        try {
            $this->authorize('update', $dm);

            return $this->dmService->updateDmById($dm, $request->validated());
        } catch (\Exception $e) {
            Log::error("An exception occurred in updating direct message: " . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'error' => config('constants.errors.UNAUTHORIZED_REQUEST')
            ], JsonResponse::HTTP_FORBIDDEN);
        }
    }
}
