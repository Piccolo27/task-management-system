<?php

namespace App\Repositories;

use App\Contracts\Repositories\DmReplyRepositoryInterface;
use App\Models\DmReply;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DmReplyRepository implements DmReplyRepositoryInterface
{
    /**
     * To create dm reply
     *
     * @param array $data
     * @return DmReply
     */
    public function createDmReply(array $data): DmReply
    {
        $dmReply = DmReply::create($data);
        return $dmReply->load('createdUser', 'thread');
    }


    /**
     * To update direct message reply by id
     *
     * @param array $data
     * @param DmReply $dmReply
     * @return DmReply
     */
    public function updateDmReply(array $data, DmReply $dmReply): DmReply
    {
        $dmReply->body = $data['body'];
        $dmReply->updated_at = Carbon::now();
        $dmReply->updated_by = Auth::id();
        $dmReply->save();

        return $dmReply->load('createdUser', 'thread');
    }

    /**
     * To delete dm reply by id
     *
     * @param DmReply $dmReply
     * @return void
     */
    public function deleteDmReply(DmReply $dmReply): void
    {
        $dmReply->delete();
    }
}
