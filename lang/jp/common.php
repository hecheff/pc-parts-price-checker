<?php
    // Japanese language data
    // Section: Common (Site-wide)
    
    class Lang {
        public function GetLanguageData() {
            // General Labels
            // Top Page
            $lang['top_product_list_title'] = "PRODUCT LIST";

            // Notices
            $lang['notice_login_success']   = "ログイン成功しました。";
            $lang['notice_login_failed']    = "ログイン失敗です。ユーザー名とパスワードを再確認の上、もう1度ログインしてください。";

            return $lang;
        }
    }