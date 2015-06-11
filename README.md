# yii2-image

Image extension for Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

php composer.phar require 'andreosoft/yii2-image'

or add

"andreosoft/yii2-image": "*"

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

<?php 
use andreosoft\image; 
?>
....
<?php
    $options = [
          'root' => \Yii::getAlias('@root'),
          'webroot' => 'http://mysite.com/uploads',
          'quality' => 50,
    ];
    echo Image::thumb('test-image.jpg', 120, 120)
?>
