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

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'view');
define('SECTION_PAGE', 'edit');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once(get_config('libroot') . 'view.php');
require_once(get_config('libroot') . 'group.php');

$view = new View(param_integer('id'));

if (!$USER->can_edit_view($view)) {
    throw new AccessDeniedException();
}

// Make sure we can edit view title for this type.
// If not, then we probably meant to edit blocks
if (!$view->can_edit_title()) {
    redirect('/view/blocks.php?id=' . $view->get('id'));
}

// If the view has been submitted, disallow editing
if ($view->is_submitted()) {
    $submittedto = $view->submitted_to();
    throw new AccessDeniedException(get_string('canteditsubmitted', 'view', $submittedto['name']));
}

$group = $view->get('group');
$institution = $view->get('institution');
$view->set_edit_nav();
$view->set_user_theme();

$new = param_boolean('new', 0);

if ($new) {
    define('TITLE', get_string('edittitleanddescription', 'view'));
}
else {
    define('TITLE', $view->get('title') . ': ' . get_string('edittitleanddescription', 'view'));
}

require_once('pieforms/pieform.php');

$formatstring = '%s (%s)';
$ownerformatoptions = array(
    FORMAT_NAME_FIRSTNAME => sprintf($formatstring, get_string('firstname'), $USER->get('firstname')),
    FORMAT_NAME_LASTNAME => sprintf($formatstring, get_string('lastname'), $USER->get('lastname')),
    FORMAT_NAME_FIRSTNAMELASTNAME => sprintf($formatstring, get_string('fullname'), full_name())
);

$displayname = display_name($USER);
if ($displayname !== '') {
    $ownerformatoptions[FORMAT_NAME_DISPLAYNAME] = sprintf($formatstring, get_string('preferredname'), $displayname);
}
$studentid = (string)get_field('artefact', 'title', 'owner', $USER->get('id'), 'artefacttype', 'studentid');
if ($studentid !== '') {
    $ownerformatoptions[FORMAT_NAME_STUDENTID] = sprintf($formatstring, get_string('studentid'), $studentid);
}

// Clean urls are only available for portfolio views owned by groups or users who already
// have their own clean profiles or group homepages.
if ($urlallowed = get_config('cleanurls') && $view->get('type') == 'portfolio' && !$institution) {
    if ($group) {
        $groupdata = get_record('group', 'id', $group);
        $urlallowed &= strlen($groupdata->urlid);
        $cleanurlbase = get_config('wwwroot') . get_config('cleanurlgroupdefault') . '/' . $groupdata->urlid . '/';
    }
    else {
        $userurlid = $USER->get('urlid');
        if ($urlallowed &= (!is_null($userurlid) && strlen($userurlid))) {
            $cleanurlbase = get_config('wwwroot') . get_config('cleanurluserdefault') . '/' . $userurlid . '/';
        }
    }
}

$editview = array(
    'name'     => 'editview',
    'method'   => 'post',
    'autofocus' => 'title',
    'autoselect' => $new ? 'title' : null,
    'plugintype' => 'core',
    'pluginname' => 'view',
    'elements' => array(
        'id' => array(
            'type'  => 'hidden',
            'value' => $view->get('id'),
        ),
        'new' => array(
            'type' => 'hidden',
            'value' => $new,
        ),
        'title' => array(
            'type'         => 'text',
            'title'        => get_string('title','view'),
            'defaultvalue' => $view->get('title'),
            'rules'        => array( 'required' => true ),
        ),
        'urlid' => array(
            'type'         => 'text',
            'title'        => get_string('viewurl', 'view'),
            'prehtml'      => '<span class="description">' . (isset($cleanurlbase) ? $cleanurlbase : '') . '</span> ',
            'description'  => get_string('viewurldescription', 'view') . ' ' . get_string('cleanurlallowedcharacters'),
            'defaultvalue' => $new ? null : $view->get('urlid'),
            'rules'        => array('maxlength' => 100, 'regex' => get_config('cleanurlvalidate')),
            'ignore'       => !$urlallowed || $new,
        ),
        'description' => array(
            'type'         => 'wysiwyg',
            'title'        => get_string('description','view'),
            'rows'         => 10,
            'cols'         => 70,
            'defaultvalue' => $view->get('description'),
            'rules'        => array('maxlength' => 65536),
        ),
        'tags'        => array(
            'type'         => 'tags',
            'title'        => get_string('tags'),
            'description'  => get_string('tagsdescprofile'),
            'defaultvalue' => $view->get('tags'),
            'help'         => true,
        ),
    ),
);

