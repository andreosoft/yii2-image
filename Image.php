<?php

namespace andreosoft\image;

/**
 * Description of Image
 *
 * @author andreo
 */
class Image {
    
    /* Example of use:
     *
     * ~~~php
     * // generate a thumbnail image
     * <?php $options = [
     *      'root' => \Yii::getAlias('@root'),
     *      'webroot' => 'http://mysite.com/uploads',
     *      'quality' => 50,
     * ];
     * echo Image::thumb('test-image.jpg', 120, 120);
     * ?>
     * ~~~
     */

    static function thumb($filename, $width, $height, $options = []) {
        
        if (isset($options['root'])) {
            $root = $options['root'];
        } else {
            $root = \Yii::getAlias('@uploads');
        }
        if (!is_file($root . $filename)) {
            $filename = '/empty.jpg';
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $old_image = $filename;
        $new_image = '/cache/' . md5(substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height) . '.' . $extension;

        if (!is_file($root . $new_image) || (filectime($root . $old_image) > filectime($root . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!is_dir($root . $path)) {
                    @mkdir($root . $path, 0777);
                }
            }

            list($width_orig, $height_orig) = getimagesize($root . $old_image);

            if ($width_orig != $width || $height_orig != $height) {
                $image = new CoreImage($root . $old_image);
                $image->resize($width, $height);
                if (isset($options['quality'])) {
                    $image->save($root . $new_image, $options['quality']);
                } else {
                    $image->save($root . $new_image);
                }
            } else {
                copy($root . $old_image, $root . $new_image);
            }
        }

        if (isset($options['webroot'])) {
            return Url::to($options['webroot'] . $new_image);
        } else {
            return Url::to('@webuploads' . $new_image);
        }
        
    }

}
