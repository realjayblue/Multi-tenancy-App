<?php

namespace App\Models;

use App\Traits\TenantAble;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory, TenantAble;

    protected $fillable = ['name', 'tenant_id'];
}
