<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Events\Project\ProjectCreated;
use App\Events\Project\ProjectDeleted;
use App\Events\Project\ProjectUpdated;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectService implements ProjectServiceInterface
{
    private ProjectRepositoryInterface $projectRepository;

    /**
     * To create new instance of project service
     *
     * @param ProjectRepositoryInterface $projectRepository
     */
    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * To create new project
     *
     * @param array $data
     * @return JsonResponse
     */
    public function createProject(array $data): JsonResponse
    {
        try {
            $project = $this->projectRepository->createProject($data);
            $this->sendProjectCreatedNotification($project);
            $response = response()->json([
                'message' => config('constants.success.PROJECT_CREATE')
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error("An exception occurred in creating new project: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.PROJECT_CREATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To send project created notification
     *
     * @param Project $project
     * @return void
     */
    private function sendProjectCreatedNotification(Project $project): void
    {
        $data = [
            'message' => Auth::user()->employee_name . ' created a new project named ' . $project->project_name,
            'created_by' => Auth::id(),
            'created_employee' => Auth::user()
        ];
        broadcast(new ProjectCreated($data));
    }

    /**
     * To get all projects
     *
     * @return JsonResponse
     */
    public function getProjects(): JsonResponse
    {
        try {
            $projects = $this->projectRepository->getProjects();
            $response = response()->json(['projects' => $projects], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting all projects: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.PROJECTS_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
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
            $project = $this->projectRepository->getProject($project);
            $response = response()->json(['project' => $project], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in getting project: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.PROJECT_GET')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To update project by id
     *
     * @param array $data
     * @param Project $project
     * @return JsonResponse
     */
    public function updateProject(array $data, Project $project): JsonResponse
    {
        try {
            $project = $this->projectRepository->updateProject($data, $project);
            $this->sendProjectUpdatedNotification($project);
            $response = response()->json([
                'message' => config('constants.success.PROJECT_UPDATE')
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error("An exception occurred in updating project: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.PROJECT_UPDATE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To send project updated notification
     *
     * @param Project $project
     * @return void
     */
    private function sendProjectUpdatedNotification(Project $project): void
    {
        $data = [
            'message' => Auth::user()->employee_name . ' updated a project named ' . $project->project_name,
            'created_by' => Auth::id()
        ];
        broadcast(new ProjectUpdated($data));
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
            $project = $this->projectRepository->deleteProject($project);
            $this->sendProjectDeletedNotification($project);
            $response = response()->json([
                'message' => config('constants.success.PROJECT_DELETE')
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error("An exception occurred in deleting project: ". $e->getMessage(), ['exception' => $e]);
            $response = response()->json([
                'error' => config('constants.errors.PROJECT_DELETE')
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * To send project deleted notification
     *
     * @param Project $project
     * @return void
     */
    private function sendProjectDeletedNotification(Project $project): void
    {
        $data = [
            'message' => Auth::user()->employee_name . ' deleted a project named ' . $project->project_name,
            'created_by' => Auth::id()
        ];
        broadcast(new ProjectDeleted($data));
    }

    /**
     * To get projects total count
     *
     * @return int
     */
    public function getProjectsTotalCount(): int
    {
        return $this->projectRepository->getProjectsTotalCount();
    }
}
