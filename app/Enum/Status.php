<?php

namespace App\Enum;

abstract class Status {
    const Coming = 0;
    const Happening = 1;
    const Done = 2;
    const Cancelled = 3;
    const Ghosted = 4;
    const NonExistent = 5;
}