<?php

namespace App\Libraries\Images;

use App\Libraries\Utils\Utils;
use File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Http\UploadedFile;
use Image;
use Imagine;
use Storage;

class Images
{
    const defaultCategory = 'Library';
    public static $folderPath = 'public/photos/';
    public static $thumbnailsPath = '_thumbnails/';
    public static $videoPath = 'video/';
    public static $videoIcon = 'images/play_button.png';
    public static $categoriesFolders = [
        'Library' => '',
    ];

    public static $categoriesDescriptions = [
        'Library' => 'Image Library',
    ];

    public static $pictureMaxWidth = 1920;
    public static $pictureMaxHeight = 1200;

    private static $thumbnailWidth = 300;
    private static $thumbnailHeight = 300;

    /**
     * Get Properties Pictures and all images from all categories
     *
     * @param string $user_id Folder name, containing pictures
     * @param array $categories Categories that should be scanned
     *
     * @return array
     *
     */
    public static function getPictures($user_id, $categories = [])
    {
        $pictures = [];

        if (empty($categories)) {
            $categories = self::$categoriesFolders;
        }
        foreach ($categories as $key => $value) {
            if (key_exists($key, self::$categoriesFolders)) {
                $picturesDir = self::getRelativePath($user_id, $key);

                $pictures[$key] = [];

                /* Process regular pictures */
                foreach (Storage::files($picturesDir) as $file) {
                    if (substr(MimeType::from($file), 0, 5) === 'image') {
                        $pictures[$key][] = self::getPictureURLs($picturesDir, File::basename($file), false, $key);
                    }
                }

                /* Process video files */
                $picturesDir .= self::$videoPath;
                foreach (Storage::files($picturesDir) as $file) {
                    $pictures[$key][] = self::getPictureURLs($picturesDir, File::basename($file), true, $key);
                }
            }
        }

        return $pictures;
    }

    /**
     * @param $user_id
     * @param $category
     *
     * @return string
     */
    private static function getRelativePath($user_id, $category = self::defaultCategory): string
    {
        return self::$folderPath . Utils::encodeId($user_id);
    }

    /**
     * Get array of URLs and other properties for picture
     *
     * @param $folder_name
     * @param $file_name
     * @param bool $is_video
     * @param string $category
     *
     * @return array
     */
    private static function getPictureURLs($folder_name, $file_name, $is_video = false, $category = self::defaultCategory)
    {
        $relative_path = str_replace(public_path(), '', $folder_name);
        $relative_path = trim($relative_path, '\/') . '/';

        return [
            'image'    => Storage::url($relative_path . $file_name),
            'thumb'    => Storage::url($is_video ? self::$videoIcon : $relative_path . self::$thumbnailsPath . $file_name),
            'uuid'     => 'ID' . str_replace('-', '', File::name($file_name)),
            'filename' => File::basename($file_name),
            'isVideo'  => $is_video,
            'category' => $category
        ];
    }

    /**
     *  Upload file to property category
     *
     * @param UploadedFile $picture
     * @param $user_id
     * @param string $category
     *
     * @return bool | array
     */
    public static function uploadPicture(UploadedFile $picture, $user_id, $category = self::defaultCategory)
    {
        if (key_exists($category, self::$categoriesFolders)) {

            $relative_path = self::getRelativePath($user_id, $category);

            self::createDirectories($relative_path);

            /**
             * Upload Pictures
             */
            $name = $picture->hashName();// Utils::uuid() . '.' . $picture->getClientOriginalExtension();
            $ext  = $picture->guessExtension();

            if (strpos($picture->getMimeType(), 'video') === false) {
                try {
                    $image = Image::open($picture);
                    $size  = $image->getSize();
                    // Resize file if it's not handled on frontend
                    if ($size && ($size->getWidth() > self::$pictureMaxWidth || $size->getHeight() > self::$pictureMaxHeight)) {
                        $resized = $image->thumbnail(new Imagine\Image\Box(self::$pictureMaxWidth, self::$pictureMaxHeight));
                        Storage::put($relative_path . '/' . $name, $resized->get($ext), 'public');
                    } else {
                        // Just store original file as is
                        $picture->storePubliclyAs($relative_path, $name);
                    }

                    // Create Thumbnails
                    $image     = Image::open($picture->path());
                    $thumbnail = $image->thumbnail(new Imagine\Image\Box(self::$thumbnailWidth, self::$thumbnailHeight));
                    Storage::put($relative_path . '/' . self::$thumbnailsPath . $name, $thumbnail->get($ext), 'public');

                    return self::getPictureURLs($relative_path, $name, false, $category);
                } catch (\Exception $e) {
                    return false;
                }
            } else {
                // Move the Original File.
                $picture->storePubliclyAs($relative_path, $name);

                return self::getPictureURLs($relative_path . self::$videoPath, $name, true, $category);
            }

        }

        return false;
    }

    private static function createDirectories($images_path)
    {
        $images_thumb_path = $images_path . '/' . self::$thumbnailsPath;
        $video_path        = $images_path . '/' . self::$videoPath;
        try {
            if (!Storage::exists($images_path)) {
                Storage::makeDirectory($images_path);
            }
            if (!Storage::exists($images_thumb_path)) {
                Storage::makeDirectory($images_thumb_path);
            }
            if (!Storage::exists($video_path)) {
                Storage::makeDirectory($video_path);
            }
        } catch (\Exception $e) {
        }
    }

    /**
     *  Delete image from property category
     *
     * @param string $user_id
     * @param string $filename
     * @param string $category
     *
     * @throws \Exception|\Throwable
     * @return bool | string
     */
    public static function deletePicture($user_id, $filename, $category = self::defaultCategory)
    {
        $relative_path = self::getRelativePath($user_id, $category);

        $fullname = "$relative_path/$filename";
        if (Storage::exists($fullname)) {
            if (strpos(Storage::mimeType($fullname), 'video') === false) {
                try {
                    Storage::delete($relative_path . '/' . self::$thumbnailsPath . $filename);
                } catch (\Exception $exception) {
                }
            }
        }
        Storage::delete($fullname);

        return true;
    }

}