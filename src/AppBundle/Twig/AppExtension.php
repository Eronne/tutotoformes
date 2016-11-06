<?php
/**
 * Created by IntelliJ IDEA.
 * User: Omar
 * Date: 28/09/2016
 * Time: 11:15
 */

namespace AppBundle\Twig;


class AppExtension extends \Twig_Extension
{

    public function getFilters(){
        return [
            new \Twig_SimpleFilter('date_locale', [$this, 'dateLocaleFilter']),
            new \Twig_SimpleFilter('slugify', [$this, 'slugify'])
        ];
    }

    /**
     * @param \DateTime $date
     * @return string
     */
    public function dateLocaleFilter(\DateTime $date){
        setlocale(LC_TIME, "fr_FR.utf8", "fra", "French_Standard");
        $formatted = strftime("%d %B %Y", $date->getTimestamp());
        return $formatted;
    }

    public function slugify($value){
        return self::_slugify($value);
    }

    static private function _slugify($text)
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


    public function getName()
    {
        return 'app_extension';
    }
}