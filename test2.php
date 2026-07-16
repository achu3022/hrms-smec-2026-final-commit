<?php
$manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
$image = $manager->decode(file_get_contents('public/logo-new.webp'));
dump(get_class($image));
dump(get_class_methods($image));
