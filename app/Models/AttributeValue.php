<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_id', 'entity_id', 'value'];

    // Relationship: An attribute value belongs to a project
    public function project()
    {
        return $this->belongsTo(Project::class, 'entity_id');
    }

    // Relationship: An attribute value belongs to an attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
