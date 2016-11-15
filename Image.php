<?php

namespace andreosoft\image;

use yii\helpers\Url;

/**
 * Description of Image
 *
 * @author Andrey Zagorets
 */
class Image {
    /**
     *  Example of use:
     *
     * 
     * // generate a thumbnail image.
     * 
     * <?php $options = [
     * 
     *      'root' => \Yii::getAlias('@root'),
     * 
     *      'webroot' => 'http://mysite.com/uploads',
     * 
     *      'quality' => 50,
     * 
     *      'cachedir' => \Yii::getAlias('@root'),
     * 
     *      'resize' => 'h'|'w'|'max';
     * 
     *      'watermark' => 
     * 
     *                  ['file' => 'watermark.png',
     * 
     *                   'position' => 'topleft'|'topright'|'bottomleft'|'bottomright']
     * ];
     * 
     * echo Image::thumb('test-image.jpg', 120, 120);
     * ?>
     * 
     * @param $filename filename 
     * @param $width  width
     * @param $height height
     * @param $options options
     * @return url cache image.
     */
    static function thumb($filename, $width, $height, $options = []) {

        if (isset($options['root'])) {
            $root = $options['root'];
        } else {
            $root = \Yii::getAlias('@uploads');
        }

        if (isset($options['cachedir'])) {
            $cachedir = $options['cachedir'];
        } else {
            $cachedir = \Yii::getAlias('@uploads');
        }
        
        $empty = false;
        if (!is_file($root . $filename)) {
            if (isset($options['empty'])) {
                $filename = '/' . $options['empty'];
            } else {
                $empty = true;
                $filename = '/empty.jpg';
            }
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        $old_image = $filename;
        $new_image = '/cache/' . md5(substr($filename, 0, strrpos($filename, '.')) . '-' . $width . 'x' . $height . '-' . json_encode($options)) . '.' . $extension;

        if (!is_file($cachedir . $new_image) || (filectime($root . $old_image) > filectime($cachedir . $new_image))) {
            $path = '';

            $directories = explode('/', dirname(str_replace('../', '', $new_image)));

            foreach ($directories as $directory) {
                $path = $path . '/' . $directory;

                if (!is_dir($cachedir . $path)) {
                    @mkdir($cachedir . $path, 0775);
                }
            }

            list($width_orig, $height_orig) = getimagesize($root . $old_image);

            if ($width_orig != $width || $height_orig != $height) {
                $image = new CoreImage($root . $old_image);
                if (isset($options['resize'])) {
                    $image->resize($width, $height, $options['resize']);
                } else {
                    $image->resize($width, $height);
                }
                if (isset($options['watermark']) && !$empty) {
                    $position = 'bottomleft';
                    if (isset($options['watermark']['position'])) {
                        $position = $options['watermark']['position'];
                    }
                    $percent = 0.3;
                    if (isset($options['watermark']['percent'])) {
                        $percent = $options['watermark']['percent'];
                    }
                    $image->watermark($root . $options['watermark']['file'], $position, $percent);
                }
                if (isset($options['quality'])) {
                    $image->save($cachedir . $new_image, $options['quality']);
                } else {
                    $image->save($cachedir . $new_image);
                }
            } else {
                copy($root . $old_image, $cachedir . $new_image);
            }
        }

        if (isset($options['webroot'])) {
            return Url::to($options['webroot'] . $new_image);
        } else {
            return Url::to('@webuploads' . $new_image);
        }
    }

}
