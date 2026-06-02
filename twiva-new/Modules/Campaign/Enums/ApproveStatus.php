<?php
namespace Modules\Campaign\Enums;
enum ApproveStatus: int
{
    case Pending = 0;
    case Approved = 1;
    case Rejected = 2;
}
