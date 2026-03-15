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
     * Override the camelCase accessor for the `facility_type` column.
     *
     * Returns an object so that ALL usage patterns work without crashing:
     *
     *   $facility->facilityType->name   → "Hospital"   (used in admin blades)
     *   $facility->facilityType->id     → "hospital"   (used in edit form selected check)
     *   {{ $facility->facilityType }}   → "Hospital"   (__toString called by Blade)
     *   {{ $facility->facility_type }}  → "Hospital"   (Eloquent resolves to same accessor)
     *
     * The raw DB value is always available via:
     *   $facility->getRawOriginal('facility_type')   → "hospital"
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

        $id    = $value ?? '';
        $label = $map[$id] ?? ucfirst(str_replace('_', ' ', $id ?: 'N/A'));

        return new FacilityTypeObject($id, $label);
    }

    /**
     * Mutator — guard against accidentally persisting the object instead of the string.
     */
    public function setFacilityTypeAttribute($value)
    {
        if ($value instanceof FacilityTypeObject) {
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

/**
 * Simple value object returned by the facilityType accessor.
 * Implements __toString so Blade's {{ $facility->facilityType }} never crashes.
 */
class FacilityTypeObject
{
    public string $id;
    public string $name;

    public function __construct(string $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}