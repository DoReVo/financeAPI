<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Transaction;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detail extends Model
{
    use SoftDeletes;
    protected $table = 'finance_transaction_detail';
    protected $primaryKey = 'transaction_id';

    public $incrementing = false;
    
    const CREATED_AT = "record_date";
    const UPDATED_AT = 'update_date';

    protected $fillable = [
    // 'transaction_id',
    'detail'
    ];

    public function transaction()
    {
        return $this->belongsTo('Transaction', 'transaction_id', 'id');
    }
}
