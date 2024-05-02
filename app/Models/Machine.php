<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Machine extends Model
{
    protected $fillable = ['name', 'price', 'status', 'last_active', 'machine_id'];
}
