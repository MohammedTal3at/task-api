<?php

namespace App\Enums;

enum TaskLogOperationType: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case STATUS_CHANGED = 'status_changed';
    case RESTORED = 'restored';
}
