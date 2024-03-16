<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ProjectServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateProjectRequest;
use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private ProjectServiceInterface $projectService;
    private JsonResponse $unauthorizeResponse;

    /**
     * To create new instance of project controller
     *
     * @param ProjectServiceInterface $projectService
     */
    public function __construct(ProjectServiceInterface $projectService)
    {
        $this->projectService = $projectService;
        $this->unauthorizeResponse = response()->json(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * To create new project
     *
     * @param CreateProjectRequest $request
     * @return JsonResponse
     */
    public function createProject(CreateProjectRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', Project::class);
            return $this->projectService->createProject($request->validated());
        } catch (AuthorizationException $e) {
            return $this->unauthorizeResponse;
        }
    }

    /**
     * To get all projects
     *
     * @return JsonResponse
     */
    public function getProjects(): JsonResponse
    {
        try {
            $this->authorize('viewAny', Project::class);
            return $this->projectService->getProjects();
        } catch (AuthorizationException $e) {
            return $this->unauthorizeResponse;
        }
    }

    /**
     * To get project by id
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function getProject(Project $project): JsonResponse
    {
        try {
            $this->authorize('view', $project);
            return $this->projectService->getProject($project);
        } catch (AuthorizationException $e) {
            return $this->unauthorizeResponse;
        }
    }

    /**
     * To update project by id
     *
     * @param CreateProjectRequest $request
     * @param Project $project
     * @return JsonResponse
     */
    public function updateProject(CreateProjectRequest $request, Project $project): JsonResponse
    {
        try {
            $this->authorize('update', $project);
            return $this->projectService->updateProject($request->validated(), $project);
        } catch (AuthorizationException $e) {
            return $this->unauthorizeResponse;
        }
    }

    /**
     * To delete project
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function deleteProject(Project $project): JsonResponse
    {
        try {
            $this->authorize('delete', $project);
            return $this->projectService->deleteProject($project);
        } catch (AuthorizationException $e) {
            return $this->unauthorizeResponse;
        }
    }
}
