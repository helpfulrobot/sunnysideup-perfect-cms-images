Perfect CMS Image
================

Why we build this module ...
------------
To make it easier to manage image sizes in the various places (CMS, templates) we have set up a system to manage image sizes in just one place (the config layer).  Each unique image collection (e.g. HomePageBanner) has its own standard settings (all optional):
 - `width` 
 - `height`  
 - `folder for upload` 
 - `file type`
 
You can also provide a backup image in the SiteConfig in case the user has not (yet) uploaded an image. 

Prerequisites
-------------
This is specifically meant for images that are always the same size (e.g. Page Banner, Team Member, etc...).

What it does
-------------
 * provides clear instructions with upload field
 * takes care of double size for retina
 * always saves the image in a specific folder
 * makes sure image is valid and not too big in file size
 * provides a backup image
 * in case there is no backup, it adds a placeholder image


Instructions
------------

  1. add an image in Page or MyDataObject
  2. add a back-up image to siteconfig
  3. define the settings for the image in the `PerfectCMSImageDataExtension` class using yml configuration.
  4. in the CMS Fields, use `PerfectCMSImagesUploadField`
  5. in the template write: `$MyImage.PerfectCMSImageLink(NameOfFormat)`

# Nota Bene

 * use a unique image name for each image field you add to the site
   e.g. call an image AccountsBanner instead of Banner
 * dont double the image sizes
 * you can choose to only set the standard height or the width

# add an image in Page or MyDataObject

```php
    class MyPage extends Page
    {
        private $has_one = array("MyImage" => "Image");
    }
```

# in a class that decorates / extends SiteConfig, add the same image

```php
    class MySiteConfigExtension extends DataExtension
    {
        private $has_one = array("MyImage" => "Image");
    }
```

# define image

copy `perfect_cms_images/_config/perfect_cms_images.yml.example`
to `mysite/_config/perfect_cms_images.yml`
and rewrite like this:

```yml
    ---
    Name: perfect_cms_images_custom
    ---
    PerfectCMSImageDataExtension:
      perfect_cms_images_background_padding_color: "#cccccc"
      perfect_cms_images_image_definitions:
        "MyImage":
          width: 900 (could be zero)
          height: 300 (could be zero)
          folder: uploaded-my-images
          filetype: "png"
        "MyOtherImage":
          width: 400
          height: 0
          folder: "uploaded-my-images2"
          filetype: "jpg or gif"
```

# set up CMS Field in Page


```php
    class MyPage extends Page
    {
        private $has_one = array("MyImage" => "Image");

        public function getCMSFields() {
            //...
            $fields->addFieldToTab(
                "Root.Images",
                PerfectCMSImagesUploadField::create(
                    $name = "MyImage",
                    $title = "My Cool Image"
                )
            );
            //...
        }
    }
```

you can also use a different formatting standard


```php
    class MyPage extends Page
    {
        private $has_one = array("MyImage" => "Image");

        public function getCMSFields() {
            //...
            $fields->addFieldToTab(
                "Root.Images",
                PerfectCMSImagesUploadField::create(
                    $name = "MyImage",
                    $title = "My Cool Image"
                )->selectFormattingStandard('MyOtherImage')
            );
            //...
        }
    }
```

# set up CMS Field in SiteConfig


```php
    class MySiteConfigExtension extends DataExtension
    {
        private $has_one = array("MyImage" => "Image");

        public function getCMSFields() {
            //...
            $fields->addFieldToTab(
                "Root.Images",
                PerfectCMSImagesUploadField::create(
                    $name = "MyImage",
                    $title = "My Default Cool Image"
                )
            );
            //...
        }
    }
```


# templage Usage

```html
    <img src="$MyImage.PerfectCMSImageLink(MyImage)" alt="$Title.ATT" />
```

OR

```html
    <img src="$MyImage.PerfectCMSImageLink(MyOtherImage)" alt="$Title.ATT" />
```

Credits
------------

Special thank you to Klemen Novak for help with this module.