if ($group) {
    $grouproles = $USER->get('grouproles');
    if ($grouproles[$group] == 'admin') {
        $editview['elements']['locked'] = array(
            'type'         => 'checkbox',
            'title'        => get_string('Locked', 'view'),
            'description'  => get_string('lockedgroupviewdesc', 'view'),
            'defaultvalue' => $view->get('locked'),
            'disabled'     => $view->get('type') == 'grouphomepage', // This page unreachable for grouphomepage anyway
        );
    }
}

if (!($group || $institution)) {
    $default = $view->get('ownerformat');
    if (!$default) {
        $default = FORMAT_NAME_DISPLAYNAME;
    }
    $editview['elements']['ownerformat'] = array(
        'type'         => 'select',
        'title'        => get_string('ownerformat','view'),
        'description'  => get_string('ownerformatdescription','view'),
        'options'      => $ownerformatoptions,
        'defaultvalue' => $default,
        'rules'        => array('required' => true),
    );
}

$editview['elements']['submit'] = array(
    'type'  => 'submit',
    'value' => get_string('save'),
);


$editview = pieform($editview);

function editview_validate(Pieform $form, $values) {
    global $view;

    if (isset($values['urlid']) && $values['urlid'] != $view->get('urlid')) {
        if (strlen($values['urlid']) < 3) {
            $form->set_error('urlid', get_string('rule.minlength.minlength', 'pieforms', 3));
        }
        else if ($group = $view->get('group') and record_exists('view', 'group', $group, 'urlid', $values['urlid'])) {
            $form->set_error('urlid', get_string('groupviewurltaken', 'view'));
        }
        else if ($owner = $view->get('owner') and record_exists('view', 'owner', $owner, 'urlid', $values['urlid'])) {
            $form->set_error('urlid', get_string('userviewurltaken', 'view'));
        }
    }
}

function editview_submit(Pieform $form, $values) {
    global $new, $view, $SESSION, $urlallowed;

    $view->set('title', $values['title']);
    $view->set('description', $values['description']);
    $view->set('tags', $values['tags']);
    if (isset($values['locked'])) {
        $view->set('locked', (int)$values['locked']);
    }
    if (isset($values['ownerformat']) && $view->get('owner')) {
        $view->set('ownerformat', $values['ownerformat']);
    }
    if (isset($values['urlid'])) {
        $view->set('urlid', strlen($values['urlid']) == 0 ? null : $values['urlid']);
    }
    else if ($new && $urlallowed) {
        // Generate one automatically based on the title
        $desired = generate_urlid($values['title'], get_config('cleanurlviewdefault'), 3, 100);
        $ownerinfo = (object) array('owner' => $view->get('owner'), 'group' => $view->get('group'));
        $view->set('urlid', View::new_urlid($desired, $ownerinfo));
    }
    $SESSION->add_ok_msg(get_string('viewsavedsuccessfully', 'view'));
    $view->commit();
    redirect('/view/blocks.php?id=' . $view->get('id'));
}

$smarty = smarty(array(), array(), array(), array('sidebars' => false));
$smarty->assign('editview', $editview);
$smarty->assign('viewid', $view->get('id'));
$smarty->assign('viewtitle', $view->get('title'));
$smarty->assign('edittitle', $view->can_edit_title());
$smarty->assign('displaylink', $view->get_url());
$smarty->assign('new', $new);
if (get_config('viewmicroheaders')) {
    $smarty->assign('microheaders', true);
    $smarty->assign('microheadertitle', $view->display_title(true, false, false));
}
$smarty->display('view/edit.tpl');