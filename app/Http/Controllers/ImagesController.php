<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImagesController extends Controller
{
    /**
     * @var ImageService
     */
    private $service;

    public function __construct(ImageService $service)
    {
        $this->middleware('auth');

        $this->service = $service;
    }

    public function store(Request $request)
    {
        $data = [
            'success' => false,
            'msg' => '上传失败!',
            'file_path' => ''
        ];

        if ($file = $request->upload_file) {
            $result = $this->service->handleUpload($file, 'topics', Auth::id(), 1024);

            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg'] = "上传成功!";
                $data['success'] = true;
            }
        }

        return $data;
    }
}
