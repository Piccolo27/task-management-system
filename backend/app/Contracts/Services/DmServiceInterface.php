<?php

namespace App\Contracts\Services;

use App\Models\DirectMessage;

interface DmServiceInterface
{
    public function createDm(array $data);
    public function getDms();
    public function getDmById(int $id);
    public function getDmThreadById(int $threadId);
    public function updateDmById(DirectMessage $dm, array $data);
}
