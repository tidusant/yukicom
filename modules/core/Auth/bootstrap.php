<?php

// API

$this->module("auth")->extend([

    "authenticate" => function($data) use($app) {

        $data = array_merge([
            "user"     => "",
            "email"    => "",
            "group"    => "",
            "password" => ""
        ], $data);

        if (!$data["password"]) return false;

        $user = $app->db->findOne("cockpit/accounts", [
            "user"     => $data["user"],
            "password" => $app->hash($data["password"]),
            "active"   => 1
        ]);

        if (count($user)) {

            $user = array_merge($data, (array)$user);

            unset($user["password"]);

            return $user;
        }

        return false;
    },

    "setUser" => function($user) use($app) {
        $app("session")->write('cockpit.app.auth', $user);
    },

    "getUser" => function() use($app) {
        return $app("session")->read('cockpit.app.auth', null);
    },

    "logout" => function() use($app) {
        $app("session")->delete('cockpit.app.auth');
    },

    "hasaccess" => function($resource, $action) use($app) {

        $user = $app("session")->read("cockpit.app.auth");
        
        //duy.ha: check user is login? redirect to login
        if(!$user){
            if(!in_array($app->route,['/auth/login','/auth/login/','/auth/check'])){
                if($app->route=='/api/tasks/getnotify')$app->route=$app->routeUrl('/');
                $app("session")->write('cockpit.app.redirect', $app->route);
                //print_r($app("session")->read('cockpit.app.redirect'));exit;
                $app->reroute('/auth/login');
            }            
        }
        if (isset($user["group"])) {

            if ($user["group"]=='admin' || $user["group"]=='superadmin') return true;
            if ($app("acl")->hasaccess($user["group"], $resource, $action)) return true;
        }

        return false;
    },

    "getGroupSetting" => function($setting, $default = null) use($app) {

        if ($user = $app("session")->read("cockpit.app.auth", null)) {
            if (isset($user["group"])) {

                $settings = $app["cockpit.acl.groups.settings"];

                return isset($settings[$user["group"]][$setting]) ? $settings[$user["group"]][$setting] : $default;
            }
        }

        return $default;
    }
]);


// ADMIN
if (COCKPIT_ADMIN && !COCKPIT_REST) include_once(__DIR__.'/admin.php');
