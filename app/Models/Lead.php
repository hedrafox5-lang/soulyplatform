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
        $foreignKey = 'interested_service_id';

        if (class_exists(\App\Models\Service::class)) {
            return $this->belongsTo(\App\Models\Service::class, $foreignKey)->withDefault([
                'title_ar' => 'خدمة عامة / استشارة تقنية',
                'name'     => 'General Service',
                'price'    => 0.00,
            ]);
        }

        if (class_exists(\App\Models\CatalogItem::class)) {
            return $this->belongsTo(\App\Models\CatalogItem::class, $foreignKey)->withDefault([
                'name_ar' => 'خدمة عامة / استشارة تقنية',
                'name'    => 'General Service',
                'price'   => 0.00,
            ]);
        }

        return $this->belongsTo(\App\Models\Service::class, $foreignKey)->withDefault();
    }

    /**
     * متوافق مع الاستدعاءات التبادلية باسم interestedService
     */
    public function interestedService()
    {
        return $this->service();
    }

    /**
     * علاقة المستخدم في حال تم تحويل العميل المحتمل إلى حساب عميل نشط
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
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