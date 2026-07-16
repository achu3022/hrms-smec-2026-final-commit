<?php
$manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
$image = $manager->decode(file_get_contents('public/logo-new.webp'));
$image->cover(413, 531);
$encoded = $image->encodeUsingFileExtension('jpeg');
dump(strlen((string) $encoded));
