<?php

Configure::load('parameters');

class Utils {

    /**
     * Manipulate date to output the correct format
     * regarding the context where it's used.
     */
    public static function formatDate($date, $context) {
        try {
            switch ($context) {
            case 'expiration':
                if (!empty($date)) {
                    $date = new DateTime($date);
                    return $date->format('d M Y H:i:s');
                }
                break;
            case 'display':
                if (!empty($date)) {
                    $date = new DateTime($date);
                    return $date->format('d/m/Y') . '&nbsp;'
                        . __('at') . '&nbsp;' . $date->format('H:i:s');
                }
                break;
            case 'durdisplay':
                if (count($date) == 2) {
                    $start = new Datetime($date[0]);
                    $stop = new Datetime($date[1]);
                    $interval = $start->diff($stop);

                    $years = $interval->format('%y');
                    $months = $interval->format('%m');
                    $days = $interval->format('%d');
                    $hours = $interval->format('%h');
                    $minutes = $interval->format('%i');
                    $seconds = $interval->format('%s');

                    $duration = $years ? __('%dy', $years).'&nbsp;' : '';
                    $duration .= $months ? __('%dm', $months).'&nbsp;' : '';
                    $duration .= $days ? __('%dd', $days).'&nbsp;' : '';
                    $duration .= $hours ? __('%dh', $hours).'&nbsp;' : '';
                    $duration .= $minutes ? __('%dmin', $minutes).'&nbsp;' : '';
                    $duration .= $seconds ? __('%ds', $seconds) : '';

                    return $interval->format('%r') . '&nbsp;' . $duration; 
                }
            case 'dursign':
                if (count($date) == 2) {
                    $start = new Datetime($date[0]);
                    $stop = new Datetime($date[1]);
                    $interval = $stop->getTimestamp() - $start->getTimestamp();

                    if ($interval > 0) {
                        return 1;
                    } else if ($interval < 0) {
                        return -1;
                    } else {
                        return 0;
                    }
                } else {
                    return false;
                }
            case 'syst':
                if (!empty($date)) {
                    $date = new DateTime($date);
                    return $date->format('Y-m-d H:i:s');
                }
                break;
            }
        } catch (Exception $ex){}

        return $date;
    }

    /**
     * Generate a sql query with cakephp tools.
     * !!!WARNING!!! Experimental
     */
    public static function generateQuery($model, $queryData) {
        $db = $model->getDataSource();
        $params = array_merge(
            array(
                'fields'     => array(),
                'table'      => $db->fullTableName($model),
                'alias'      => ucfirst($model->table),
                'limit'      => null,
                'offset'     => null,
                'joins'      => array(),
                'conditions' => array(),
                'order'      => null,
                'group'      => null
            ),
            $queryData
        );
        return trim($db->buildStatement($params, $model));
    }

    /**
     * Tell if the given string is a MAC address
     * @param  string $string string to check
     * @return boolean         string is a MAC
     */
    public static function isMAC($string) {
        return preg_match(
            '/^(?:[[:xdigit:]]{2}([-:]?))(?:[[:xdigit:]]{2}\1){4}[[:xdigit:]]{2}$/',
            $string
        );
    }

    /**
     * Tell if the given string is an IP address
     * @param  string $string string to check
     * @return boolean         string is an IP
     */
    public static function isIP($string) {
        return preg_match(
            '/^([[:digit:]]{1,3}.){3}[[:digit:]]{1,3}(\/[[:digit:]]{2})?$/',
            $string
        );
    }

    /**
     * Clean a MAC string by removing - and : chars
     * @param  string $mac mac nicely formatted
     * @return string      cleaned mac
     */
    public static function cleanMAC($mac) {
        $mac = str_replace(':', '', $mac);
        $mac = str_replace('-', '', $mac);

        return $mac;
    }

    public static function cleanPath(&$path) {
        if (substr($path, -1) == '/') {
            $path = substr($path, 0, strlen($path)-1);
        }
    }

    public static function formatTime($days, $hours, $minutes, $seconds) {
        $result = ($days) ? $days . ' '  
            . __n(__("day"), __("days"), $days)
            . ' ' : '';
        $result .= ($hours) ? $hours . ' '  
            . __n(__("hour"), __("hours"), $hours)
            . ' ' : '';
        $result .= ($minutes) ? $minutes . ' '  
            . __n(__("minute"), __("minutes"), $minutes)
            . ' ' : '';
        $result .= ($seconds) ? $seconds . ' '  
            . __n(__("second"), __("seconds"), $seconds)
            . ' ' : '';

        return $result;
    }

    public static function secondToTime($last) {
		$seconds	= $last%60;
		$minutes	= floor($last/60)%60;
		$hours		= floor($last/3600)%24;
		$days		= floor($last/86400);

        return Utils::formatTime($days, $hours, $minutes, $seconds);
    }

    public static function octets($data) {
		$octets     = $data%1024;
		$koctets	= floor($data/1024)%1024;
		$moctets	= floor($data/1048576)%1024;
		$goctets	= floor($data/1073741824);

        $result = ($goctets) ? $goctets . ' Go ' : '';
        $result .= ($moctets) ? $moctetss . ' Mo ' : '';
        $result .= ($koctets) ? $koctets . ' Ko ' : '';
        $result .= ($octets) ? $octets . ' '
            . __n(__("byte"), __("bytes"), $octets)
            . ' ' : '';

        return $result;
    }

    /**
     * Reformat the mac addr for a nice display
     */
    public static function formatMAC( $mac, $delimiter = ':' ) {
        return ( strlen( $mac ) == 12 ) ? 
            substr($mac, 0, 2) . $delimiter
            . substr($mac, 2, 2) . $delimiter
            . substr($mac, 4, 2) . $delimiter
            . substr($mac, 6, 2) . $delimiter
            . substr($mac, 8, 2) . $delimiter
            . substr($mac, 10, 2) : $mac;
    }

    public static function shell($command) {
        exec($command, $output, $return);

        $result = array(
            'command' => $command,
            'code' => $return,
            'msg' => $output,
        );

        return $result;
    }

	/* Reads a file. */
	public static function readFile($fileName) {
        $result = file(
            $fileName,
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

		foreach  ($result as &$value) {
            $value = trim( $value );
        }

		return $result;
	}

    public static function getUserCertsPath($username) {
        $base = Configure::read('Parameters.certsPath') . '/users/' . $username;
        return array(
            'public' => $base . '_cert.pem',
            'key' => $base . '_key.pem',
        );
    }

    public function getType($id, $nice = false) {
        $types = array(
            array('cisco', 'Cisco'),
            array('loginpass', 'Login / Password'),
            array('mac', 'MAC'),
            array('cert', 'Certificate')
        );

        if($this->isCisco($id))
            return $types[0][$nice];
        if($this->isLoginPass($id))
            return $types[1][$nice];
        if($this->isMAC($id))
            return $types[2][$nice];
        if($this->isCert($id))
            return $types[3][$nice];
        return "";
    }

    public static function getISOCode($httpAcceptLanguage) {
        $langs = array(
            'fr-FR' => 'fra',
            'en-US' => 'eng'
        );

        foreach ($langs as $key => $value) {
    	    $httpAcceptLanguages = explode(',', $httpAcceptLanguage);

            if($httpAcceptLanguages[0] == $key) {
                return $value;
            }
        }
        return 'eng';
    }

    /**
     * Log a message in syslog using currently connected user
     * @param  string $message message to log
     * @param  string $level   level of the message
     * @return void         
     */
    public static function userlog($message, $level='info') {
        CakeLog::write($level, AuthComponent::user('username') . ': ' . $message);
    }
}
?>
