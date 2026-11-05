<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
        'is_read'  => 'boolean',
    ];

    /**
     * علاقة الخدمة المرتبطة بطلب العميل المحتمل
     */
    public function service()
    {
        if (class_exists(\App\Models\Service::class)) {
            return $this->belongsTo(\App\Models\Service::class, 'service_id')->withDefault([
                'title_ar' => 'خدمة عامة / استشارة تقنية',
                'name'     => 'General Service',
                'price'    => 0.00,
            ]);
        }

        if (class_exists(\App\Models\CatalogItem::class)) {
            return $this->belongsTo(\App\Models\CatalogItem::class, 'service_id')->withDefault([
                'name_ar' => 'خدمة عامة / استشارة تقنية',
                'name'    => 'General Service',
                'price'   => 0.00,
            ]);
        }

        return $this->belongsTo(\App\Models\Service::class, 'service_id')->withDefault();
    }

    /**
     * علاقة المستخدم في حال تم تحويل العميل المحتمل إلى حساب عميل نشط
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(\App\Models\LeadActivity::class, 'lead_id');
    }

    public function conversations()
    {
        return $this->hasMany(\App\Models\AiConversation::class, 'lead_id');
    }
}