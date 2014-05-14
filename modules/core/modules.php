<?php

/* INPUT */

class Hm_Handler_http_headers extends Hm_Handler_Module {
    public function process($data) {
        if (isset($data['language'])) {
            $data['http_headers'][] = 'Content-Language: '.substr($data['language'], 0, 2);
        }
        if ($this->request->tls) {
            $data['http_headers'][] = 'Strict-Transport-Security: max-age=31536000';
        }
        return $data;
    }
}

class Hm_Handler_process_language_setting extends Hm_Handler_Module {
    public function process($data) {
        list($success, $form) = $this->process_form(array('save_settings', 'language_setting'));
        if ($success) {
            $data['new_user_settings']['language_setting'] = $form['language_setting'];
        }
        else {
            $data['user_settings']['language'] = $this->user_config->get('language_setting', false);
        }
        return $data;
    }
}

class Hm_Handler_process_timezone_setting extends Hm_Handler_Module {
    public function process($data) {
        list($success, $form) = $this->process_form(array('save_settings', 'timezone_setting'));
        if ($success) {
            $data['new_user_settings']['timezone_setting'] = $form['timezone_setting'];
        }
        else {
            $data['user_settings']['timezone'] = $this->user_config->get('timezone_setting', false);
        }
        return $data;
    }
}

class Hm_Handler_save_user_settings extends Hm_Handler_Module {
    public function process($data) {
        list($success, $form) = $this->process_form(array('save_settings'));
        if ($success && isset($data['new_user_settings'])) {
            foreach ($data['new_user_settings'] as $name => $value) {
                $this->user_config->set($name, $value);
            }
            $user = $this->session->get('username', false);
            $path = $this->config->get('user_settings_dir', false);
            if ($user && $path) {
                $this->user_config->save($user);
                Hm_Msgs::add('Settings saved');
            }
            Hm_Page_Cache::flush($this->session);
        }
        return $data;
    }
}

class Hm_Handler_title extends Hm_Handler_Module {
    public function process($data) {
        $data['title'] = ucfirst($this->page);
        return $data;
    }
}

class Hm_Handler_language extends Hm_Handler_Module {
    public function process($data) {
        $data['language'] = $this->user_config->get('language_setting', 'en_US');
        //$data['language'] = $this->session->get('language', 'en_US');
        return $data;
    }
}

class Hm_Handler_date extends Hm_Handler_Module {
    public function process($data) {
        $data['date'] = date('Y-m-d h:i:s');
        return $data;
    }
}

class Hm_Handler_login extends Hm_Handler_Module {
    public function process($data) {
        if (!isset($this->request->post['create_hm_user'])) {
            list($success, $form) = $this->process_form(array('username', 'password'));
            if ($success) {
                $this->session->check($this->request, $form['username'], $form['password']);
                $this->session->set('username', $form['username']);
            }
            else {
                $this->session->check($this->request);
            }
            $data['session_type'] = get_class($this->session);
            if ($this->session->is_active()) {
                Hm_Page_Cache::load($this->session);
            }
        }
        return $data;
    }
}

class Hm_Handler_create_user extends Hm_Handler_Module {
    public function process($data) {
        list($success, $form) = $this->process_form(array('username', 'password', 'create_hm_user'));
        if ($success) {
            $this->session->create($this->request, $form['username'], $form['password']);
        }
        return $data;
    }
}

class Hm_Handler_load_user_data extends Hm_Handler_Module {
    public function process($data) {
        $user_data = $this->session->get('user_data', array());
        if (!empty($user_data)) {
            $this->user_config->reload($user_data);
        }
        else {
            $user = $this->session->get('username', false);
            $this->user_config->load($user);
            $pages = $this->user_config->get('saved_pages', array());
            if (!empty($pages)) {
                $this->session->set('saved_pages', $pages);
            }
        }
        return $data;
    }
}

class Hm_Handler_save_user_data extends Hm_Handler_Module {
    public function process($data) {
        $user_data = $this->user_config->dump();
        if (!empty($user_data)) {
            $this->session->set('user_data', $user_data);
        }
        return $data;
    }
}

class Hm_Handler_logout extends Hm_Handler_Module {
    public function process($data) {
        if (isset($this->request->post['logout']) && !$this->session->loaded) {
            $user = $this->session->get('username', false);
            $path = $this->config->get('user_settings_dir', false);
            $pages = $this->session->get('saved_pages', array());
            if (!empty($pages)) {
                $this->user_config->set('saved_pages', $pages);
            }
            if ($user && $path) {
                $this->user_config->save($user);
                Hm_Msgs::add('Saved user data on logout');
            }
            $this->session->destroy();
            Hm_Msgs::add('Session destroyed on logout');
        }
        return $data;
    }
}

