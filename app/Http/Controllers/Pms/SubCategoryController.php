<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Imports\CategoryImport;
use App\Models\PmsModels\Category;
use App\Models\PmsModels\RequisitionType;
use App\Models\PmsModels\Warehouses;
use App\Models\PmsModels\CategoryDepartment;
use App\Models\Hr\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

use \App\Models\PmsModels\Attribute;
use \App\Models\PmsModels\AttributeOption;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Sub Category';

            $categories = Category::with('requisitionType')
            ->doesntHave('category')->orderby('code', 'asc')->get();

            $requisitions = RequisitionType::all();
            $departments= Department::all();
            $code=uniqueCode(7,'CT-','categories','id');
            return view('pms.backend.pages.sub-category.index', compact('title', 'categories','requisitions','departments','code'));
        }catch (\Throwable $th){

            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::findOrFail(request()->get('category_id'));
        $data = [
            'subcategory' => $category,
            'categoryAttributes' => Attribute::whereHas('options', function($query) use($category){
                return $query->whereIn('id', isset(json_decode($category->attributes, true)[0]) ? json_decode($category->attributes, true) : []);
            })->pluck('id')->toArray(),
            'categoryAttributeOptions' => json_decode($category->attributes, true),
            'attributes' => Attribute::has('options')->get(),
        ];

        return view('pms.backend.pages.sub-category.attributes', $data);
    }

    public function updateAttributes(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            
            $attributeOptions = [];
            if(isset($request->productAttributes[0])){
                foreach ($request->productAttributes as $key => $attribute_id) {
                    array_push($attributeOptions, array_values($request->attributeOptions[$attribute_id]));
                }
            }

            $category->attributes = json_encode(isset($attributeOptions[0]) ? call_user_func_array('array_merge', array_values($attributeOptions)) : []);
            $category->save();

            return $this->backWithSuccess('Sub Category Attributes have benn updated. ');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => ['required', 'string', 'max:255', 'unique:categories'],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            // 'requisition_type_id' => ['required', 'integer'],
        ]);
        try {
            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);

            $category=Category::create($inputs);

            $departments = CategoryDepartment::where('category_id', $request->parent_id)->pluck('hr_department_id')->toArray();
            $category->department()->sync($departments);

            return $this->backWithSuccess('Sub Category created successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($category_id)
    {
        try {
            $category = Category::findOrFail($category_id);
            $category->src = route('pms.product-management.sub-category.update', $category->id);
            $category->req_type = 'put';
            $category->parent_id = !$category->category?null:$category->category;

            $array=[];
            foreach($category->departmentsList as $key => $department){
                array_push($array,$department->hr_department_id);
            }

            $new_array=[];

            foreach(Department::whereIn('hr_department_id',$array)->select('hr_department_id')->get() as $values){
                array_push($new_array, $values->hr_department_id);
            }

            $data = [
                'status' => 'success',
                'info' => $category,
                'departments'=>Department::whereIn('hr_department_id',$array)->pluck('hr_department_id')->all()
            ];

            return response()->json($data);
        }catch (\Throwable $th){
            $data = [
                'status' => null,
                'info' => $th->getMessage()
            ];
            return response()->json($data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $category_id)
    {
        $category = Category::findOrFail($category_id);

        $this->validate($request, [
            'code' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            // 'requisition_type_id' => ['required', 'integer'],
        ]);
        try {
            $departments = CategoryDepartment::where('category_id', $category->parent_id)->pluck('hr_department_id')->toArray();

            $inputs = $request->all();
            unset($inputs['_token']);
            unset($inputs['_method']);
            $category->update($inputs);

            $category->department()->sync($departments);

            return $this->backWithSuccess('Sub Category updated successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($category_id)
    {
        try {
            $category = Category::findOrFail($category_id);
            $category->subCategory->each->delete();
            CategoryDepartment::where('category_id', $category->id)->delete();
            $category->delete();
            return response()->json([
                'success' => true
            ]);
        }catch (\Throwable $th){
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }


    public function importCategory(Request $request){

        $this->validate($request, [
            'category_file' => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('category_file')->getRealPath();

        try {
            Excel::import(new CategoryImport(), $path);

            return $this->backWithSuccess('Category Data Imported successfully.');

        }catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $errorMessage='';
            $rowNumber=1;
            $rowNumber+=$e->failures()[0]->row();
            $column=$e->failures()[0]->attribute();

            $errorMessage.=$e->failures()[0]->errors()[0].' for row '.$rowNumber.' on Column '.$column;

            return $this->backWithError($errorMessage);
        }
    }
}
