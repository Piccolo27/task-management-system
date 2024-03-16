<?php

namespace App\Contracts\Repositories;

use App\Models\Project;

interface ProjectRepositoryInterface
{

    public function createProject(array $data);

    public function getProjects();

    public function deleteProject(Project $project);

    public function getProject(Project $project);

    public function updateProject(array $data, Project $project);

    public function getProjectsTotalCount();
}