class Hm_Handler_message_list_type extends Hm_Handler_Module {
    public function process($data) {
        $data['list_path'] = false;
        if (isset($this->request->get['list_path'])) {
            $path = $this->request->get['list_path'];
            if ($path == 'unread') {
                $data['list_path'] = 'unread';
            }
            elseif (preg_match("/^imap_\d+_[^\s]+/", $path)) {
                $data['list_path'] = $path;
                $parts = explode('_', $path, 3);
                $details = Hm_IMAP_List::dump(intval($parts[1]));
                $data['mailbox_list_title'] = array($details['name'], $parts[2]);
            }
            elseif (preg_match("/^pop3_\d+/", $path)) {
                $data['list_path'] = $path;
                $parts = explode('_', $path, 3);
                $details = Hm_POP3_List::dump(intval($parts[1]));
                if ($details['name'] == 'Default-Auth-Server') {
                    $details['name'] = 'Default';
                }
                $data['mailbox_list_title'] = array($details['name'], 'INBOX');
            }
        }
        if (isset($this->request->get['list_page'])) {
            $data['list_page'] = (int) $this->request->get['list_page'];
            if ($data['list_page'] < 1) {
                $data['list_page'] = 1;
            }
        }
        else {
            $data['list_page'] = 1;
        }
        return $data;
    }
}


/* OUTPUT */

class Hm_Output_title extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            return '<h1 class="title">HM3</h1>';
        }
    }
}

class Hm_Output_login extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            if (!$input['router_login_state']) {
                $res = '<form class="login_form" method="POST">'.
                    ' '.$this->trans('Username').': <input type="text" name="username" value="">'.
                    ' '.$this->trans('Password').': <input type="password" name="password">'.
                    ' <input type="submit" value="Login" />';
                if (($input['session_type'] == 'Hm_DB_Session_DB_Auth' || $input['session_type'] == 'Hm_PHP_Session_DB_Auth') &&
                    $input['router_page_name'] == 'home') {
                    $res .= ' <input type="submit" name="create_hm_user" value="Create" />';
                }
                $res .= '</form>';
                return $res;
            }
        }
        return '';
    }
}

class Hm_Output_date extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            return '<div class="date">'.$this->html_safe($input['date']).'</div>';
        }
    }
}

class Hm_Output_logout extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' && $input['router_login_state']) {
            return '<form class="logout_form" method="POST"><input type="submit" class="logout" name="logout" value="Logout" /></form>';
        }
    }
}

class Hm_Output_msgs extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            $res = '';
            $msgs = Hm_Msgs::get();
            $res .= '<div class="sys_messages">';
            if (!empty($msgs)) {
                $res .= implode(',', array_map(function($v) {
                    if (preg_match("/ERR/", $v)) {
                        return sprintf('<span class="err">%s</span>', substr($this->html_safe($v), 3));
                    }
                    else {
                        return $this->html_safe($v);
                    }
                }, $msgs));
            }
            $res .= '</div>';
            return $res;
        }
        return '';
    }
}

class Hm_Output_header_start extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            $lang = '';
            if ($this->lang) {
                $lang = 'lang='.strtolower(str_replace('_', '-', $this->lang));
            }
            return '<!DOCTYPE html><html '.$lang.'><head>';
        }
        elseif ($format == 'CLI') {
            return sprintf("\nHM3 CLI Interface\n\n");
        }
    }
}

class Hm_Output_header_end extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '</head><body>';
        }
    }
}

class Hm_Output_content_start extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<body>';
        }
    }
}

class Hm_Output_header_content extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<title>HM3</title><meta charset="utf-8" />'.
                '<base href="'.$this->html_safe($input['router_url_path']).'" />';
        }
    }
}

class Hm_Output_settings_link extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            return '<a class="settings_link" href="'.$this->html_safe($input['router_url_path']).'?page=settings">'.$this->trans('Settings').'</a>';
        }
    }
}

class Hm_Output_homepage_link extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            return '<a class="home_link" href="'.$this->html_safe($input['router_url_path']).'">'.$this->trans('Home').'</a>';
        }
    }
}

class Hm_Output_header_css extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            $res = '';
            if (DEBUG_MODE) {
                foreach (glob('modules/*', GLOB_ONLYDIR | GLOB_MARK) as $name) {
                    if (is_readable(sprintf("%ssite.css", $name))) {
                        $res .= '<link href="'.sprintf("%ssite.css", $name).'" media="all" rel="stylesheet" type="text/css" />';
                    }
                }
            }
            else {
                $res .= '<link href="site.css" media="all" rel="stylesheet" type="text/css" />';
            }
            return $res;
        }
    }
}

class Hm_Output_page_js extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            if (DEBUG_MODE) {
                $res = '';
                foreach (glob('modules/*', GLOB_ONLYDIR | GLOB_MARK) as $name) {
                    if (is_readable(sprintf("%ssite.js", $name))) {
                        $res .= '<script type="text/javascript" src="'.sprintf("%ssite.js", $name).'"></script>';
                    }
                }
                return $res;
            }
            else {
                return '<script type="text/javascript" src="site.js"></script>';
            }
        }
    }
}

class Hm_Output_content_end extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<div class="elapsed"></div></body></html>';
        }
    }
}

