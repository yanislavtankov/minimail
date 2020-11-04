<?php

namespace App\Modules\Images\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ImagesController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        if ($request->allFiles('files')){
            foreach($request->allFiles('files') as $file){
                $name = $request->file->getClientOriginalName();
                $path[] = $request->file->storeAs('public', $name);
            }
            return response()->json([
                'type' => self::RESPONSE_TYPE_SUCCESS,
                'message' => 'File ready for upload.',
                'path' => $path
            ]);
        }
        return response()->json([
            'type' => self::RESPONSE_TYPE_ERROR,
            'message' => 'Error loading file. Please try again or contact your administrator.'
        ]);
    }
}
