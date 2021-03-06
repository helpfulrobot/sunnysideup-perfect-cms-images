<?php

/**
 * defines the image sizes
 * and default upload folder.
 */
class PerfectCMSImageDataExtension extends DataExtension
{
    /**
     * background image for padded images...
     *
     * @var string
     */
    private static $perfect_cms_images_background_padding_color = '#cccccc';

    /***
     * sizes of the images
     *     width: 3200
     *     height: 3200
     *     folder: "myfolder"
     *     filetype: "try jpg"
     *
     * @var array
     *
     */
    private static $perfect_cms_images_image_definitions = array();

    /**
     * @param string            $nameOfImageField
     * @param object (optional) $backupObject
     * @param string (optional) $backupField
     *
     * @return string
     */
    public function PerfectCMSImageLink(
        $name,
        $backupObject = null,
        $backupField = ''
    ) {
        $image = $this->owner;
        if ($image && $image->exists()) {
            //we are all good ...
        } else {
            if (!$backupObject) {
                $backupObject = SiteConfig::current_site_config();
            }
            if (!$backupField) {
                $backupField = $name;
            }
            if ($backupObject->hasMethod($backupField)) {
                $image = $backupObject->$backupField();
            }
        }

        $perfectWidth = (intval(self::get_width($name)) - 0) * 2;
        $perfectHeight = (intval(self::get_height($name)) - 0) * 2;
        if ($image) {
            if ($image instanceof Image) {
                if ($image->exists()) {
                    //get preferred width and height
                    $myWidth = $image->getWidth();
                    $myHeight = $image->getHeight();
                    $backEndString = Image::get_backend();
                    $backend = Injector::inst()->get($backEndString);
                    if ($perfectWidth && $perfectHeight) {
                        if ($myWidth == $perfectWidth || $myHeight ==  $perfectHeight) {
                            return $image->Link();
                        } elseif ($myWidth < $perfectWidth || $myHeight < $perfectHeight) {
                            return $image->Pad(
                                $perfectWidth,
                                $perfectHeight,
                                Config::inst()->get('PerfectCMSImageDataExtension', 'perfect_cms_images_background_padding_color')
                            )->Link();
                        } elseif ($myWidth > $perfectWidth || $myHeight > $perfectHeight) {
                            return $image->FitMax($perfectWidth, $perfectHeight)->Link();
                        }
                    } elseif ($perfectWidth) {
                        return $image->SetWidth($perfectWidth)->Link();
                    } elseif ($perfectHeight) {
                        return $image->SetHeight($perfectHeight)->Link();
                    } else {
                        return $image->Link();
                    }
                }
            }
        }
        if ($perfectWidth || $perfectHeight) {
            if (!$perfectWidth) {
                $perfectWidth = $perfectHeight;
            }
            if (!$perfectHeight) {
                $perfectHeight = $perfectWidth;
            }
            $text = "$perfectWidth x $perfectHeight /2 = $perfectWidth x $perfectHeight ";

            return 'https://placehold.it/'.($perfectWidth).'x'.($perfectHeight).'?text='.urlencode($text);
        } else {
            return 'https://placehold.it/500x500?text='.urlencode('no size set');
        }
    }

    /**
     * @param string           $name
     * @param Image (optional) $image
     *
     * @return int
     */
    public static function get_width($name)
    {
        return self::get_one_value_for_image($name, "width", 0);
    }

    /**
     * @param string           $name
     * @param Image (optional) $image
     *
     * @return int
     */
    public static function get_height($name)
    {
        return self::get_one_value_for_image($name, "height", 0);
    }

    /**
     * @param string           $name
     * @param Image (optional) $image
     *
     * @return string
     */
    public static function get_folder($name)
    {
        return self::get_one_value_for_image($name, "folder", 'other-images');
    }

    /**
     * @param string           $name
     * @param Image (optional) $image
     *
     * @return string
     */
    public static function get_file_type($name)
    {
        return self::get_one_value_for_image($name, "filetype", 'jpg');
    }

    /**
     * @param string $name
     * @param int    $key
     * @param mixed  $default
     */
    private static function get_one_value_for_image($name, $key, $default = '')
    {
        $sizes = self::get_sizes();
        //print_r($sizes);die();
        if (isset($sizes[$name])) {
            if (isset($sizes[$name][$key])) {
                return $sizes[$name][$key];
            }
        } else {
            user_error('no information for image with name: '.$name);
        }

        return $default;
    }

    /**
     * @return array
     */
    private static function get_sizes()
    {
        return Config::inst()->get('PerfectCMSImageDataExtension', 'perfect_cms_images_image_definitions');
    }
}
