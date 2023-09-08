<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait TenantAble
{
  public static function bootTenantAble()
  {
    $tenantId = auth()->user()->tenant_id;

    self::creating(function ($model) use ($tenantId) {
      $model->tenant_id = $tenantId;
    });

    self::addGlobalScope(function (Builder $builder) use ($tenantId) {
      $builder->where('tenant_id', $tenantId);
    });
  }
}
