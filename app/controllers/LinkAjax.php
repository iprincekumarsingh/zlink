<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\BiolinkTheme;
use Altum\Response;
use Altum\Routing\Router;

class LinkAjax extends Controller {

    public function index() {
        Authentication::guard();

        if(!empty($_POST) && (Csrf::check('token') || Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch($_POST['request_type']) {

                /* Status toggle */
                case 'is_enabled_toggle': $this->is_enabled_toggle(); break;

                /* Create */
                case 'create': $this->create(); break;

                /* Update */
                case 'update': $this->update(); break;

                /* Delete */
                case 'delete': $this->delete(); break;

                /* Duplicate */
                case 'duplicate': $this->duplicate(); break;

            }

        }

        die($_POST['request_type']);
    }

    private function is_enabled_toggle() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];

        /* Get the current status */
        $link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links', ['link_id', 'is_enabled']);

        if($link) {
            $new_is_enabled = (int) !$link->is_enabled;

            db()->where('link_id', $link->link_id)->update('links', ['is_enabled' => $new_is_enabled]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);
            \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $_POST['link_id']);

            Response::json('', 'success');
        }
    }

    private function create() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['type'] = trim(Database::clean_string($_POST['type']));

        /* Check for possible errors */
        if(!in_array($_POST['type'], ['link', 'biolink'])) {
            die();
        }

        switch($_POST['type']) {
            case 'link':
                $this->create_link();
                break;

            case 'biolink':
                $this->create_biolink();
                break;
        }

        die();
    }

    private function create_link() {
        $_POST['location_url'] = mb_substr(trim(Database::clean_string($_POST['location_url'])), 0, 2048);
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url']), '-', false) : false;
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Middlewares\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        if(empty($_POST['location_url'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        $this->check_url($_POST['url']);

        $this->check_location_url($_POST['location_url']);

        /* Make sure that the user didn't exceed the limit */
        $user_total_links = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'link'")->fetch_object()->total;
        if($this->user->plan_settings->links_limit != -1 && $user_total_links >= $this->user->plan_settings->links_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('create_link_modal.error_message.url_exists'), 'error');
            }

        }

        if(empty($errors)) {
            $url = $_POST['url'] ? $_POST['url'] : string_generate(10);
            $type = 'link';
            $settings = json_encode([
                'clicks_limit' => null,
                'expiration_url' => null,
                'password' => null,
                'sensitive_content' => false,
                'targeting_type' => null,
            ]);

            /* Generate random url if not specified */
            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = string_generate(10);
            }

            /* Insert to database */
            $link_id = db()->insert('links', [
                'user_id' => $this->user->user_id,
                'domain_id' => $domain_id,
                'type' => $type,
                'url' => $url,
                'location_url' => $_POST['location_url'],
                'settings' => $settings,
                'datetime' => \Altum\Date::$date,
            ]);

            Response::json('', 'success', ['url' => url('link/' . $link_id)]);
        }
    }

    private function create_biolink() {
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url']), '-', false) : false;

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Middlewares\Authentication::is_admin()) {
            Response::json(l('create_biolink_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Make sure that the user didn't exceed the limit */
        $user_total_biolinks = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'biolink'")->fetch_object()->total;
        if($this->user->plan_settings->biolinks_limit != -1 && $user_total_biolinks >= $this->user->plan_settings->biolinks_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('create_biolink_modal.error_message.url_exists'), 'error');
            }
        }

        /* Start the creation process */
        $url = $_POST['url'] ? $_POST['url'] : string_generate(10);
        $type = 'biolink';
        $settings = json_encode([
            'verified_location' => 'top',
            'favicon' => null,
            'background_type' => 'preset',
            'background' => 'one',
            'text_color' => 'white',
            'display_branding' => true,
            'branding' => [
                'url' => '',
                'name' => ''
            ],
            'seo' => [
                'block' => false,
                'title' => '',
                'meta_description' => '',
                'image' => '',
            ],
            'utm' => [
                'medium' => '',
                'source' => '',
            ],
            'font' => null,
            'font_size' => 16,
            'password' => null,
            'sensitive_content' => false,
            'leap_link' => null,
            'custom_css' => null,
            'custom_js' => null,
        ]);

        /* Generate random url if not specified */
        while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
            $url = string_generate(10);
        }

        $this->check_url($_POST['url']);

        /* Insert to database */
        $link_id = db()->insert('links', [
            'user_id' => $this->user->user_id,
            'domain_id' => $domain_id,
            'type' => $type,
            'url' => $url,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        Response::json('', 'success', ['url' => url('link/' . $link_id)]);
    }

    private function update() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        if(!empty($_POST)) {
            $_POST['type'] = trim(Database::clean_string($_POST['type']));

            /* Check for possible errors */
            if(!in_array($_POST['type'], ['link', 'biolink'])) {
                die();
            }

            switch($_POST['type']) {
                case 'link':
                    $this->update_link();
                    break;

                case 'biolink':
                    $this->update_biolink();
                    break;
            }
        }

        die();
    }

    private function update_biolink() {
        $_POST['project_id'] = empty($_POST['project_id']) ? null : (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url']), '-', false) : false;

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Middlewares\Authentication::is_admin()) {
            Response::json(l('create_biolink_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Check for any errors */
        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
            die();
        }

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        $link->settings = json_decode($link->settings);


        if($_POST['url'] == $link->url) {
            $url = $link->url;

            if($link->domain_id != $domain_id) {
                if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                    Response::json(l('create_biolink_modal.error_message.url_exists'), 'error');
                }
            }

        } else {
            $url = $_POST['url'] ? $_POST['url'] : string_generate(10);

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('create_biolink_modal.error_message.url_exists'), 'error');
            }

            /* Generate random url if not specified */
            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = string_generate(10);
            }

            $this->check_url($_POST['url']);
        }

        /* Image uploads */
        $image_allowed_extensions = [
            'seo_image' => ['jpg', 'jpeg', 'png', 'gif'],
            'favicon' => ['png', 'gif', 'ico'],
            'background' => ['jpg', 'jpeg', 'png', 'svg', 'gif']
        ];
        $image = [
            'seo_image' => !empty($_FILES['seo_image']['name']) && !isset($_POST['seo_image_remove']),
            'favicon' => !empty($_FILES['favicon']['name']) && !isset($_POST['favicon_remove']),
        ];
        $image_upload_path = [
            'seo_image' => 'block_images',
            'favicon' => 'favicons',
        ];
        $image_uploaded_file = [
            'seo_image' => $link->settings->seo->image,
            'favicon' => $link->settings->favicon
        ];
        $image_url = [
            'seo_image' => null,
            'favicon' => null
        ];

        foreach(['favicon', 'seo_image'] as $image_key) {
            if($image[$image_key]) {
                $file_name = $_FILES[$image_key]['name'];
                $file_extension = explode('.', $file_name);
                $file_extension = mb_strtolower(end($file_extension));
                $file_temp = $_FILES[$image_key]['tmp_name'];

                if($_FILES[$image_key]['error'] == UPLOAD_ERR_INI_SIZE) {
                    Response::json(sprintf(l('global.error_message.file_size_limit'), settings()->links->{$image_key . '_size_limit'}), 'error');
                }

                if($_FILES[$file_name]['error'] && $_FILES[$file_name]['error'] != UPLOAD_ERR_INI_SIZE) {
                    Response::json(l('global.error_message.file_upload'), 'error');
                }

                if(!in_array($file_extension, $image_allowed_extensions[$image_key])) {
                    Response::json(l('global.error_message.invalid_file_type'), 'error');
                }

                if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                    if(!is_writable(UPLOADS_PATH . $image_upload_path[$image_key] . '/')) {
                        Response::json(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . $image_upload_path[$image_key] . '/'), 'error');
                    }
                }

                if($_FILES[$image_key]['size'] > settings()->links->{$image_key . '_size_limit'} * 1000000) {
                    Response::json(sprintf(l('global.error_message.file_size_limit'), settings()->links->{$image_key . '_size_limit'}), 'error');
                }

                /* Generate new name for image */
                $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                /* Try to compress the image */
                if(\Altum\Plugin::is_active('image-optimizer')) {
                    \Altum\Plugin\ImageOptimizer::optimize($file_temp, $image_new_name);
                }

                /* Offload uploading */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    try {
                        $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                        /* Delete current image */
                        $s3->deleteObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => 'uploads/' . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key],
                        ]);

                        /* Upload image */
                        $result = $s3->putObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => 'uploads/' . $image_upload_path[$image_key] . '/' . $image_new_name,
                            'ContentType' => mime_content_type($file_temp),
                            'SourceFile' => $file_temp,
                            'ACL' => 'public-read'
                        ]);
                    } catch (\Exception $exception) {
                        Response::json($exception->getMessage(), 'error');
                    }
                }

                /* Local uploading */
                else {
                    /* Delete current image */
                    if(!empty($image_uploaded_file[$image_key]) && file_exists(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key])) {
                        unlink(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key]);
                    }

                    /* Upload the original */
                    move_uploaded_file($file_temp, UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_new_name);
                }

                $image_uploaded_file[$image_key] = $image_new_name;
            }

            /* Check for the removal of the already uploaded file */
            if(isset($_POST[$image_key . '_remove'])) {

                /* Offload deleting */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/' . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key],
                    ]);
                }

                /* Local deleting */
                else {
                    /* Delete current file */
                    if(!empty($image_uploaded_file[$image_key]) && file_exists(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key])) {
                        unlink(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key]);
                    }
                }

                $image_uploaded_file[$image_key] = null;
            }

            $image_url[$image_key] = $image_uploaded_file[$image_key] ? UPLOADS_FULL_URL . $image_upload_path[$image_key] . '/' . $image_uploaded_file[$image_key] : null;
        }

        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];
        $biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';
        $_POST['background_type'] = array_key_exists($_POST['background_type'], $biolink_backgrounds) ? $_POST['background_type'] : 'preset';
        $background = 'one';

        switch($_POST['background_type']) {
            case 'preset':
            case 'preset_abstract':
                $background = array_key_exists($_POST['background'], $biolink_backgrounds[$_POST['background_type']]) ? $_POST['background'] : 'one';
                break;

            case 'color':

                $background = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background']) ? '#000' : $_POST['background'];

                break;

            case 'gradient':

                $background_color_one = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color_one']) ? '#000' : $_POST['background_color_one'];
                $background_color_two = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color_two']) ? '#000' : $_POST['background_color_two'];

                break;

            case 'image':

                $background = (bool) !empty($_FILES['background']['name']);

                /* Check for any errors on the logo image */
                if($background) {
                    $background_file_extension = explode('.', $_FILES['background']['name']);
                    $background_file_extension = mb_strtolower(end($background_file_extension));
                    $background_file_temp = $_FILES['background']['tmp_name'];

                    if($_FILES['background']['error'] == UPLOAD_ERR_INI_SIZE) {
                        Response::json(sprintf(l('global.error_message.file_size_limit'), settings()->links->background_size_limit), 'error');
                    }

                    if($_FILES['background']['error'] && $_FILES['background']['error'] != UPLOAD_ERR_INI_SIZE) {
                        Response::json(l('global.error_message.file_upload'), 'error');
                    }

                    if(!is_writable(UPLOADS_PATH . 'backgrounds/')) {
                        Response::json(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'backgrounds/'), 'error');
                    }

                    if(!in_array($background_file_extension, $image_allowed_extensions['background'])) {
                        Response::json(l('global.error_message.invalid_file_type'), 'error');
                    }

                    if($_FILES['background']['size'] > settings()->links->background_size_limit * 1000000) {
                        Response::json(sprintf(l('global.error_message.file_size_limit'), settings()->links->background_size_limit), 'error');
                    }

                    /* Generate new name */
                    $background_new_name = md5(time() . rand()) . '.' . $background_file_extension;

                    /* Try to compress the image */
                    if(\Altum\Plugin::is_active('image-optimizer')) {
                        \Altum\Plugin\ImageOptimizer::optimize($background_file_temp, $background_new_name);
                    }

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Delete current image */
                            if(is_string($link->settings->background)) {
                                $s3->deleteObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => 'uploads/backgrounds/' . $link->settings->background,
                                ]);
                            }

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/backgrounds/' . $background_new_name,
                                'ContentType' => mime_content_type($background_file_temp),
                                'SourceFile' => $background_file_temp,
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Response::json($exception->getMessage(), 'error');
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Delete current file */
                        if(is_string($link->settings->background) && !empty($link->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $link->settings->background)) {
                            unlink(UPLOADS_PATH . 'backgrounds/' . $link->settings->background);
                        }

                        /* Upload the original */
                        move_uploaded_file($background_file_temp, UPLOADS_PATH . 'backgrounds/' . $background_new_name);
                    }

                    $background = $background_new_name;
                }

                break;
        }

        /* Delete existing background file if needed */
        if($_POST['background_type'] != 'image' && $link->settings->background_type == 'image' && is_string($link->settings->background)) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/backgrounds/' . $link->settings->background,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($link->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $link->settings->background)) {
                    unlink(UPLOADS_PATH . 'backgrounds/' . $link->settings->background);
                }
            }
        }

        $_POST['display_branding'] = (bool) isset($_POST['display_branding']);
        $_POST['verified_location'] = in_array($_POST['verified_location'], ['top', 'bottom']) ? Database::clean_string($_POST['verified_location']) : 'top';
        $_POST['branding_name'] = mb_substr(trim(Database::clean_string($_POST['branding_name'])), 0, 128);
        $_POST['branding_url'] = mb_substr(trim(Database::clean_string($_POST['branding_url'])), 0, 2048);
        $_POST['seo_block'] = (bool) isset($_POST['seo_block']);
        $_POST['seo_title'] = trim(Database::clean_string(mb_substr($_POST['seo_title'], 0, 70)));
        $_POST['seo_meta_description'] = trim(Database::clean_string(mb_substr($_POST['seo_meta_description'], 0, 160)));
        $_POST['utm_medium'] = mb_substr(trim(Database::clean_string($_POST['utm_medium'])), 0, 128);
        $_POST['utm_source'] = mb_substr(trim(Database::clean_string($_POST['utm_source'])), 0, 128);
        $_POST['password'] = !empty($_POST['qweasdzxc']) ?
            ($_POST['qweasdzxc'] != $link->settings->password ? password_hash($_POST['qweasdzxc'], PASSWORD_DEFAULT) : $link->settings->password)
            : null;
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);
        $_POST['custom_css'] = mb_substr(trim(filter_var($_POST['custom_css'], FILTER_SANITIZE_STRING)), 0, 8192);
        $_POST['custom_js'] = mb_substr(trim($_POST['custom_js']), 0, 8192);
        $_POST['leap_link'] = mb_substr(trim(Database::clean_string($_POST['leap_link'] ?? null)), 0, 2048);
        $this->check_location_url($_POST['leap_link'], true);

        /* Make sure the font is ok */
        $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php';
        $_POST['font'] = !array_key_exists($_POST['font'], $biolink_fonts) ? false : Database::clean_string($_POST['font']);
        $_POST['font_size'] = (int) $_POST['font_size'] < 14 || (int) $_POST['font_size'] > 22 ? 16 : (int) $_POST['font_size'];

        /* Get available themes */
        $biolinks_themes = (new BiolinkTheme())->get_biolinks_themes();
        $_POST['biolink_theme_id'] = isset($_POST['biolink_theme_id']) && array_key_exists($_POST['biolink_theme_id'], $biolinks_themes) ? $_POST['biolink_theme_id'] : null;

        /* Set the new settings variable */
        $settings = [
            'verified_location' => $_POST['verified_location'],
            'background_type' => $_POST['background_type'],
            'background' => $background ? $background : $link->settings->background,
            'background_color_one' => $background_color_one ?? null,
            'background_color_two' => $background_color_two ?? null,
            'favicon' => $image_uploaded_file['favicon'],
            'text_color' => $_POST['text_color'],
            'display_branding' => $_POST['display_branding'],
            'branding' => [
                'name' => $_POST['branding_name'],
                'url' => $_POST['branding_url'],
            ],
            'seo' => [
                'block' => $_POST['seo_block'],
                'title' => $_POST['seo_title'],
                'meta_description' => $_POST['seo_meta_description'],
                'image' => $image_uploaded_file['seo_image'],
            ],
            'utm' => [
                'medium' => $_POST['utm_medium'],
                'source' => $_POST['utm_source'],
            ],
            'font' => $_POST['font'],
            'font_size' => $_POST['font_size'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
            'leap_link' => $_POST['leap_link'],
            'custom_css' => $_POST['custom_css'],
            'custom_js' => $_POST['custom_js'],
        ];

        /* Check if we need to override defaults for a new theme */
        if($_POST['biolink_theme_id'] && $link->biolink_theme_id != $_POST['biolink_theme_id']) {
            $biolink_theme = $biolinks_themes[$_POST['biolink_theme_id']];

            /* Save settings for biolink page */
            $settings = array_merge($settings, (array) $biolink_theme->settings->biolink);

            /* Save settings for all existing blocks */
            $themable_blocks = ['link', 'mail', 'paypal', 'phone_collector', 'rss_feed', 'vcard', 'cta', 'youtube_feed', 'share', 'file'];
            $themable_blocks_sql = "'" . implode('\', \'', $themable_blocks) . "'";

            $biolink_blocks_result = database()->query("SELECT `biolink_block_id`, `settings` FROM `biolinks_blocks` WHERE `link_id` = {$link->link_id} AND `type` IN ({$themable_blocks_sql})");
            while($biolink_block = $biolink_blocks_result->fetch_object()) {
                $biolink_block->settings = json_decode($biolink_block->settings);
                $new_biolink_block_settings = json_encode(array_merge((array) $biolink_block->settings, (array) $biolink_theme->settings->biolink_block));

                db()->where('biolink_block_id', $biolink_block->biolink_block_id)->update('biolinks_blocks', [
                    'settings' => $new_biolink_block_settings,
                ]);
            }

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
            \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);
        }

        /* Prepare settings for JSON insertion */
        $settings = json_encode($settings);

        /* Update the record */
        db()->where('link_id', $link->link_id)->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'biolink_theme_id' => $_POST['biolink_theme_id'],
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'settings' => $settings,
        ]);

        /* Update the biolink page blocks if needed */
        if($link->project_id != $_POST['project_id']) {
            db()->where('biolink_id', $link->link_id)->update('links', ['project_id' => $_POST['project_id']]);
        }

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        Response::json(l('global.success_message.update2'), 'success', [
            'url' => $url,
            'image_prop' => true,
            'seo_image_url' => $image_url['seo_image'],
            'favicon_url' => $image_url['favicon']
        ]);

    }

    private function update_link() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['project_id'] = empty($_POST['project_id']) ? null : (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug(Database::clean_string($_POST['url']), '-', false) : false;
        $_POST['location_url'] = mb_substr(trim(Database::clean_string($_POST['location_url'])), 0, 2048);
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }
        $_POST['expiration_url'] = mb_substr(trim(Database::clean_string($_POST['expiration_url'] ?? null)), 0, 2048);
        $_POST['clicks_limit'] = empty($_POST['clicks_limit']) ? null : (int) $_POST['clicks_limit'];
        $this->check_location_url($_POST['expiration_url'], true);
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Middlewares\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Check for any errors */
        $required_fields = ['location_url'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_url($_POST['url']);

        $this->check_location_url($_POST['location_url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
            die();
        }

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['qweasdzxc']) ?
            ($_POST['qweasdzxc'] != $link->settings->password ? password_hash($_POST['qweasdzxc'], PASSWORD_DEFAULT) : $link->settings->password)
            : null;


        /* Check for duplicate url if needed */
        if($_POST['url'] && ($_POST['url'] != $link->url || $domain_id != $link->domain_id)) {

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('create_link_modal.error_message.url_exists'), 'error');
            }

        }

        $url = $_POST['url'];

        if(empty($_POST['url'])) {
            /* Generate random url if not specified */
            $url = string_generate(10);

            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = string_generate(10);
            }

        }

        /* Prepare the settings */
        $targeting_types = ['country_code', 'device_type', 'browser_language', 'rotation', 'os_name'];
        $_POST['targeting_type'] = in_array($_POST['targeting_type'], array_merge(['false'], $targeting_types)) ? Database::clean_string($_POST['targeting_type']) : 'false';

        $settings = [
            'clicks_limit' => $_POST['clicks_limit'],
            'expiration_url' => $_POST['expiration_url'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
            'targeting_type' => $_POST['targeting_type'],
        ];

        /* Process the targeting */
        foreach($targeting_types as $targeting_type) {
            ${'targeting_' . $targeting_type} = [];

            if(isset($_POST['targeting_' . $targeting_type . '_key'])) {
                foreach ($_POST['targeting_' . $targeting_type . '_key'] as $key => $value) {
                    if (empty(trim($value))) continue;

                    ${'targeting_' . $targeting_type}[] = [
                        'key' => trim(Database::clean_string($value)),
                        'value' => mb_substr(trim(Database::clean_string($_POST['targeting_' . $targeting_type . '_value'][$key])), 0, 2048),
                    ];
                }

                $settings['targeting_' . $targeting_type] = ${'targeting_' . $targeting_type};
            }
        }

        $settings = json_encode($settings);

        db()->where('link_id', $_POST['link_id'])->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'location_url' => $_POST['location_url'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'settings' => $settings,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['url' => $url]);
    }

    private function delete() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];

        /* Check for possible errors */
        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links', ['link_id'])) {
            die();
        }

        (new \Altum\Models\Link())->delete($link->link_id);

        Response::json('', 'success', ['url' => url('links')]);

    }

    public function duplicate() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];

        //ALTUMCODE.DEMO: if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('links');
        }

        /* Get the link data */
        $link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links');

        if(!$link) {
            redirect('links');
        }

        /* Make sure that the user didn't exceed the limit */
        if($link->type == 'link') {
            $user_total_links = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'link'")->fetch_object()->total;
            if($this->user->plan_settings->links_limit != -1 && $user_total_links >= $this->user->plan_settings->links_limit) {
                Alerts::add_error(l('global.info_message.plan_feature_limit'));
            }
        }

        elseif($link->type == 'biolink') {
            $user_total_biolinks = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'biolink'")->fetch_object()->total;
            if($this->user->plan_settings->biolinks_limit != -1 && $user_total_biolinks >= $this->user->plan_settings->biolinks_limit) {
                Alerts::add_error(l('global.info_message.plan_feature_limit'));
            }
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Duplicate the link */
            $link->settings = json_decode($link->settings);

            if ($link->type == 'biolink') {
                $link->settings->seo_image = null;
                $link->settings->favicon = null;
                if ($link->settings->background_type == 'image') $link->settings->background = null;
            }

            /* Generate random url if not specified */
            $url = string_generate(10);
            while (db()->where('url', $url)->where('domain_id', $link->domain_id)->getValue('links', 'link_id')) {
                $url = string_generate(10);
            }

            /* Database query */
            $link_id = db()->insert('links', [
                'user_id' => $this->user->user_id,
                'project_id' => $link->project_id,
                'biolink_theme_id' => $link->biolink_theme_id,
                'biolink_id' => $link->biolink_id,
                'domain_id' => $link->domain_id,
                'pixels_ids' => $link->pixels_ids,
                'type' => $link->type,
                'url' => $url,
                'location_url' => $link->location_url,
                'settings' => json_encode($link->settings),
                'start_date' => $link->start_date,
                'end_date' => $link->end_date,
                'is_verified' => $link->is_verified,
                'is_enabled' => $link->is_enabled,
                'datetime' => \Altum\Date::$date,
            ]);

            /* Duplicate the biolink blocks */
            if ($link->type == 'biolink') {

                /* Get all biolink blocks if needed */
                $biolink_blocks = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->get('biolinks_blocks');

                foreach ($biolink_blocks as $biolink_block) {
                    $biolink_block->settings = json_decode($biolink_block->settings);
                    $biolink_block->settings->image = null;
                    $biolink_block->settings->file = null;

                    /* Database query */
                    db()->insert('biolinks_blocks', [
                        'user_id' => $this->user->user_id,
                        'link_id' => $link_id,
                        'type' => $biolink_block->type,
                        'location_url' => $biolink_block->location_url,
                        'settings' => json_encode($biolink_block->settings),
                        'order' => $biolink_block->order,
                        'start_date' => $biolink_block->start_date,
                        'end_date' => $biolink_block->end_date,
                        'is_enabled' => $biolink_block->is_enabled,
                        'datetime' => \Altum\Date::$date,
                    ]);
                }
            }

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.create2'));

            /* Redirect */
            redirect('link/' . $link_id);
        }

        redirect('links');
    }


    /* Function to bundle together all the checks of a custom url */
    private function check_url($url) {

        if($url) {
            /* Make sure the url alias is not blocked by a route of the product */
            if(array_key_exists($url, Router::$routes[''])) {
                Response::json(l('link.error_message.blacklisted_url'), 'error');
            }

            /* Make sure the custom url is not blacklisted */
            if(in_array(mb_strtolower($url), explode(',', settings()->links->blacklisted_keywords))) {
                Response::json(l('link.error_message.blacklisted_keyword'), 'error');
            }

        }

    }

    /* Function to bundle together all the checks of an url */
    private function check_location_url($url, $can_be_empty = false) {

        if(empty(trim($url)) && $can_be_empty) {
            return;
        }

        if(empty(trim($url))) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        $url_details = parse_url($url);

        if(!isset($url_details['scheme'])) {
            Response::json(l('link.error_message.invalid_location_url'), 'error');
        }

        if(!$this->user->plan_settings->deep_links && !in_array($url_details['scheme'], ['http', 'https'])) {
            Response::json(l('link.error_message.invalid_location_url'), 'error');
        }

        /* Make sure the domain is not blacklisted */
        $domain = get_domain_from_url($url);

        if($domain && in_array($domain, explode(',', settings()->links->blacklisted_domains))) {
            Response::json(l('link.error_message.blacklisted_domain'), 'error');
        }

        /* Check the url with google safe browsing to make sure it is a safe website */
        if(settings()->links->google_safe_browsing_is_enabled) {
            if(google_safe_browsing_check($url, settings()->links->google_safe_browsing_api_key)) {
                Response::json(l('link.error_message.blacklisted_location_url'), 'error');
            }
        }
    }

    /* Check if custom domain is set and return the proper value */
    private function get_domain_id($posted_domain_id) {

        $domain_id = 0;

        if(isset($posted_domain_id)) {
            $domain_id = (int) Database::clean_string($posted_domain_id);

            /* Make sure the user has access to global additional domains */
            if($this->user->plan_settings->additional_global_domains) {
                $domain_id = database()->query("SELECT `domain_id` FROM `domains` WHERE `domain_id` = {$domain_id} AND (`user_id` = {$this->user->user_id} OR `type` = 1)")->fetch_object()->domain_id ?? 0;
            } else {
                $domain_id = database()->query("SELECT `domain_id` FROM `domains` WHERE `domain_id` = {$domain_id} AND `user_id` = {$this->user->user_id}")->fetch_object()->domain_id ?? 0;
            }

        }

        return $domain_id;
    }
}
