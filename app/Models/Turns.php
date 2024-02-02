<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turns extends Model
{
    use HasFactory;

    protected $table = 'turns';

    protected $fillable = [
        'code','window','status',
    ];

}
