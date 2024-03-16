<?php

namespace App\Contracts\Repositories;

use App\Models\DmReply;

interface DmReplyRepositoryInterface
{
    public function createDmReply(array $data);
    public function updateDmReply(array $data, DmReply $dmReply);
    public function deleteDmReply(DmReply $dmReply);
}
