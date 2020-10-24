<?php
    // Japanese language data
    // Section: Common (Site-wide)
    
    class Lang {
        public function GetLanguageData() {
            // General Labels
            $lang['general_wip']            = "仕掛中！";

            // User Panel
            $lang['user_panel_title']           = "ユーザーパネル";
            $lang['user_panel_username']        = "ユーザー名";
            $lang['user_panel_password']        = "パスワード";
            $lang['user_panel_welcome']         = "ようこそ、";
            $lang['user_panel_button_login']    = "ログイン";
            $lang['user_panel_button_logout']   = "ログアウト";

            // Admin Panel
            $lang['admin_panel_title']      = "管理者パネル";

            // Top Page
            $lang['top_product_list_title'] = "商品一覧";

            // Notices
            $lang['notice_login_success']   = "ログイン成功しました。";
            $lang['notice_login_failed']    = "ログイン失敗です。ユーザー名とパスワードを再確認の上、もう1度ログインしてください。";

            return $lang;
        }
    }