class Hm_Output_jquery_table extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<script type="text/javascript" src="modules/imap/jquery.tablesorter.min.js"></script>';
        }
        return '';
    }
}

class Hm_Output_jquery extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<script type="text/javascript" src="modules/core/jquery-1.11.0.min.js"></script>';
        }
        return '';
    }
}

class Hm_Output_js_data extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<script type="text/javascript">'.
                'var hm_url_path = "'.$input['router_url_path'].'";'.
                'var hm_page_name = "'.$input['router_page_name'].'";'.
                'var hm_list_path = "'.(isset($input['list_path']) ? $input['list_path'] : '').'";'.
                '</script>';
        }
    }
}

class Hm_Output_loading_icon extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<div class="loading_icon"><img alt="Loading..." src="images/ajax-loader.gif" width="16" height="16" /></div>';
        }
    }
}

class Hm_Output_start_settings_form extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<div class="user_settings"><div class="content_title">Settings</div><br />'.
                '<form method="POST" action=""><table class="settings_table">';
        }
    }
}

class Hm_Output_language_setting extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            $langs = array(
                'en_US' => 'English',
                'es_ES' => 'Spanish'
            );
            if (isset($input['user_settings']['language'])) {
                $mylang = $input['user_settings']['language'];
            }
            else {
                $mylang = false;
            }
            $res = '<tr><td>Interface Language</td><td><select name="language_setting">';
            foreach ($langs as $id => $lang) {
                $res .= '<option ';
                if ($id == $mylang) {
                    $res .= 'selected="selected" ';
                }
                $res .= 'value="'.$id.'">'.$lang.'</option>';
            }
            $res .= '</select></td></tr>';
            return $res;
        }
    }
}

class Hm_Output_timezone_setting extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            $zones = timezone_identifiers_list();
            if (isset($input['user_settings']['timezone'])) {
                $myzone = $input['user_settings']['timezone'];
            }
            else {
                $myzone = false;
            }
            $res = '<tr><td>Timezone</td><td><select name="timezone_setting">';
            foreach ($zones as $zone) {
                $res .= '<option ';
                if ($zone == $myzone) {
                    $res .= 'selected="selected" ';
                }
                $res .= 'value="'.$zone.'">'.$zone.'</option>';
            }
            $res .= '</select></td></tr>';
            return $res;
        }
    }
}

class Hm_Output_end_settings_form extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<tr><td colspan="2" class="submit_cell">'.
                '<input class="save_settings" type="submit" name="save_settings" value="Save" />'.
                '</tr></table></form></div>';
        }
    }
}

class Hm_Output_toolbar_start extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '<div class="toolbar">';
        }
    }
}

class Hm_Output_toolbar_end extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5' ) {
            return '</div>';
        }
    }
}

class Hm_Output_servers_link extends Hm_Output_Module {
    protected function output($input, $format) {
        if ($format == 'HTML5') {
            return '<a class="server_link" href="'.$this->html_safe($input['router_url_path']).'?page=servers">'.$this->trans('Servers').'</a>';
        }
    }
}

class Hm_Output_folder_list_start extends Hm_Output_Module {
    protected function output($input, $format) {
        $res = '<table><tr><td class="folder_cell"><div class="folder_list">';
        $res .= '<div class="src_name">Everything</div><ul class="folders">'.
            '<li><img class="account_icon" src="images/open_iconic/globe-2x.png" alt="" /> '.
            '<a class="unread_link" href="?page=home">'.$this->trans('Home').'</a></li>'.
            '<li><img class="account_icon" src="images/open_iconic/globe-2x.png" alt="" /> '.
            '<a class="unread_link" href="?page=message_list&amp;list_path=unread">'.$this->trans('Unread').'</a></li>'.
            '</ul>';
        if (isset($input['folder_sources'])) {
            foreach ($input['folder_sources'] as $src) {
                $name = ucwords(str_replace(array('imap', 'pop3', '_'), array('IMAP', 'POP3', ' '), $src));
                $res .= '<div class="src_name">'.$this->html_safe($name).'</div>';
                $res .= '<div class="'.$src.'">';
                $cache = Hm_Page_Cache::get($src);
                if ($cache) {
                    $res .= $cache;
                }
                $res .= '</div>';
            }
        }
        $res .= '</div></td><td class="content_cell">';
        return $res;
    }
}

class Hm_Output_folder_list_end extends Hm_Output_Module {
    protected function output($input, $format) {
        return '</td></tr></table>';
    }
}

class Hm_Output_server_summary_start extends Hm_Output_Module {
    protected function output($input, $format) {
        $res = '<div class="server_summary"><div class="content_title">Summary</div>';
        $res .= '<table><thead><tr><th>Type</th><th>Name</th><th>Address</th><th>Port</th>'.
                '<th>TLS</th></tr></thead><tbody>';
        return $res;
    }
}

class Hm_Output_server_summary_end extends Hm_Output_Module {
    protected function output($input, $format) {
        return '</tbody></table></div>';
    }
}

?>
