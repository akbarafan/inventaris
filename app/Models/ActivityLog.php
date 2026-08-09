<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'model_type', 'model_id', 'action',
        'description', 'properties', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function log($action, $description = null, $model = null, $properties = [])
    {
        try {
            return static::create([
                'user_id' => auth()->id(),
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model ? $model->getKey() : null,
                'action' => $action,
                'description' => $description,
                'properties' => $properties,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}