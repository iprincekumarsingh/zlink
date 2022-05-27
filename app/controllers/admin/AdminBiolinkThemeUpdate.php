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
use Altum\Middlewares\Csrf;
use Altum\Uploads;

class AdminBiolinkThemeUpdate extends Controller {

    public function index() {

        $biolink_theme_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$biolink_theme = db()->where('biolink_theme_id', $biolink_theme_id)->getOne('biolinks_themes')) {
            redirect('admin/biolinks-themes');
        }
        $biolink_theme->settings = json_decode($biolink_theme->settings);

        $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php';
        $biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';
        unset($biolink_backgrounds['image']);

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name'] = trim(Database::clean_string($_POST['name']));
            $_POST['is_enabled'] = (int) (bool) $_POST['is_enabled'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Check for errors & process  potential uploads */
            $image = !empty($_FILES['image']['name']) && !isset($_POST['image_remove']);

            if($image) {
                $file_name = $_FILES['image']['name'];
                $file_extension = explode('.', $file_name);
                $file_extension = mb_strtolower(end($file_extension));
                $file_temp = $_FILES['image']['tmp_name'];

                if($_FILES['image']['error'] == UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
                }

                if($_FILES['image']['error'] && $_FILES['image']['error'] != UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(l('global.error_message.file_upload'));
                }

                if(!in_array($file_extension, Uploads::get_whitelisted_file_extensions('biolinks_themes'))) {
                    Alerts::add_error(l('global.error_message.invalid_file_type'));
                }

                if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                    if(!is_writable(UPLOADS_PATH . 'biolinks_themes' . '/')) {
                        Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'biolinks_themes' . '/'));
                    }
                }

                if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                    /* Generate new name for image */
                    $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Delete current image */
                            $s3->deleteObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/' . 'biolinks_themes' . '/' . $biolink_theme->image,
                            ]);

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/' . 'biolinks_themes' . '/' . $image_new_name,
                                'ContentType' => mime_content_type($file_temp),
                                'SourceFile' => $file_temp,
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Alerts::add_error($exception->getMessage());
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Delete current image */
                        if(!empty($biolink_theme->image) && file_exists(UPLOADS_PATH . 'biolinks_themes' . '/' . $biolink_theme->image)) {
                            unlink(UPLOADS_PATH . 'biolinks_themes' . '/' . $biolink_theme->image);
                        }

                        /* Upload the original */
                        move_uploaded_file($file_temp, UPLOADS_PATH . 'biolinks_themes' . '/' . $image_new_name);
                    }

                    $biolink_theme->image = $image_new_name;
                }
            }

            /* Check for the removal of the already uploaded file */
            if(isset($_POST['image' . '_remove'])) {
                /* Offload deleting */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/' . 'biolinks_themes' . '/' . $biolink_theme->image,
                    ]);
                }

                /* Local deleting */
                else {
                    /* Delete current file */
                    if(!empty($biolink_theme->image) && file_exists(UPLOADS_PATH . 'biolinks_themes' . '/' . $biolink_theme->image)) {
                        unlink(UPLOADS_PATH . 'biolinks_themes' . '/' . $biolink_theme->image);
                    }
                }

                /* Database query */
                $biolink_theme->image = null;
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $settings = json_encode([
                    'biolink' => [
                        'background_type' => $_POST['biolink_background_type'],
                        'background' => $_POST['biolink_background'],
                        'background_color_one' => $_POST['biolink_background_color_one'],
                        'background_color_two' => $_POST['biolink_background_color_two'],
                        'font' => $_POST['biolink_font'],
                        'font_size' => $_POST['biolink_font_size'],
                    ],

                    'biolink_block' => [
                        'text_color' => $_POST['biolink_block_text_color'],
                        'background_color' => $_POST['biolink_block_background_color'],
                        'border_width' => $_POST['biolink_block_border_width'],
                        'border_color' => $_POST['biolink_block_border_color'],
                        'border_radius' => $_POST['biolink_block_border_radius'],
                        'border_style' => $_POST['biolink_block_border_style'],
                    ]
                ]);

                /* Database query */
                db()->where('biolink_theme_id', $biolink_theme_id)->update('biolinks_themes', [
                    'name' => $_POST['name'],
                    'image' => $biolink_theme->image,
                    'settings' => $settings,
                    'is_enabled' => $_POST['is_enabled'],
                    'last_datetime' => \Altum\Date::$date,
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . filter_var($_POST['name'], FILTER_SANITIZE_STRING) . '</strong>'));

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItem('biolinks_themes');

                /* Refresh the page */
                redirect('admin/biolink-theme-update/' . $biolink_theme_id);

            }

        }

        /* Main View */
        $data = [
            'biolink_theme_id' => $biolink_theme_id,
            'biolink_theme' => $biolink_theme,
            'biolink_backgrounds' => $biolink_backgrounds,
            'biolink_fonts' => $biolink_fonts,
        ];

        $view = new \Altum\Views\View('admin/biolink-theme-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
