<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\UserGalleryStoreCommentRequest;
use App\IgnoreUser;
use App\UserGallery;
use Illuminate\Http\Request;

class UserGalleryCommentController extends CommentController
{
    /**
     * Relation id
     *
     * @var string
     */
    protected static $relation = Comment::RELATION_USER_GALLERY;

    /**
     * View name
     *
     * @var string
     */
    protected static $view_name = 'gallery.photo';

    /**
     * object name with 'id'
     *
     * @var string
     */
    protected static $name_id = 'gallery_id';

    /**
     * Store a newly created resource in storage.
     *
     * @param UserGalleryStoreCommentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserGalleryStoreCommentRequest $request)
    {
        $user_id = UserGallery::find('gallery_id')->user_id;

        if (IgnoreUser::me_ignore($user_id)){
            return abort(403);
        }

        return self::storeComment($request);
    }
}