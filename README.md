# PixImGen

Pixel graphics library for PHP.

## Installation

Add this to your composer.json:

```JSON
{
	"require": {
		"thekonz/piximgen": "1.0.*@dev"
	}
}
```

Then run `composer install` or `composer update`.

## Example App

Wanna try it out? Take a look at the [example app](https://github.com/thekonz/piximgengui).

## Saving the image as a file

Since the method **getImage()** returns an Imagick object, you can use the method [Imagick::writeImage](http://www.php.net/manual/en/imagick.writeimage.php) to write the image to a file.