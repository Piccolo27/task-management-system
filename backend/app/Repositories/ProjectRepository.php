<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository implements ProjectRepositoryInterface
{

    /**
     * To create new project
     *
     * @param array $data
     * @return Project
     */
    public function createProject(array $data): Project
    {
        return Project::create($data);
    }

    /**
     * To get all projects
     *
     * @return Collection
     */
    public function getProjects(): Collection
    {
        return Project::all();
    }

    /**
     * To get project
     *
     * @param Project $project
     * @return Project
     */
    public function getProject(Project $project): Project
    {
        return $project;
    }

    /**
     * To update project by id
     *
     * @param array $data
     * @param Project $project
     * @return Project
     */
    public function updateProject(array $data, Project $project): Project
    {
        $project->project_name = $data['project_name'];
        $project->language = $data['language'];
        $project->description = $data['description'];
        $project->start_date = $data['start_date'];
        $project->end_date = $data['end_date'];
        $project->save();

        return $project;
    }

    /**
     * To delete project
     *
     * @param Project $project
     * @return Project
     */
    public function deleteProject(Project $project): Project
    {
        $project->delete();
        return $project;
    }

    /**
     * To get total count of projects
     *
     * @return int
     */
    public function getProjectsTotalCount(): int
    {
        return Project::count();
    }
}
