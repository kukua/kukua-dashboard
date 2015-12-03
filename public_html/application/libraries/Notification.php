<?php

class Notification {

    public static $name = "notification";
    public static $validTypes = Array("danger", "info", "success", "warning");

    /**
     * Get and unserialize message session
     *
     * @static
     * @access public
     * @return Array
     */
    public static function get() {
        $session = (isset($_SESSION[Notification::$name]) ? $_SESSION[Notification::$name] : null);
        if ($session !== Null) {
            $notification = unserialize($session);
            return array(
                "type" => $notification["type"],
                "message" => $notification["message"]
            );
        }
    }

    /**
     * Set serialized message session
     *
     * @static
     * @access public
     * @param  string $type
     * @param  string $message
     * @return void
     */
    public static function set($type, $message) {
        if (in_array($type, Notification::$validTypes)) {
            $notification = array("type" => $type, "message" => $message);
            $_SESSION[self::$name] = serialize($notification);
        }
    }

    /**
     * Reset session
     *
     * @static
     * @access public
     * @return void
     */
    public static function reset() {
        if (isset($_SESSION[Notification::$name])) {
            unset($_SESSION[Notification::$name]);
        }
    }
}
