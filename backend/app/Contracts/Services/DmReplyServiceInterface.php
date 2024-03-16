<?php

namespace App\Contracts\Services;

use App\Models\DmReply;

interface DmReplyServiceInterface
{
    public function createDmReply(array $data);
    public function updateDmReply(array $data, DmReply $dmReply);
    public function deleteDmReply(DmReply $dmReply);
}
