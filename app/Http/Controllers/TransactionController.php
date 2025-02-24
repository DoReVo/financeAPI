<?php

namespace App\Http\Controllers;

use App\Category;
use App\Detail;
use App\Item;
use App\Transaction;
use Illuminate\Http\Request;

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
    private $test;

    public function __construct(Request $request)
    {
        try {
            $uri = $request->path();
            $method = $request->method();

            // IF USER IS TRYING TO CREATE NEW TRANSACTION
            // 1. VALIDATE DATA , THROW EXCEPTION IF DATA INVALID
            // Pass a third array argument to validate method to define custom message
            if ($method == "POST" && $uri == "api/transaction") {
                $this->validate(
                    $request,
                    [
                        'date_time' => [
                            'bail',
                            'required',
                            'date',
                        ],
                        'category' => [
                            'bail',
                            'required',
                            'int',
                            function ($attribute, $value, $fail) {
                                $category = new Category;
                                $category = $category->find($value);

                                if (!$category) {
                                    $fail("Category does not exist");
                                }
                            },
                        ],
                        'amount' => [
                            'bail',
                            'required',
                            'numeric',
                            function ($attribute, $value, $fail) {
                                if (!is_int((INT) $value) || !is_double((DOUBLE) $value) || !is_float((FLOAT) $value)) {
                                    $fail($attribute . " is neither double or int");
                                }
                            },
                        ],
                        'item' => [
                            'sometimes',
                        ],
                        'item.*.item_name' => [
                            'bail',
                            'required',
                            'string',
                        ],
                        'item.*.item_amount' => [
                            'bail',
                            'required',
                            'numeric',
                            'int',
                            'gte:0',
                        ],
                        'item.*.unit_price' => [
                            'bail',
                            'required',
                            'numeric',
                            'gte:0',
                        ],
                        'detail' => [
                            'bail',
                            'sometimes',
                            'string',
                            'nullable',
                        ],
                    ]
                );

                $dateTime = date_create($request->date_time);
                $this->date = date_format($dateTime, 'Y:m:d G:i:s');
                $this->category = $request->category;
                $this->amount = $request->amount;
                $this->detail = $request->detail;
                $this->item = $request->item;
            }

            // If user is adding item for existing transaction
            if ($method == 'POST' && preg_match('/api\/transaction\/\d+\/item/', $uri)) {
                $this->validate(
                    $request,
                    [

                        'item_name' => [
                            'bail',
                            'required',
                            'string',
                        ],
                        'item_amount' => [
                            'bail',
                            'required',
                            'numeric',
                            'int',
                            'gte:0',
                        ],
                        'unit_price' => [
                            'bail',
                            'required',
                            'numeric',
                            'gte:0',
                        ],
                    ]
                );
            }

            // If user is adding category
            if ($method == 'POST' && $uri == ('api/category')) {
                $this->validate(
                    $request,
                    [
                        'category_name' => [
                            'required',
                            'string',
                        ],
                    ]
                );

                $this->userInput = $request->category_name;
            }

            // If user is editing transaction
            if ($method == 'PATCH' && preg_match('/api\/transaction\/\d+\/(date_time|category|amount)/', $uri)) {
                // If user wants to change date_time
                if ($request->route('column') == 'date_time') {
                    $this->validate(
                        $request,
                        [
                            'data' => [
                                'bail',
                                'required',
                                'date',
                            ],
                        ]
                    );

                    $dateTime = date_create($request->data);
                    $this->userInput = date_format($dateTime, 'Y:m:d G:i:s');
                } elseif ($request->route('column') == 'category') {
                    // If user wants to change category
                    $this->validate(
                        $request,
                        [
                            'data' => [
                                'bail',
                                'required',
                                'int',
                                function ($attribute, $value, $fail) {
                                    $category = new Category;
                                    $category = $category->find($value);

                                    if (!$category) {
                                        $fail("Category does not exist");
                                    }
                                },

                            ],
                        ]
                    );
                    $this->userInput = (INT) $request->data;
                } elseif ($request->route('column') == 'amount') {
                    // If user wants to change amount
                    $this->validate(
                        $request,
                        [
                            'data' => [
                                'bail',
                                'required',
                                'numeric',
                                function ($attribute, $value, $fail) {
                                    if (!is_int((INT) $value) || !is_double((DOUBLE) $value) ||
                                        !is_float((FLOAT) $value)) {
                                        $fail($attribute . " is neither double or int");
                                    }
                                },
                            ],
                        ]
                    );
                    $this->userInput = (DOUBLE) $request->data;
                }

                // Id of transaction
                $this->id = $request->route('id');
                // Which column date_time, category or amount
                $this->column = $request->route('column');
            }

            // If user is editing transaction detail
            if ($method == 'PATCH' && preg_match('/api\/transaction\/\d+\/detail/', $uri)) {
                $this->validate(
                    $request,
                    [
                        'data' => [
                            'bail',
                            'required',
                            'string',
                            'nullable',
                        ],
                    ]
                );

                $this->detail = $request->data;
                // $this->id = $request->id;
                // $this->id = $request->route('id');
            }
            // If user is editing item details
            if ($method == 'PATCH' &&
                preg_match('/api\/transaction\/\d+\/item\/\d+\/(item_name|item_amount|unit_price)/', $uri)) {
                if ($request->route('column') == 'item_name') {
                    $this->validate(
                        $request,
                        [
                            'data' =>
                            [
                                'bail',
                                'required',
                                'string',
                            ],
                        ]
                    );
                    $this->userInput = (STRING) $request->data;
                } elseif ($request->route('column') == 'item_amount') {
                    $this->validate(
                        $request,
                        [
                            'data' =>
                            [
                                'bail',
                                'required',
                                'numeric',
                                'int',
                                'gte:0',
                            ],
                        ]
                    );
                    $this->userInput = (INT) $request->data;
                } elseif ($request->route('column') == 'unit_price') {
                    $this->validate(
                        $request,
                        [
                            'data' =>
                            [
                                'bail',
                                'required',
                                'numeric',
                                'gte:0',
                            ],
                        ]
                    );
                    $this->userInput = (DOUBLE) $request->data;
                }
                $this->column = $request->route('column');
            }

            // If user is editing category
            if ($method == 'PATCH' && $uri == preg_match('/api\/category\/\d+/', $uri)) {
                $this->validate(
                    $request,
                    ['data' =>
                        'required',
                        'string',
                    ]
                );
            }
        } catch (\ValidationException $th) {
            return response($th->getMessage(), 422);
        }
    }

    public function getAllTransaction()
    {
        try {
            $transaction = new Transaction;
            $transaction = $transaction->with(['detail', 'item'])->get();

            return response(
                json_encode($transaction),
                200
            );
        } catch (\Throwable $th) {
            return response(
                json_encode(
                    array(
                        'message' => 'Failed to retrieve transactions',
                    )
                ),
                400
            );
        }
    }

    public function getOneTransaction($id)
    {
        try {
            $transaction = new Transaction;
            $transaction = $transaction->with(['detail', 'item'])->findOrFail($id);

            return response(
                json_encode($transaction),
                200
            );
        } catch (\Throwable $th) {
            return response(
                json_encode(
                    array(
                        'message' => 'Failed to retrieve transaction',
                    )
                ),
                400
            );
        }
    }

    public function createTransaction(Request $request)
    {
        try {
            $transaction = new Transaction;

            //SAVE TO DATABASE
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

            $transaction = $transaction->with(['detail', 'item'])->findOrFail($transaction->id);

            return response(
                json_encode($transaction),
                201
            );
        } catch (\Throwable $th) {
            return response(
                json_encode(
                    array(
                        'message' => 'Failed to create transaction',
                    )
                ),
                400
            );
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
                    'unit_price' => $request->unit_price,
                ]
            );

            return response(
                json_encode($item),
                201
            );
        } catch (\Throwable $th) {
            return response(
                json_encode(
                    array(
                        'message' => 'Failed to create transaction item',
                    )
                ),
                400
            );
        }
    }

    public function createCategory(Request $request)
    {
        try {
            $category = new Category;
            $category->category_name = $this->userInput;

            $category->save();

            return response(
                json_encode($category),
                201
            );
        } catch (\Throwable $th) {
            return response(
                json_encode(array(
                    'message' => 'Failed to create category',
                )),
                400
            );
        }
    }

    public function deleteTransaction($id)
    {
        $transaction = new Transaction;

        try {
            $transaction = $transaction->with(['detail', 'item'])->findOrFail($id);

            // FIND MORE ELEGANT CODE LATER
            $transaction->delete();
            $transaction->detail()->delete();
            $transaction->item()->delete();

            return response(json_encode(array('message' => "Delete Successfully")), 200);
        } catch (\Throwable $th) {
            return response(
                json_encode(
                    array(
                        'message' => 'Failed to delete transaction',
                    )
                ),
                400
            );
        }
    }

    public function deleteCategory($id)
    {
        $category = new Category;

        try {
            $category = $category->find($id);

            $category->delete();

            return response(
                json_encode(
                    array(
                        'message' => 'Delete successfully',
                    )
                ),
                200
            );
        } catch (\Throwable $th) {
            return response(
                json_encode(
                    array(
                        'message' => 'Failed to delete category',
                    )
                ),
                400
            );
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
                return response(json_encode('Transaction ID does not exist'), 404);
            }

            // Set new data on user defined column based on route
            $transaction[$this->column] = $this->userInput;

            $transaction->save();

            // Return updated value only
            // $updatedValue[$this->column] = $transaction[$this->column];

            return response(json_encode($transaction), 200);
            // return response($request->path());
        } catch (\Throwable $th) {
            return response(
                json_encode(
                    array(
                        'message' => 'Failed to edit transaction',
                    )
                ),
                400
            );
        }
    }

    // Transaction Detail editing
    public function editTransactionDetail(Request $request, $id)
    {
        try {
            $this->id = $id;
            $transaction = new Transaction;
            $transaction = $transaction->find($id);

            // If Id does not exist
            if (!$transaction) {
                return response(json_encode('Transaction ID does not exist'), 404);
                // return $this->id;
            }

            $detail = new Detail;
            $detail = $detail->find($id);

            $detail->detail = $this->detail;
            $detail->save();

            return response(json_encode($detail), 200);
        } catch (\Throwable $th) {
            return response(
                json_encode(
                    array(
                        'message' => 'Failed to edit transaction detail',
                    )
                ),
                400
            );
        }
    }

    public function editTransactionItem(Request $request, $id, $itemId, $column)
    {
        try {
            $transaction = new Transaction;
            $transaction = $transaction->find($id);

            // If Transaction ID does not exist
            if (!$transaction) {
                return response(
                    json_encode(
                        array(
                            'message' => 'Transaction ID does not exist',
                        )
                    ),
                    404
                );
            }

            $item = new Item;
            $item = $item->find($itemId);

            // If item ID does not exist
            if (!$item) {
                return response(
                    json_encode(
                        array(
                            'message' => 'Item ID does not exist',
                        )
                    ),
                    404
                );
            }

            // Set item detail to new value
            $item[$this->column] = $this->userInput;
            $item->save();

            // Return success
            return response(
                json_encode(
                    $item
                ),
                200
            );
        } catch (\Throwable $th) {
            return response(
                json_encode(
                    array(
                        'message' => 'Failed to edit item details',
                    )
                ),
                400
            );
        }
    }

    public function editCategory(Request $request, $id)
    {

        try {
            $category = new Category;
            $category = $category->find($id);

            if (!$category) {
                return response(
                    json_encode(array('message' => 'Category ID does not exist')),
                    404
                );
            }

            $category->category_name = $request->data;

            $category->save();

            return response(json_encode($category), 200);
        } catch (\Throwable $th) {
            return response(json_encode(array('message' => 'Failed to edit category name')));
            // return $th->getMessage();
            // return $request->data;
        }
    }

    // Unused for now
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
