<?php

namespace App\Enum;

enum ApprovalStatus : int {
    case Pending = 10;
    case Approved = 11;
    case Rejected = 12;
}