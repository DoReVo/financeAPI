<?php

namespace App;

use App\Category;
use App\Detail;
use App\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    protected $table = 'finance_transaction';
    protected $primaryKey = 'id';

    const CREATED_AT = "record_date";
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'date_time',
        'amount',
    ];

    public function category()
    {
        return $this->belongsTo('App\Category', 'category', 'id');
    }

    public function item()
    {
        return $this->hasMany('App\Item', 'transaction_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne('App\Detail', 'transaction_id', 'id');
    }
}
