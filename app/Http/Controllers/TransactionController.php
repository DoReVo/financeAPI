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
    private $date;
    private $amount;
    private $category;
    private $item;
    private $detail;
    private $column;
    private $id;
    private $userInput;


    public function __construct(Request $request)
    {

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
                $this->date = date_format($dateTime, 'Y:m:d G:i:s');
                $this->category = $request->category;
                $this->amount = $request->amount;
                $this->detail = $request->detail;
                $this->item = $request->item;
            }

            if ($method=='PATCH') {
                $this->id = $request->route('id');
                $this->column = $request->route('column');
                $this->userInput = $request->data;
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
            $transaction->date_time = $this->date;
            $transaction->category = $this->category;
            $transaction->amount = $this->amount;

            // finance_transaction table
            $transaction->save();

            // finance_transaction_detail table
            
                $detail = new Detail;
            $detail->detail = $this->detail ? $this->detail : null;
                $transaction->detail()->save($detail);
            

            //finance_transaction_item table

            if ($this->item) {
                $itemArray = array();
    
                foreach ($this->item as $rowKey => $rowValue) {
                    $item = new Item;
                    foreach ($this->item[$rowKey] as $colKey => $colValue) {
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

    public function createTransactionItem(Request $request, $id)
    {
        try {
            $item = new Item;
            $item = $item->create(
                [
                    'transaction_id' => $id,
                    'item_name' => $request->item_name,
                    'item_amount' => $request->item_amount,
                    'unit_price' => $request->unit_price
                ]
            );

            return response($item, 200);
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
        try {
            // Find if ID exist
            $transaction = new Transaction;
            $transaction = $transaction->find($this->id);

            // Return error if ID not exist
            if (!$transaction) {
                return response(json_encode('ID does not exist'), 422);
            }

            // Set new data on user defined column based on route
            $transaction[$this->column] = $this->userInput;

            $transaction->save();
        
            // Return updated value only
            $updatedValue[$this->column] = $transaction[$this->column];

            return response($updatedValue);
            // return response($request->path());
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
            }

    // Transaction Detail editing
    public function editTransactionDetail(Request $request, $id)
    {
        $this->id = $id;
            $transaction = new Transaction;
        $transaction = $transaction->find($id);
            
        // If Id does not exist
        if (!$transaction) {
            return response(json_encode('ID does not exist'), 422);
            // return $this->id;
        }

        $detail = new Detail;
        $detail = $detail->find($id);
            
        $detail->detail = $this->detail;
        $detail->save();

        return response($detail);
    }

    public function replaceTransaction($id, Request $request)
    {
        // 1.ERROR HANDLING FOR DELETED RECORD
        $transaction = new Transaction;

        try {
            $transaction = $transaction->findOrFail($id);

            $transaction->update(
                ['date_time' => $this->date,
                'amount' => $request->amount]
            );

            return response($transaction);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
