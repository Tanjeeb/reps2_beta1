<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\{ ChatPictureSearchAdminRequest, PictureStoreRequest, PictureUpdateRequest};
use App\Services\Base\{BaseDataService, AdminViewService};
use App\Services\Chat\ChatPicturesService;
use App\Http\Controllers\Controller;
use App\ChatPicture;
use App\ChatPictureCategory;

class ChatPicturesController extends Controller
{   
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ChatPictureSearchAdminRequest $request)
    {
        $data = ChatPicturesService::search($request)->count();
        $categories = ChatPictureCategory::orderBy('id')->get();
        return view('admin.chat.pictures.list')->with(['pictures_count'=> $data,'categories'=>$categories, 'request_data' => $request->validated()]);
    }

    /**
     * Get chat pictures list paginate
     *
     * @return array
     */
    public function pagination(ChatPictureSearchAdminRequest $request)
    {
        
        $pictures = ChatPicture::getPictures($request);
   
        return BaseDataService::getPaginationData(
            AdminViewService::getChatPictures($pictures), 
            AdminViewService::getPagination($pictures),
            AdminViewService::getChatPicturesPopUp($pictures)
        );    
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $categories = ChatPictureCategory::orderBy('id')->get();
        return view('admin.chat.pictures.create')->with(['categories' => $categories]);       
    }

    /**
     * @param PictureStoreRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store(PictureStoreRequest $request)
    {
        $save = ChatPicturesService::store($request);
        return redirect()->route('admin.chat.pictures');
    }

     /**
     * @param $picture_id
     * @return mixed
     */
    private function getPictureObject($picture_id)
    {
        return ChatPicture::getpictureById($picture_id);
    }
    
    /**
     * @param $stream_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($picture_id)
    {
        $categories = ChatPictureCategory::orderBy('id')->get();
        return view('admin.chat.pictures.edit')->with(['picture'=> $this->getPictureObject($picture_id), 'categories' => $categories]);
    }

    /**
     * @param PictureUpdateRequest $request
     * @param $picture_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PictureUpdateRequest $request, $picture_id)
    {
        $picture = ChatPicture::find($picture_id);

        if($picture){
            ChatPicturesService::update($request, $picture);
            return redirect()->route('admin.chat.pictures');
        }
        return abort(404);
    }
    
    /**
     * @param $picture_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($picture_id)
    {
        ChatPicturesService::destroy(ChatPicture::find($picture_id));
        return back();       
    }
}
