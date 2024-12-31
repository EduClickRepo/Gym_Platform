<?php

namespace App\Utils;

enum PayStatusEnum: string
{
    case APPROVED = 'APPROVED';
    case PENDING = 'PENDING';
    case DECLINED = 'DECLINED';
    case ERROR = 'ERROR';
}

