<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Tag
 * 
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * 
 * @package App\Models
 */
class Tag extends Model
{
    use HasFactory;
}
