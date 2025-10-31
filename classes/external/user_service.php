<?php

namespace local_wordpress_integration\external;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/user/lib.php');

use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use context_system;
use moodle_exception;
use enrol_get_plugin;

class user_service extends \core_external\external_api {

    public static function sync_user_and_enrol_parameters() {
        return new external_function_parameters([
            'email' => new external_value(PARAM_EMAIL, 'Correo electrónico del usuario'),
            'firstname' => new external_value(PARAM_NOTAGS, 'Nombre', VALUE_DEFAULT, ''),
            'lastname' => new external_value(PARAM_NOTAGS, 'Apellido', VALUE_DEFAULT, ''),
            'username' => new external_value(PARAM_USERNAME, 'Username', VALUE_DEFAULT, ''),
            'password' => new external_value(PARAM_RAW, 'Contraseña', VALUE_DEFAULT, ''),
            'courseid' => new external_value(PARAM_INT, 'ID del curso donde matricular'),
        ]);
    }

    public static function sync_user_and_enrol($email, $firstname = '', $lastname = '', $username = '', $password = '', $courseid) {
        global $DB, $CFG;

        self::validate_parameters(self::sync_user_and_enrol_parameters(), [
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => $username,
            'password' => $password,
            'courseid' => $courseid,
        ]);

        $context = context_system::instance();
        self::validate_context($context);

        $user = $DB->get_record('user', ['email' => $email], '*', IGNORE_MISSING);

        if (!$user) {
            if (empty($username)) {
                // Si el usuario esta vacio crea el usuario a partir del correo electronico
                $username = strstr($email, '@', true);
            }
            if (empty($password)) {
                $password = random_string(10);
            }

            $newuser = new \stdClass();
            $newuser->username = strtolower($username);
            $newuser->firstname = $firstname ?: 'Usuario';
            $newuser->lastname = $lastname ?: 'Nuevo';
            $newuser->email = strtolower($email);
            $newuser->password = hash_internal_user_password($password);
            $newuser->auth = 'manual';
            $newuser->confirmed = 1;
            $newuser->mnethostid = $DB->get_field('mnet_host', 'id', ['wwwroot' => $CFG->wwwroot]);
            $newuser->password_change = 1;

            $newuser->id = user_create_user($newuser, false, false);
            update_internal_user_password($newuser, $password);
            $user = $newuser;
            $created = true;
        } else {
            $created = false;
        }

        
        
        $enrol = enrol_get_plugin('manual');
        if (!$enrol) {
            throw new moodle_exception('manualenrolnotfound', 'local_wordpress_integration');
        }

        $instances = enrol_get_instances($courseid, false);
        $manualinstance = null;
        foreach ($instances as $instance) {
            if ($instance->enrol === 'manual') {
                $manualinstance = $instance;
                break;
            }
        }

        if (!$manualinstance) {
            throw new moodle_exception('noenrolinstance', 'local_wordpress_integration');
        }

        // Evitar duplicar matrícula
        if (!$DB->record_exists('user_enrolments', ['userid' => $user->id, 'enrolid' => $manualinstance->id])) {
            $enrol->enrol_user($manualinstance, $user->id, 5); // rol 5 = estudiante
        }

        return [
            'status' => 'ok',
            'userid' => $user->id,
            'created' => $created,
            'courseid' => $courseid,
            'message' => $created ? 'Usuario creado y matriculado' : 'Usuario existente y matriculado'
        ];
    }

    public static function sync_user_and_enrol_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Estado'),
            'userid' => new external_value(PARAM_INT, 'ID del usuario'),
            'created' => new external_value(PARAM_BOOL, 'Indica si el usuario fue creado'),
            'courseid' => new external_value(PARAM_INT, 'ID del curso'),
            'message' => new external_value(PARAM_TEXT, 'Mensaje de resultado'),
        ]);
    }
}