<?php


namespace App\Http\Controllers;

use App\Transaction;
use App\Category;
use App\Detail;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    private $_date;
    private $_amount;
    private $_category;
    private $_item;
    private $_detail;
    private $_column;
    private $_id;
    private $_userInput;

    public function __construct(Request $request)
    {
        /**
         * @__construct
         *
         * DO SOMETHING
         */

        try {
            $uri = $request->path();
            $method = $request->method();

            // IF USER IS TRYING TO CREATE NEW TRANSACTION
            // 1. VALIDATE DATA , THROW EXCEPTION IF DATA INVALID
            if ($method == "POST" && $uri == "api/transaction") {
                $this->validate(
                    $request,
                    [
                        'date_time'=>[
                            'bail',
                            'required',
                            'date'
                        ],
                        'category'=>[
                            'bail',
                            'required',
                            'int',
                            function ($attribute, $value, $fail) {
                                $category = new Category;
                                $category = $category->find($value);

                                if (!$category) {
                                    $fail("Category does not exist");
                                }
                            }
                        ],
                        'amount' =>[
                            'bail',
                            'required',
                            'numeric',
                            function ($attribute, $value, $fail) {
                                if (!is_int((INT)$value) || !is_double((DOUBLE)$value) || !is_float((FLOAT)$value)) {
                                    $fail($attribute." is neither double or int");
                                }
                            }
                        ],
                        'item' =>[
                            'sometimes',
                        ],
                        'item.*.item_name' =>[
                            'bail',
                            'required',
                            'string'
                        ],
                        'item.*.item_amount'=>[
                            'bail',
                            'required',
                            'numeric',
                            'int',
                            'gte:0'
                        ],
                        'item.*.unit_price'=>[
                            'bail',
                            'required',
                            'numeric',
                            'gte:0'
                        ],
                        'detail'=>[
                            'bail',
                            'sometimes',
                            'string',
                            'nullable'
                        ]
                    ]
                );

                $dateTime = date_create($request->date_time);
                $this->_date = date_format($dateTime, 'Y:m:d G:i:s');
                $this->_category = $request->category;
                $this->_amount = $request->amount;
                $this->_detail = $request->detail;
                $this->_item = $request->item;
            }

            if ($method=='PATCH') {
                $this->_id = $request->route('id');
                $this->_column = $request->route('column');
                $this->_userInput = $request->data;
            }
        } catch (\ValidationException $th) {
            return response($th->getMessage(), 422);
        }
    }

    public function getAllTransaction()
    {
        $transaction = new Transaction;
        $transaction = $transaction->with(['detail','item'])->get();
        
        return($transaction);
    }

    public function getOneTransaction($id)
    {
        $transaction = new Transaction;

        try {
            $transaction = $transaction->with(['detail','item'])->findOrFail($id);
            return(
                $transaction
            );
        } catch (\Throwable $th) {
            return response("Not Found", 404);
        }
    }

    public function createTransaction(Request $request)
    {
        try {
            $transaction = new Transaction;

            // 5. SAVE TO DATABASE
            $transaction->date_time = $this->_date;
            $transaction->category = $this->_category;
            $transaction->amount = $this->_amount;

            // finance_transaction table
            $transaction->save();

            // finance_transaction_detail table
            if ($this->_detail) {
                $detail = new Detail;
                $detail->detail = $this->_detail;
                $transaction->detail()->save($detail);
            }

            //finance_transaction_item table

            if ($this->_item) {
                $itemArray = array();
    
                foreach ($this->_item as $rowKey => $rowValue) {
                    $item = new Item;
                    foreach ($this->_item[$rowKey] as $colKey => $colValue) {
                        $item->$colKey = $colValue;
                    }
                    array_push($itemArray, $item);
                }
    
                $transaction->item()->saveMany($itemArray);
            }

            return $transaction;
        } catch (\Throwable $th) {
            return($th->getMessage());
        }
    }

    public function deleteTransaction($id)
    {
        $transaction = new Transaction;

        try {
            $transaction = $transaction->with(['detail','item'])->findOrFail($id);

            // FIND MORE ELEGANT CODE LATER
            $transaction->delete();
            $transaction->detail()->delete();
            $transaction->item()->delete();
            return response("Delete Successfully");
        } catch (\Throwable $th) {
            return $th->getMessage();
            // return response("Fail To Delete");
        }
    }

    public function editTransaction(Request $request)
    {

        // 1.ERROR HANDLING FOR DELETED RECORD
        

        try {
            if (!is_numeric($request->route('id'))) {
                return response('ID IS NOT NUMB', 400);
            }

            // return response($this->_column, 200);

            $transaction = new Transaction;
            
            $transaction = $transaction->findOrFail($this->_id);


            $transaction[$this->_column] = $request->amount;

            // if ($column == 'date_time') {
            //     $transaction->date_time = $this->_date;
            // } elseif ($column=='amount') {
            //     $transaction->amount = $request->amount;
            // }
            
            $transaction->save();

            return response($transaction);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function replaceTransaction($id, Request $request)
    {
        // 1.ERROR HANDLING FOR DELETED RECORD
        $transaction = new Transaction;

        try {
            $transaction = $transaction->findOrFail($id);

            $transaction->update(
                ['date_time' => $this->_date,
                'amount' => $request->amount]
            );

            return response($transaction);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
