<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'facility_type',
        'license_number',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
        'postal_code',
        'phone',
        'email',
        'website',
        'operating_hours',
        'contact_person_name',
        'contact_person_phone',
        'contact_person_email',
        'notes',
        'is_approved',
        'approved_by',
        'approved_at',
        'status',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // ---------- Relationships ----------

    public function users()
    {
        return $this->belongsToMany(User::class, 'facility_users')
            ->withPivot(['position', 'department', 'is_primary_contact'])
            ->withTimestamps();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function specimenRequests()
    {
        return $this->hasMany(SpecimenRequest::class);
    }

    // ---------- Accessors ----------

    /**
     * Override the camelCase accessor for the facility_type column so that
     *   $facility->facilityType->name
     * works in every Blade view without a separate facility_types table.
     *
     * The raw value is still accessible via  $facility->getRawOriginal('facility_type')
     * or  $facility->attributes['facility_type']  which is what forms should use.
     */
    public function getFacilityTypeAttribute($value)
    {
        $map = [
            'hospital'        => 'Hospital',
            'clinic'          => 'Clinic',
            'lab'             => 'Laboratory',
            'research_center' => 'Research Center',
            'other'           => 'Other',
        ];

        $label = $map[$value] ?? ucfirst(str_replace('_', ' ', $value ?? 'N/A'));

        return (object) ['id' => $value, 'name' => $label];
    }

    /**
     * Store the raw enum string; guard against accidentally persisting the object.
     */
    public function setFacilityTypeAttribute($value)
    {
        if (is_object($value) && isset($value->id)) {
            $value = $value->id;
        }
        $this->attributes['facility_type'] = $value;
    }

    // ---------- Scopes ----------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}