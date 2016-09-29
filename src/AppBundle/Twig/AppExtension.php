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
            new \Twig_SimpleFilter('date_locale', [$this, 'dateLocaleFilter'])
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


    public function getName()
    {
        return 'app_extension';
    }
}