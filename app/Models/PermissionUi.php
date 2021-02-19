<?php

namespace App\Models;

use App\Interfaces\UiInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission;

class PermissionUi extends Permission implements UiInterface
{
    use HasFactory;
}
