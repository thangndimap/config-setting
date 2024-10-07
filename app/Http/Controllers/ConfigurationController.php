<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $input = $request->only('filter','page', 'limit');
        $filterParams = \Arr::WhereNotNull($input['filter']??[]);
        $rows = (new Configuration)->all($filterParams,['pagination' => 1]);
        $data = array(
            'pagination' => ($rows instanceof \Illuminate\Pagination\Paginator) ? $rows->setPath(\URL::current())->appends($input)->links() : '',
            'rows' => $rows,
            'page' => $input['page']??1,
            'filter' => $input['filter'] ?? []
        );
        return view('configuration.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'action' => route('configuration.store'),
            'method' => 'post',
        ];
        return view('configuration.form_all', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->only('config', 'module');
        $validator = \Validator::make($input, [
            'module' => 'required',
            'config' => 'required',
            'config.*.key' => 'required',
            'config.*.name' => 'required',
            'config.*.type_input' => 'required',
            'config.*.status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }
        $configs = array_values($input['config']);
        $keys = \Arr::pluck($configs, 'key');
        $checkKey = findDuplicates($keys);
        $model = (new Configuration());
        if (!empty($checkKey)) {
            return response()->json(['status' => 'error', 'message' => 'Key '.$checkKey[0].' đang bị trùng'], 200);
        }
        try {
            foreach($configs as $key => $item) {
                if (in_array($item['type_input'], ['select', 'checkbox']) && empty($item['option_value'])) {
                    return response()->json(['status' => 'error', 'message' => 'Trường Giá trị thứ '.$key+1 .' là bắt buộc'], 200);
                }
                if (!empty($item['config_id'])) {
                    $data_update = [
                        "name" => $item['name'],
                        "key" => $item['key'],
                        "type_input" =>  $item['type_input'],
                        "value" => $item['value'] ?? null,
                        "is_required" => $item['is_required'] ?? 0,
                        "status" => $item['status'] ?? 'active'
                    ];
                    if (!empty($item['option_value']) && $item['type_input'] == 'select') {
                        $data_update['option_value'] = $item['option_value'];
                        $item['option_value'] = explode("\r\n", $item['option_value']);
                        $item['option_value'] = array_map("trim", $item['option_value']);
                        $item['option_value'] = array_map("trim", $item['option_value']);
                        $item['option_value'] = array_filter(array_map('trim', $item['option_value']), function($value) {
                            return !is_null($value) && $value !== '';
                        });
                        $item['option_value'] = array_values(array_unique($item['option_value']));
                        $data_update['select_value'] = $item['option_value'];
                    }
                    $model->update((int)$item['config_id'], $data_update);
                } else {
                    $data = [
                        "name" => $item['name'],
                        "key" => $item['key'],
                        "type_input" =>  $item['type_input'],
                        "value" => $item['value'] ?? null,
                        'module' => $input['module'],
                        "is_required" => $item['is_required'] ?? 0,
                        "status" => $item['status'] ?? 'active'
                    ];
                    if (!empty($item['option_value']) && in_array($item['type_input'], ['select', 'checkbox'])) {
                        $data['option_value'] = $item['option_value'];
                        $item['option_value'] = explode("\r\n", $item['option_value']);
                        $item['option_value'] = array_map("trim", $item['option_value']);
                        $item['option_value'] = array_map("trim", $item['option_value']);
                        $item['option_value'] = array_filter(array_map('trim', $item['option_value']), function($value) {
                            return !is_null($value) && $value !== '';
                        });
                        $item['option_value'] = array_values(array_unique($item['option_value']));
                        $data['select_value'] = $item['option_value'];
                    }
                    $data_create[] = $data;
                }
            }
            if (!empty($data_create)) {
                $model->createBatch($data_create);
            }
            
            return response()->json(['status' => 'success', 'message' => 'Thêm mới thành công'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $model = new Configuration();
        $row = $model->detail($id);
        if (empty($row)) {
            return abort(404);
        }
        
        $data = [
            'row' => $row,
            'action' => route('configuration.update', [$id]),
            'method' => 'PUT',
        ];
        return view('configuration.form', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $input = $request->only('name', 'value', 'key', 'type_input', 'option_value', 'is_required', 'status');
        $validator = \Validator::make($input, [
            'name' => 'required',
            'key' => 'required',
            'type_input' => 'required',
            'option_value' => Rule::requiredIf(in_array($input['type_input'], ['select', 'checkbox'])),
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }
        $model = new Configuration();
        $row = $model->detail($id);
        if (empty($row)) {
            return response()->json(['status' => 'error', 'message' => 'Cấu hình không tồn tại']);
        }
        $check = $model->all(['key' => $input['key'], 'module' => $row['module']])->first();
        if (!empty($check) && $check['_id'] != $id) {
            return response()->json(['status' => 'error', 'message' => 'Key đang được sử dụng trong module này']);
        }
        $dataUpadte = [
            "name" => $input['name'],
            "key" => $input['key'],
            "type_input" =>  $input['type_input'],
            "value" => $input['value'] ?? null,
            "is_required" => $input['is_required'] ?? 0,
            'status' => $input['status'] ?? 0,
        ];
        if (!empty($input['option_value']) && in_array($input['type_input'], ['select', 'checkbox'])) {
            $dataUpadte['option_value'] = $input['option_value'];
            $input['option_value'] = explode("\r\n", $input['option_value']);
            $input['option_value'] = array_map("trim", $input['option_value']);
            $input['option_value'] = array_map("trim", $input['option_value']);
            $input['option_value'] = array_filter(array_map('trim', $input['option_value']), function($value) {
                return !is_null($value) && $value !== '';
            });
            $input['option_value'] = array_values(array_unique($input['option_value']));
            $dataUpadte['select_value'] = $input['option_value'];
        }
        
        $result = $model->update($id, $dataUpadte);
        $data = [
            'status' => 'success',
            'message' => 'Cập nhật thành công!',
        ];
        if (!$result) {
            $data = [
                'status' => 'error',
                'message' => 'Cập nhật thất bại!',
            ];
        }
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $model = new Configuration();
        
        $data = [
            'status' => 'success',
            'message' => 'Xóa thành công!',
        ];
        $result = $model->remove($id);
        if (empty($result)) {
            $data = [
                'status' => 'error',
                'message' => 'Xóa thất bại!',
            ];
        }
        return response()->json($data, 200);
    }
}
