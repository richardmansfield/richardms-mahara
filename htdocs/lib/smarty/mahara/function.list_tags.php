<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {list_tags} function plugin
 *
 * Type:     function<br>
 * Name:     str<br>
 * Date:     June 22, 2006<br>
 * Purpose:  Render a list of tags
 * @author   Catalyst IT Ltd
 * @version  1.0
 * @param array
 * @param Smarty
 * @return Internationalized string
 */
function smarty_function_list_tags($params, &$smarty) {
    global $USER;
    if (empty($params['tags']) || !is_array($params['tags'])) {
        return '';
    }

    if (empty($params['owner']) || $params['owner'] != $USER->get('id')) {
        return join(', ', array_map('hsc', $params['tags']));
    }

    foreach ($params['tags'] as &$t) {
        $t = '<a class="tag" href="' . get_config('wwwroot') . 'tags.php?tag=' . urlencode($t) . '">' . hsc(str_shorten_text($t, 50)) . '</a>';
    }
    return join(', ', $params['tags']);
}

?>