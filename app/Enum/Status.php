<?php

namespace App\Enum;

enum Status : int {
    case CancelRequest = -1;
    case Coming = 0;
    case Happening = 1;
    case Done = 2;
    case Cancelled = 3;
    case Ghosted = 4;
    case NonExistent = 5;
}