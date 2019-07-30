<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Transaction;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;
    protected $table = 'finance_transaction_item';
    protected $primaryKey = 'id';
    
    const CREATED_AT = "record_date";
    const UPDATED_AT = 'update_date';

    protected $fillable = [
    'transaction_id',
    'item_name',
    'item_amount',
    'unit_price'
    ];

    public function transaction()
    {
        return $this->belongsTo('Transaction', 'transaction_id', 'id');
    }
}
