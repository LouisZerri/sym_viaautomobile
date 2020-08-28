<?php

namespace App\Services;

class WeekFormat
{
    public function weekToString(string $annee, string $noSemaine)
    {
        setlocale (LC_TIME, 'fr_FR.utf8','fra.utf-8');
        $timeStart = strtotime("First Monday January {$annee} + ".($noSemaine - 1)." Week");
        $timeEnd   = strtotime("First Monday January {$annee} + {$noSemaine} Week -1 day");

        $anneeStart = date("Y", $timeStart);
        $anneeEnd   = date("Y", $timeEnd);
        $moisStart  = date("m", $timeStart);
        $moisEnd    = date("m", $timeEnd);

        if( $anneeStart != $anneeEnd ){
            $retour = "Semaine du ".strftime("%d %B %Y", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        } elseif( $moisStart != $moisEnd ){
            $retour = "Semaine du ".strftime("%d %B", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        } else {
            $retour = "Semaine du ".strftime("%d", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        }

        return $retour;
    }
}