<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'breed',
        'date_of_birth',
        'name',
        'neutered',
        'temperament',
        'observation',
        'images_id',
        'sexes_id',
        'species_id',
    ];

    /**
     * Returns the pet's sex
     *
     * @return BelongsTo
     */
    public function sex(): BelongsTo
    {
        return $this->belongsTo(Sex::class, 'sexes_id');
    }

    /**
     * Returns the pet's species
     *
     * @return BelongsTo
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    } 

    /**
     * Returns the pet's image
     *
     * @return BelongsTo
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'images_id');
    } 

  

    /**
     * Returns pet owners
     *
     * @return BelongsToMany
     */
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_has_pets', 'pet_id', 'user_id');
    }
}
