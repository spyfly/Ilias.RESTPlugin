<?php
/**
 * ILIAS REST Plugin for the ILIAS LMS
 *
 * Authors: D.Schaefer and T.Hufschmidt <(schaefer|hufschmidt)@hrz.uni-marburg.de>
 * Since 2014
 */
namespace RESTController\core\clients;

// This allows us to use shortcuts instead of full quantifier
use \RESTController\libs as Libs;


/**
 * This class provides methods for dealing with a particular REST client / API-Key.
 * Constructor requires $sqlDB.
 */
class RESTClient extends Libs\RESTModel {

    /* Stores the basic data of the rest client */
    protected $client_fields;

    /* Array of ip addresses (strings) */
    protected $allowedIPs;

    /* Array of ILIAS user ids (numbers) */
    protected $allowedUsers;


    /**
     * Constructor
     * @param $api_key
     */
    function RESTClient($api_key) {
        $sql = Libs\RESTLib::safeSQL("SELECT * FROM ui_uihk_rest_keys WHERE api_key = %s", $api_key);
        $query = self::$sqlDB->query($sql);
        if (self::$sqlDB->numRows($query) > 0) {
            $this->client_fields = self::$sqlDB->fetchAssoc($query);
        } else {
            $this->client_fields = array();
        }
    }

    /**
     * Gets the internal table id of the client
     */
    function getApiId() {
        return $this->client_fields['id'];
    }

    /**
     * Returns whether the client is restricted to IP addresses
     * @return bool
     */
    function hasIpRestrictions() {
        if ($this->client_fields['ip_restriction_active'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns whether the user restriction option is active or not.
     * @return bool
     */
    function hasUserRestrictions() {
        if ($this->client_fields['oauth2_user_restriction_active'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks whether the current client has a particular api key.
     * @param $api_key
     * @return bool
     */
    function hasAPIKey($api_key) {
        return $this->client_fields['api_key'] == $api_key;
    }

    /**
     * Checks whether the current client has a particular api secret.
     * @param $api_secret
     * @return bool
     */
    function hasAPISecret($api_secret) {
        return $this->client_fields['api_secret'] == $api_secret;
    }

    /**
     * Returns an array of IP addresses that are allowed to use this client, in case the IP restriction option is active.
     * @return array of strings
     */
    function getAllowedIPAddresses() {
        if (!$this->allowedIPs) {
            $sql = Libs\RESTLib::safeSQL("SELECT * FROM ui_uihk_rest_keyipmap WHERE api_id = %d", $this->client_fields['id']);
            $query = self::$sqlDB->query($sql);
            if (self::$sqlDB->numRows($query) > 0) {
                $this->allowedIPs = array();
                while($row = self::$sqlDB->fetchAssoc($query)) {
                    $this->allowedIPs[] = $row['ip'];
                }
                return $this->allowedIPs;
            }
        } else {
            return $this->allowedIPs;
        }
    }

    /**
     * Returns an array of ILIAS user IDs that are allowed to use this client, in case the User restriction option is active.
     * @return array
     */
    function getAllowedUsers() {
        if (!$this->allowedUsers) {
            oauth2_user_restriction_active
            $sql = Libs\RESTLib::safeSQL("SELECT * FROM ui_uihk_rest_keyusermap WHERE api_id = %d", $this->client_fields['id']);
            $query = self::$sqlDB->query($sql);
            if (self::$sqlDB->numRows($query) > 0) {
                $this->allowedUsers = array();
                while($row = self::$sqlDB->fetchAssoc($query)) {
                    $this->allowedUsers[] = $row['user_id'];
                }
                return $this->allowedUsers;
            }
        } else {
            return $this->allowedUsers;
        }
    }


}
