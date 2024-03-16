<?php

namespace App\Contracts\Repositories;

use App\Models\DirectMessage;

interface DmRepositoryInterface
{
    public function createDm(array $data);
    public function createDmThread(array $data);
    public function createThreadEmployee(array $data);
    public function getDmsForAdmin();
    public function getDmsForMember();
    public function getDmById(int $id);
    public function getDmThreadById(int $threadId);
    public function updateDm(DirectMessage $dm, array $data);
    public function updateDmThread(array $data);
    public function updateThreadEmployeeByThreadId(int $dmThreadId, array $selectedPersonIds);
}
