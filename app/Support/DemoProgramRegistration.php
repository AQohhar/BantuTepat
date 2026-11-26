<?php

namespace App\Support;

class DemoProgramRegistration
{
    public static function allowsSelfApplication(array $program): bool
    {
        return $program['status'] === 'OPEN'
            && in_array($program['mode'], ['SELF', 'BOTH'], true);
    }
}
