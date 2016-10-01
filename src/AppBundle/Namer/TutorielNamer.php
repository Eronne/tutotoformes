<?php
/**
 * Created by IntelliJ IDEA.
 * User: OMAR-PC
 * Date: 01/10/2016
 * Time: 17:35
 */

namespace AppBundle\Namer;


use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

class TutorielNamer implements NamerInterface
{

    /**
     * Creates a name for the file being uploaded.
     *
     * @param object $object The object the upload is attached to.
     * @param PropertyMapping $mapping The mapping to use to manipulate the given object.
     *
     * @return string The file name.
     */
    public function name($object, PropertyMapping $mapping)
    {
//        return $mapping->getFile($object)
        $now = new \DateTime('now');
        return $now->format('d_m_Y') . "_" . $object->getId() . "_" . self::slugify($object->getTitle()) . "." . $mapping->getFile($object)->guessClientExtension();
    }

    static private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

}