<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSale extends Model
{
    protected $fillable = [
        'lead_id',
        'sales_person_id',
        'applicant_name',
        'applicant_number',
        'project_name',
        'unit_number',
        'project_category',
        'project_sub_category',
        'dob',
        'doa',
        'email',
        'permanent_address',
        'current_address',
        'checklist',
        'user_id'
    ];

    protected $casts = [
        'dob' => 'date',
        'doa' => 'date',
        'checklist' => 'array'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function salesPerson()
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}