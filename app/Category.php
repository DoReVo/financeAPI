<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Transaction;

class Category extends Model
{
    protected $table = 'finance_category';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
    'category_name'
    ];

    public function transaction()
    {
        return $this->hasOne('Transaction', 'category', 'id');
    }
}
