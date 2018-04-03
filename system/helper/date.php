<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  date.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

if (!function_exists('set_time_zone')) {

    /**
     * Modifie la timezone de php
     * @param string $continent
     * @param string $ville
     */
    function set_time_zone($continent = 'Europe', $ville = 'Paris') {
        date_default_timezone_set($continent . '/' . $ville);
    }

}

if (!function_exists('date_now')) {

    /**
     * Retourne la date et l'heure d'aujourd'hui
     * @param boolean $fr - En Fr ou en US
     * @return string
     */
    function date_now($fr = false) {
        set_time_zone();
        if ($fr === true) {
            return date('d/m/Y à H\hi');
        }
        return date('Y-m-d H:i:s');
    }

}

if (!function_exists('convert_date')) {

    /**
     * Convertit une date dans un format
     * @see strftime()
     * @param string $format
     * @param string $date
     * @return string
     */
    function convert_date($format, $date) {
        set_time_zone();
        return strftime($format, strtotime(str_replace('/', '-', $date)));
    }

}

if (!function_exists('diff_date')) {

    /**
     * Fait la difference entre deux dates
     * @param string $date1
     * @param string $date2
     * @param int La difference
     */
    function diff_date($date1, $date2) {
        $date1 = convert_date('%Y%m%d', $date1);
        $date2 = convert_date('%Y%m%d', $date2);
        return ($date1 - $date2);
    }
}

if (!function_exists('compare_date')){

	/**
	 * Compare deux dates 
	 * @param string $date1 
	 * @param string $date2
	 * @return int -1 $date1 plus ancienne que $date2, 0 même date, 1 $date1 plus recente que $date2
	 */
	function compare_date($date1, $date2){
		$res = diff_date($date1, $date2);
		if($res < 0){
			return -1;
		} else if($res > 0){
			return 1;
		} else {
			return 0;
		}
	}
}

if (!function_exists('days_between_dates')) {

    /**
     * Calcul le nombre de jour entre deux date
     * @param string $dateDebut
     * @param string $dateFin
     * @return boolean|int
     */
    function days_between_dates($dateDebut, $dateFin) {
        set_time_zone();
        if ($dateDebut == $dateFin) {
            return 0;
        }
        $nbSecondes = 60 * 60 * 24;
        $debut_ts = strtotime($dateDebut);
        $fin_ts = strtotime($dateFin);
        if ($fin_ts < $debut_ts) {
            return false;
        }
        $diff = $fin_ts - $debut_ts;
        return ceil($diff / $nbSecondes);
    }

}

if (!function_exists('hours_int')) {

    /**
     * Convertit une heure en int
     * 
     * @param string $date - une date avec l'heure, ou juste l'heure
     * @return int
     */
    function hours_int($date) {
        $date = convert_date('%H:%M:%S', $date);
        list($heure, $min, $sec) = explode(":", $date);
        return $sec + ($min * 60) + ($heure * 60 * 60);
    }

}

if (!function_exists('diff_hours')) {

    /**
     * Compare 2 heures
     * 
     * @param string $date1 - une date avec l'heure, ou juste l'heure
     * @param string $date2 - une date avec l'heure, ou juste l'heure
     * @return int
     */
    function diff_hours($date1, $date2) {
        $date1 = hours_int($date1);
        $date2 = hours_int($date2);
        return $date1 - $date2;
    }

}

if (!function_exists('is_bissextile')) {

    /**
     * Indique si une annéée est bissextile
     * @param int $year
     * @return boolean
     */
    function is_bissextile($year) {
        if ($year % 400 == 0) {
            return true;
        } else if ($year % 100 == 0) {
            return false;
        } else if ($year % 4 == 0) {
            return true;
        } else {
            return false;
        }
    }

}

if (!function_exists('add_day')) {

    /**
     * Ajoute un nombre de jour à une date
     * @param string $date
     * @param int $add_day - Le nombre de jour à ajouter
     * @return string
     */
    function add_day($date, $add_day) {
        list($year, $month, $day) = explode("-", convert_date("%Y-%m-%d", $date));
        if (in_array($month, array('01', '03', '05', '07', '08', '10', '12'))) {
            if ($day + $add_day > 31) {
                $nbJourAv31 = 31 - $day + 1;
                $add_day = $add_day - $nbJourAv31;
                $date = ($month == '12') ? $year . '-01-' . $add_day : $year . '-' . ($month + 1) . '-01';
                $return = add_day($date, $add_day);
            } else {
                $return = $year . '-' . $month . '-' . ($day + $add_day);
            }
        } else if (in_array($month, array('04', '06', '09', '11'))) {
            if ($day + $add_day > 30) {
                $nbJourAv30 = 30 - $day + 1;
                $add_day = $add_day - $nbJourAv30;
                $date = ($month == '12') ? $year . '-01-' . $add_day : $year . '-' . ($month + 1) . '-01';
                $return = add_day($date, $add_day);
            } else {
                $return = $year . '-' . $month . '-' . ($day + $add_day);
            }
        } else {
            if (is_bissextile($year)) {
                if ($day + $add_day > 29) {
                    $nbJourAv29 = 29 - $day + 1;
                    $add_day = $add_day - $nbJourAv29;
                    $date = ($month == '12') ? $year . '-01-' . $add_day : $year . '-' . ($month + 1) . '-01';
                    $return = add_day($date, $add_day);
                } else {
                    $return = $year . '-' . $month . '-' . ($day + $add_day);
                }
            } else {
                if ($day + $add_day > 28) {
                    $nbJourAv28 = 28 - $day + 1;
                    $add_day = $add_day - $nbJourAv28;
                    $date = ($month == '12') ? $year . '-01-' . $add_day : $year . '-' . ($month + 1) . '-01';
                    $return = add_day($date, $add_day);
                } else {
                    $return = $year . '-' . $month . '-' . ($day + $add_day);
                }
            }
        }

        return convert_date("%Y-%m-%d", $return);
    }

}

if (!function_exists('days_in_month')) {

    /**
     * Number of days in a month
     *
     * Takes a month/year as input and returns the number of days
     * for the given month/year. Takes leap years into consideration.
     *
     * @param	int	a numeric month
     * @param	int	a numeric year
     * @return	int
     */
    function days_in_month($month = 0, $year = '') {
        if ($month < 1 OR $month > 12) {
            return 0;
        } elseif (!is_numeric($year) OR strlen($year) !== 4) {
            $year = date('Y');
        }
        if (defined('CAL_GREGORIAN')) {
            return cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        if ($year >= 1970) {
            return (int) date('t', mktime(12, 0, 0, $month, 1, $year));
        }
        if ($month == 2) {
            if ($year % 400 === 0 OR ( $year % 4 === 0 && $year % 100 !== 0)) {
                return 29;
            }
        }
        $days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        return $days_in_month[$month - 1];
    }

}
