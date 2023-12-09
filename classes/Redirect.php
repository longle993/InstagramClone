<?php
namespace classes;
class Redirect {
    public static function to($location=null) {
        if(isset($location)) {
            if(is_numeric($location)) {
                switch($location) {
                    case 404:
                        header("HTTP/1.0 404 Not Found");
                        exit();
                    break;
                }
            }
            header("Location: " . $location);
        }
    }
}