<?php
/**
 * Mahara: Electronic portfolio, weblog, resume builder and social networking
 * Copyright (C) 2006-2009 Catalyst IT Ltd and others; see:
 *                         http://wiki.mahara.org/Contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    mahara
 * @subpackage core
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2006-2009 Catalyst IT Ltd http://catalyst.net.nz
 *
 */

define('INTERNAL', 1);
define('PUBLIC', 1);
define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'site');
define('SECTION_PAGE', 'contact');
require('init.php');
require_once('pieforms/pieform.php');
define('TITLE', get_string('contactus'));

if ($USER->is_logged_in()) {
    $userid = $USER->get('id');
    $name = display_name($userid);
    $email = $USER->get('email');
}
else {
    $userid = 0;
    $name = '';
    $email = '';
}

$elements = array(
    'name' => array(
        'type'  => 'text',
        'title' => get_string('name'),
        'defaultvalue' => $name,
        'rules' => array(
            'required'    => true
        ),
    ),
    'email' => array(
        'type'  => 'text',
        'title' => get_string('email'),
        'defaultvalue' => $email,
        'rules' => array(
            'required'    => true
        ),
    ),
    'subject' => array(
        'type'  => 'text',
        'title' => get_string('subject'),
        'defaultvalue' => '',
    ),
    'message' => array(
        'type'  => 'textarea',
        'rows'  => 10,
        'cols'  => 60,
        'title' => get_string('message'),
        'defaultvalue' => '',
        'rules' => array(
            'required'    => true
        ),
    )
);

if (get_config('captchaoncontactform') && !$USER->is_logged_in()) {
    $elements['captcha'] = array(
        'type'  => 'captcha',
        'title' => get_string('captchatitle'),
        'description' => get_string('captchadescription'),
        'rules' => array('required' => true)
    );
}

$elements['userid'] = array(
    'type'  => 'hidden',
    'value' => $userid,
);
$elements['submit'] = array(
    'type'  => 'submit',
    'value' => get_string('sendmessage'),
);

$contactform = pieform(array(
    'name'     => 'contactus',
    'method'   => 'post',
    'action'   => '',
    'elements' => $elements
));

function contactus_validate(Pieform $form, $values) {
    global $USER;
    if (get_config('captchaoncontactform') && !$USER->is_logged_in() && !$values['captcha']) {
        $form->set_error('captcha', get_string('captchaincorrect'));
    }
}

function contactus_submit(Pieform $form, $values) {
    global $SESSION;
    $data = new StdClass;
    $data->fromname    = $values['name'];
    $data->fromemail   = $values['email'];
    $data->subject     = $values['subject'];
    $data->message     = $values['message'];
    if ($values['userid']) {
        $data->fromuser = $values['userid'];
    }
    require_once('activity.php');
    activity_occurred('contactus', $data);
    $SESSION->add_ok_msg(get_string('messagesent'));
    redirect();
}

$smarty = smarty();
$smarty->assign('PAGEHEADING', TITLE);
$smarty->assign('form', $contactform);
$smarty->display('form.tpl');

?>
