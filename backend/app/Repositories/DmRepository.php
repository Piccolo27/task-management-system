<?php

namespace App\Repositories;

use App\Models\DmThread;
use App\Models\DirectMessage;
use App\Models\ThreadEmployee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Contracts\Repositories\DmRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DmRepository implements DmRepositoryInterface
{
    /**
     * To create direct message
     *
     * @param array $data
     * @return DirectMessage
     */
    public function createDm(array $data): DirectMessage
    {
        return DirectMessage::create($data);
    }

    /**
     * To create direct message thread
     *
     * @param array $data
     * @return DmThread
     */
    public function createDmThread(array $data): DmThread
    {
        return DmThread::create($data);
    }

    /**
     * To create direct message thread and employee relationship
     *
     * @param array $data
     * @return void
     */
    public function createThreadEmployee(array $data): void
    {
        ThreadEmployee::create($data);
    }

    /**
     * To get all direct messages
     *
     * @return Collection
     */
    public function getDmsForAdmin(): Collection
    {
        return DirectMessage::with('owner')->get();
    }

    /**
     * To get direct messages only for members
     *
     * @return Collection
     */
    public function getDmsForMember(): Collection
    {
        return DirectMessage::whereHas('dmThread.members', function ($query) {
            $query->where('employees.employee_id', Auth::id());
        })
        ->with([
            'dmThread.members',
            'owner'
        ])->get();
    }

    /**
     * To get direct message by id
     *
     * @param integer $id
     * @return Collection
     */
    public function getDmById(int $id): Collection
    {
        return DirectMessage::where('direct_message_id', $id)
            ->with([
                'dmThread.members',
                'dmThread.dmReplys.createdUser',
                'owner'
            ])
            ->get();
    }


    /**
     * To get dm thread by id
     *
     * @param int $threadId
     * @return Collection
     */
    public function getDmThreadById(int $threadId): Collection
    {
        return DmThread::where('dm_thread_id', $threadId)->with('members', 'dm')->get();
    }

    /**
     * To update direct message
     *
     * @param DirectMessage $dm
     * @param array $data
     * @return void
     */
    public function updateDm(DirectMessage $dm, array $data): void
    {
        $dm->title = $data['title'];
        $dm->body = $data['body'];
        $dm->replyable = $data['replyable'];
        $dm->start_at = $data['start_at'];
        $dm->updated_at = $data['updated_at'];
        $dm->save();
    }

    /**
     * To update direct message thread
     *
     * @param array $data
     * @return void
     */
    public function updateDmThread(array $data): void
    {
        $dmThread = DmThread::findOrFail($data['dm_thread_id']);
        $dmThread->dm_updated = $data['dm_updated'];
        $dmThread->save();
    }

    /**
     * To update thread employee by thread id
     *
     * @param int $dmThreadId
     * @param array $selectedPersonIds
     * @return void
     */
    public function updateThreadEmployeeByThreadId(int $dmThreadId, array $selectedPersonIds): void
    {
        $dmThread = DmThread::findOrFail($dmThreadId);
        $dmThread->members()->sync($selectedPersonIds);
    }
}
