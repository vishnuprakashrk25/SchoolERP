<?php

namespace App\Models;

/**
 * Class BehaviourRecord
 * Alias of StudentIncident for student remarks / behaviour points
 */
class BehaviourRecord extends StudentIncident
{
    protected $table = 'student_incidents';
}
