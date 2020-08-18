<?php


namespace App\Services;


class MonthToNumber
{
    function month_to_number($month)
    {
        switch ($month)
        {
            case "Janvier": $month = "01"; break;
            case "Février": $month = "02"; break;
            case "Mars": $month = "03"; break;
            case "Avril": $month = "04"; break;
            case "Mai": $month = "05"; break;
            case "Juin": $month = "06"; break;
            case "Juillet": $month = "07"; break;
            case "Août": $month = "08"; break;
            case "Septembre": $month = "09"; break;
            case "Octobre": $month = "10"; break;
            case "Novembre": $month = "11"; break;
            case "Décembre": $month = "12"; break;
        }

        return $month;
    }

}