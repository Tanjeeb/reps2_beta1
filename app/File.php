<?php

namespace App;

use App\Traits\ModelRelations\FileRelation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Image;
use Illuminate\Support\Facades\Storage; //Laravel Filesystem

class File extends Model
{
    use FileRelation;
    /**
     * Using table name
     *
     * @var string
     */
    protected $table='files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'link',
        'size',
        'type'
    ];

    /**
     * Storage new file
     *
     * @param $file
     * @param $dir_name
     * @param string $file_title
     * @param bool $file_name
     * @return mixed
     */
    public static function storeFile($file, $dir_name, $file_title = '', $flag= false)
    {
        $uploading_path = $dir_name.'/'.Carbon::now()->format('Y-m-d');
        $ext = $flag == 'smile' ? 'gif' : pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $original_name = Carbon::now()->timestamp. '.' .$ext;
        
        $path = str_replace('public', '/storage',  $file->storeAs('public/' . $uploading_path, $original_name));

        if($flag == 'smile') {
            $img = Image::make(public_path($path))->resize(15, 15, function($constraint) {
                $constraint->aspectRatio();
            });     
            $img->save(public_path($path));        
        }
        $file_boj = File::create([
            'user_id' => Auth::id(),
            'title' => $file_title,
            'link' => $path,
            'size' => $file->getSize(),
            'type' => $file->getMimeType(),
        ]);

        return $file_boj;
    }
}