<?php

namespace Modules\Diet\Livewire\Admin\Traits;

trait ConditionTrait
{
    public function convertToAssociativeArray($array, $value = true): array
    {
        $result = [];
        foreach ($array as $item) {
            $result[$item] = $value;
        }

        return $result;
    }
}
