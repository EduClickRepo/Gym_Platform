<?php

namespace App\Utils;

enum FeaturesEnum: string
{
    case SEE_PETTY_CASH  = 'SEE_PETTY_CASH';
    case SAVE_PETTY_CASH  = 'SAVE_PETTY_CASH';
    case SEE_MAYOR_CASH  = 'SEE_MAYOR_CASH';
    case SEE_USERS = 'SEE_USERS';
    case SEE_ACHIEVEMENTS_WEEKS_RANK = 'SEE_ACHIEVEMENTS_WEEKS_RANK';
    case SEE_USERS_GENERAL_INFO = 'SEE_USERS_GENERAL_INFO';
    case SEE_ATTENDEES = 'SEE_ATTENDEES';
    case SEE_EXPIRED_PLANS = 'SEE_EXPIRED_PLANS';
    case SAVE_SESSION = 'SAVE_SESSION';
    case SEE_CLIENT_FOLLOW_UP = 'SEE_CLIENT_FOLLOW_UP';
    case CHANGE_CLIENT_FOLLOWER = 'CHANGE_CLIENT_FOLLOWER';
    case SEE_USERS_MEDICAL_INFO = 'SEE_USERS_MEDICAL_INFO';
    case LOAD_CLIENT_PLAN = 'LOAD_CLIENT_PLAN';
    case MAKE_WELLBEING_TEST = 'MAKE_WELLBEING_TEST';
    case SEE_EXTENDED_SCHEDULE = 'SEE_EXTENDED_SCHEDULE';
    case CHANGE_TRANSACTION_CATEGORY = 'CHANGE_TRANSACTION_CATEGORY';

    case schedule_until = 'schedule_until';
}