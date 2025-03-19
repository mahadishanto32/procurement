<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\Brand;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use DB;

use App\Imports\BrandsImport;
use Maatwebsite\Excel\Facades\Excel;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Brands';
            return view('pms.backend.pages.brand.index', compact('title'));
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
        try {
            $data = [
                'status' => 'success',
                'info' => Brand::all()
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return response()->json();
        if ($request->has('brandsrc')){
//            return response()->json();
            return $this->uploadWithImage($request->all());
//            return redirect(url($request->brandsrc));
        }
        try {
            if ($request->hasFile('image')) {
                $image = $request->image;
                $x = 'abcdefghijklmnopqrstuvwxyz0123456789';
                $x = str_shuffle($x);
                $x = substr($x, 0, 6) . 'DAC.';
                $filename = time() . $x . $image->getClientOriginalExtension();
                Image::make($image->getRealPath())
                ->save(public_path('/upload/brands/' . $filename));
                $path = "/upload/brands/" . $filename;
                $data['file_name'] = $request->image->getClientOriginalName();
                $data['image'] = $path;
            }
            $data['code'] = $request->code;
            $data['name'] = $request->name;

            $brand = Brand::create($data);

            $data = [
                'status' => 'success store',
                'info' => [$brand]
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        try {
            $data = [
                'status' => 'success',
                'info' => $brand
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
    public function update(Request $request, $id)
    {   
        $brand=Brand::findOrFail($id);

        return response()->json($request->all());

        try{
           if ($request->hasFile('image')) {
               if (!empty($brand->image)){
                   if (file_exists(public_path($brand->image))){
                       unlink(public_path($brand->image));
                   }
               }
                $image = $request->image;
                $x = 'abcdefghijklmnopqrstuvwxyz0123456789';
                $x = str_shuffle($x);
                $x = substr($x, 0, 6) . 'DAC.';
                $filename = time() . $x . $image->getClientOriginalExtension();
                Image::make($image->getRealPath())
                ->save(public_path('/upload/brands/' . $filename));
                $path = "/upload/brands/" . $filename;
                $data['file_name'] = $request->image->getClientOriginalName();
                $data['image'] = $path;
           }
           $data['code'] = $request->code;
           $data['name'] = $request->name;

           $brand->update($data);

            $data = [
                'status' => 'success',
                'info' => $request->all(),
                'ingo' => $brand
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

    public function uploadWithImage($inputs){

        $id = str_replace('/pms/product-management/brand/','',$inputs['brandsrc']);
        $brand=Brand::findOrFail($id);
//        return response()->json($inputs['image']);

        try{
            if (!empty($brand->image)){
                if (file_exists(public_path($brand->image))){
                    unlink(public_path($brand->image));
                }
            }
            $image = $inputs['image'];
            $x = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $x = str_shuffle($x);
            $x = substr($x, 0, 6) . 'DAC.';
            $filename = time() . $x . $image->getClientOriginalExtension();
            Image::make($image->getRealPath())
                ->save(public_path('/upload/brands/' . $filename));
            $path = "/upload/brands/" . $filename;
            $data['file_name'] = $inputs['image']->getClientOriginalName();
            $data['image'] = $path;
            $data['code'] = $inputs['code'];
            $data['name'] = $inputs['name'];

            $brand->update($data);

            $data = [
                'status' => 'success',
                'ingo' => $brand
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        try {
            if ($brand->image){
                if (file_exists(public_path($brand->image))){
                    unlink(public_path($brand->image));
                }
            }
            $brand->delete();
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }

    public function importBrand(Request $request)
    {
        $this->validate($request, [
            'brand_file'  => 'required|mimes:xls,xlsx'
        ]);

        $path = $request->file('brand_file')->getRealPath();

        try {

            Excel::import(new BrandsImport, $path);

            return $this->backWithSuccess('Excel Data Imported successfully.');

        }catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            $error=[];
            foreach ($failures as $failure) {
                $failure->row(); 
                $failure->attribute(); 
                $error[]=$failure->errors(); 
                $failure->values(); 
            }

            return $this->backWithError($error[0][0]);
        }
    }
}
