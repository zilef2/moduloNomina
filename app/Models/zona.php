<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

    class zona extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'nombre', 'nombre2', 'codigo'];

}
