<?php
    // English language data
    // Section: Common (Site-wide)
    
    class Lang {
        public function GetLanguageData() {
            // General Labels
            $lang['general_wip']            = "WORK IN PROGRESS!";

            // User Panel
            $lang['user_panel_title']           = "User Panel";
            $lang['user_panel_username']        = "Username";
            $lang['user_panel_password']        = "Password";
            $lang['user_panel_welcome']         = "Welcome, ";
            $lang['user_panel_button_login']    = "Login";
            $lang['user_panel_button_logout']   = "Logout";

            // Admin Panel
            $lang['admin_panel_title']      = "ADMIN PANEL";
            
            // Top Page
            $lang['top_product_list_title'] = "PRODUCT LIST";

            // Notices
            $lang['notice_login_success']   = "Login success.";
            $lang['notice_login_failed']    = "Login failed. Incorrect username and/or password.";

            return $lang;
        }
    }