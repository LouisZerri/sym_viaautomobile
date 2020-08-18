<?php

namespace App\Services;

class WeekFormat
{
    public function weekToString(string $annee, string $noSemaine)
    {
        // Récup jour début et fin de la semaine
        $timeStart = strtotime("First Monday January {$annee} + ".($noSemaine - 1)." Week");
        $timeEnd   = strtotime("First Monday January {$annee} + {$noSemaine} Week -1 day");

        // Récup année et mois début
        $anneeStart = date("Y", $timeStart);
        $anneeEnd   = date("Y", $timeEnd);
        $moisStart  = date("m", $timeStart);
        $moisEnd    = date("m", $timeEnd);

        // Gestion des différents cas de figure
        if( $anneeStart != $anneeEnd ){
            // à cheval entre 2 années
            $retour = "Semaine du ".strftime("%d %B %Y", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        } elseif( $moisStart != $moisEnd ){
            // à cheval entre 2 mois
            $retour = "Semaine du ".strftime("%d %B", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        } else {
            // même mois
            $retour = "Semaine du ".strftime("%d", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        }

        return $retour;
    }
}