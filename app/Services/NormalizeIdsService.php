<?php

namespace App\Services;

class NormalizeIdsService
{
    public function normalize($ids): array
    {
        if (is_null($ids)) 
        {
            return [];
        }

        if (is_string($ids)) 
        {
            $idsArr = array_filter(explode(',', $ids), fn($v) => strlen($v));
            return array_map('intval', $idsArr);
        }

        if (is_array($ids)) 
        {
            return array_map('intval', $ids);
        }

        return [(int)$ids];
    }
}
