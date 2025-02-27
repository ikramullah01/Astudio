<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'status'];

    // Relationship: A project can have many dynamic attributes through AttributeValue
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class, 'entity_id');
    }

    // Relationship: A project can have many attributes through AttributeValue
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_values', 'entity_id', 'attribute_id');
    }

    // Relationship: A project can have many users
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    // Relationship: A project can have many timesheets
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }
}
