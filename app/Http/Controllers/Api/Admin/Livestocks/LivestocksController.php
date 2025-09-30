<?php

namespace App\Http\Controllers\Api\Admin\Livestocks;

use App\Http\Controllers\Api\Helpers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Livestock;
use Illuminate\Support\Facades\Validator;

class LivestocksController extends BaseController

{
    //
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();


        $area = Livestock::create($input);
        $success['name'] =  $area->name;

        return $this->sendResponse($success, 'Area created successfully.');
    }

    public function index(Request $request)
    {
        $filter = $request->all();

        $data = Livestock::all();

        return $this->sendResponse($data, 'Supervisor retrieved successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $data = Livestock::findOrFail($id);

        if ($data->null) {
            return $this->sendError('Data not found!', []);
        } else {
            $data->delete();
            return $this->sendResponse($data, 'Data deleted successfully.');
        }
    }   
    
    public function update(Request $request, $id)
    {
        $data = Livestock::find($id);
        $data->update($request->all());
        return $this->sendResponse($data, 'Data updated successfully.');
    }

}
