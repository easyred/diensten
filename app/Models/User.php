<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name', 'company_name', 'phone', 'whatsapp_number', 'email', 'password',
        'address', 'number', 'postal_code', 'city', 'country', 'role', 'btw_number', 'address_json',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'address_json' => 'array',
        ];
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->exists();
    }

    public function coverages()
    {
        return $this->hasMany(ServiceProviderCoverage::class, 'user_id');
    }

    public function schedule()
    {
        return $this->hasOne(ServiceProviderSchedule::class, 'user_id');
    }

    // Ratings given by this user (as customer)
    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'customer_id');
    }

    // Ratings received by this user (as service provider)
    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'service_provider_id');
    }

    /**
     * Get average rating for service providers
     */
    public function getAverageRatingAttribute()
    {
        if (!in_array($this->role, ['plumber', 'gardener'])) {
            return null;
        }
        
        return $this->ratingsReceived()->avg('rating');
    }

    /**
     * Get total completed services for service providers
     */
    public function getTotalCompletedServicesAttribute()
    {
        if (!in_array($this->role, ['plumber', 'gardener'])) {
            return 0;
        }
        
        return \App\Models\WaRequest::where('selected_plumber_id', $this->id)
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get total services availed for clients
     */
    public function getTotalServicesAvailedAttribute()
    {
        if ($this->role !== 'client') {
            return 0;
        }
        
        return \App\Models\WaRequest::where('customer_id', $this->id)
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Add default municipality coverage for service providers
     */
    public function addDefaultMunicipalityCoverage()
    {
        if (!in_array($this->role, ['plumber', 'gardener']) || !$this->city) {
            return false;
        }

        // Find the user's municipality based on their city (case-insensitive)
        // Try exact match first, then LIKE for variations (e.g., Ghent vs GENT)
        $cityUpper = strtoupper(trim($this->city));
        $userMunicipality = DB::table('postal_codes')
            ->where('Plaatsnaam_NL', $cityUpper)
            ->whereNotNull('Hoofdgemeente')
            ->value('Hoofdgemeente');
        
        // If not found, try LIKE search (handles spelling variations like Ghent/GENT)
        if (!$userMunicipality) {
            $userMunicipality = DB::table('postal_codes')
                ->where('Plaatsnaam_NL', 'LIKE', $cityUpper)
                ->whereNotNull('Hoofdgemeente')
                ->value('Hoofdgemeente');
        }
        
        // If still not found, try matching by postal code if user has one
        if (!$userMunicipality && $this->postal_code) {
            $userMunicipality = DB::table('postal_codes')
                ->where('Postcode', $this->postal_code)
                ->whereNotNull('Hoofdgemeente')
                ->value('Hoofdgemeente');
        }

        if (!$userMunicipality) {
            return false;
        }

        // Get center coordinates of user's municipality
        $center = DB::table('postal_codes')
            ->select('Latitude', 'Longitude')
            ->where('Hoofdgemeente', $userMunicipality)
            ->whereNotNull('Latitude')
            ->whereNotNull('Longitude')
            ->first();

        if (!$center) {
            return false;
        }

        // Find nearby municipalities within 10km
        $nearby = DB::select("
            SELECT DISTINCT Hoofdgemeente,
                (6371 * acos(cos(radians(?)) * cos(radians(Latitude)) * 
                cos(radians(Longitude) - radians(?)) + sin(radians(?)) * 
                sin(radians(Latitude)))) AS distance
            FROM postal_codes
            WHERE Latitude IS NOT NULL 
                AND Longitude IS NOT NULL 
                AND Hoofdgemeente != ?
            HAVING distance <= 10
            ORDER BY distance ASC
            LIMIT 20
        ", [$center->Latitude, $center->Longitude, $center->Latitude, $userMunicipality]);
        
        $nearbyMunicipalities = collect($nearby)->pluck('Hoofdgemeente');

        if ($nearbyMunicipalities->isEmpty()) {
            // Just add the user's own municipality
            ServiceProviderCoverage::firstOrCreate([
                'user_id' => $this->id,
                'hoofdgemeente' => $userMunicipality,
                'coverage_type' => 'municipality'
            ]);
            return true;
        }

        // Add municipalities to coverage (avoid duplicates)
        $existing = ServiceProviderCoverage::where('user_id', $this->id)
            ->pluck('hoofdgemeente')
            ->toArray();

        // Add user's own municipality first
        if (!in_array($userMunicipality, $existing)) {
            ServiceProviderCoverage::create([
                'user_id' => $this->id,
                'hoofdgemeente' => $userMunicipality,
                'coverage_type' => 'municipality'
            ]);
        }

        // Add nearby municipalities
        foreach ($nearbyMunicipalities as $municipality) {
            if (!in_array($municipality, $existing)) {
                ServiceProviderCoverage::create([
                    'user_id' => $this->id,
                    'hoofdgemeente' => $municipality,
                    'coverage_type' => 'municipality'
                ]);
            }
        }

        return true;
    }
}
