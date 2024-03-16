<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class UniqueSoftDeleteObserver
{
    private const DELIMITER = '--';

    public function deleted(Model $model): void
    {
        foreach ($model->getDuplicateAvoidColumns() as $column) {
            $newValue = time().self::DELIMITER.$model->{$column};
            $model->{$column} = $newValue;
        }
        $model->save();
    }
}
