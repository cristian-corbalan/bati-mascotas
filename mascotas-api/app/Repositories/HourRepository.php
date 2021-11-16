<?php

namespace App\Repositories;

use App\Models\Hour;
use Illuminate\Database\Eloquent\Collection;

interface HourRepository
{
    /**
     * Returns all the hours in the database.
     *
     * @return Collection
     */
    public function getAll(): Collection;
}
