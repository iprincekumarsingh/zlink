<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="phone_collector" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'phone_collector_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'phone_collector_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_phone_collector_modal.phone_collector_header') ?>
    </button>

    <div class="collapse" id="<?= 'phone_collector_settings_container_' . $row->biolink_block_id ?>">
        <div class="form-group">
            <label for="<?= 'phone_collector_phone_placeholder_' . $row->biolink_block_id ?>"><?= l('create_biolink_phone_collector_modal.phone_placeholder') ?></label>
            <input id="<?= 'phone_collector_phone_placeholder_' . $row->biolink_block_id ?>" type="text" name="phone_placeholder" class="form-control" value="<?= $row->settings->phone_placeholder ?>" maxlength="64" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_name_placeholder_' . $row->biolink_block_id ?>"><?= l('create_biolink_phone_collector_modal.name_placeholder') ?></label>
            <input id="<?= 'phone_collector_name_placeholder_' . $row->biolink_block_id ?>" type="text" name="name_placeholder" class="form-control" value="<?= $row->settings->name_placeholder ?>" maxlength="64" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_button_text_' . $row->biolink_block_id ?>"><?= l('create_biolink_phone_collector_modal.button_text') ?></label>
            <input id="<?= 'phone_collector_button_text_' . $row->biolink_block_id ?>" type="text" name="button_text" class="form-control" value="<?= $row->settings->button_text ?>" maxlength="64" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_success_text_' . $row->biolink_block_id ?>"><?= l('create_biolink_phone_collector_modal.success_text') ?></label>
            <input id="<?= 'phone_collector_success_text_' . $row->biolink_block_id ?>" type="text" name="success_text" class="form-control" value="<?= $row->settings->success_text ?>" maxlength="256" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_thank_you_url_' . $row->biolink_block_id ?>"><?= l('create_biolink_phone_collector_modal.thank_you_url') ?></label>
            <input id="<?= 'phone_collector_thank_you_url_' . $row->biolink_block_id ?>" type="url" name="thank_you_url" class="form-control" value="<?= $row->settings->thank_you_url ?>" maxlength="2048" />
        </div>

        <div class="custom-control custom-switch mr-3 mb-3">
            <input
                    type="checkbox"
                    class="custom-control-input"
                    id="<?= 'phone_collector_show_agreement_' . $row->biolink_block_id ?>"
                    name="show_agreement"
                <?= $row->settings->show_agreement ? 'checked="checked"' : null ?>
            >
            <label class="custom-control-label clickable" for="<?= 'phone_collector_show_agreement_' . $row->biolink_block_id ?>"><?= l('create_biolink_phone_collector_modal.show_agreement') ?></label>
            <div><small class="form-text text-muted"><?= l('create_biolink_phone_collector_modal.show_agreement_help') ?></small></div>
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_agreement_text_' . $row->biolink_block_id ?>"><?= l('create_biolink_phone_collector_modal.agreement_text') ?></label>
            <input id="<?= 'phone_collector_agreement_text_' . $row->biolink_block_id ?>" type="text" name="agreement_text" class="form-control" value="<?= $row->settings->agreement_text ?>" maxlength="256" />
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_agreement_url_' . $row->biolink_block_id ?>"><?= l('create_biolink_phone_collector_modal.agreement_url') ?></label>
            <input id="<?= 'phone_collector_agreement_url_' . $row->biolink_block_id ?>" type="text" name="agreement_url" class="form-control" value="<?= $row->settings->agreement_url ?>" maxlength="2048" />
        </div>
    </div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'phone_collector_data_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'phone_collector_data_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_block_modal.data_header') ?>
    </button>

    <div class="collapse" id="<?= 'phone_collector_data_container_' . $row->biolink_block_id ?>">
        <div class="alert alert-info">
            <i class="fa fa-fw fa-sm fa-info-circle mr-1"></i> <?= l('create_biolink_block_modal.data_help') ?>
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_email_notification_' . $row->biolink_block_id ?>"><?= l('create_biolink_block_modal.email_notification') ?></label>
            <input type="text" id="<?= 'phone_collector_email_notification_' . $row->biolink_block_id ?>" name="email_notification" class="form-control" value="<?= $row->settings->email_notification ?? null ?>" maxlength="<?= $data->biolink_blocks['service']['fields']['email_notification']['max_length'] ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_block_modal.email_notification_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_webhook_url_' . $row->biolink_block_id ?>"><?= l('create_biolink_block_modal.webhook_url') ?></label>
            <input id="<?= 'phone_collector_webhook_url_' . $row->biolink_block_id ?>" type="text" name="webhook_url" class="form-control" value="<?= $row->settings->webhook_url ?>" maxlength="2048" />
            <small class="form-text text-muted"><?= l('create_biolink_block_modal.webhook_url_help') ?></small>
        </div>
    </div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'button_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'button_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_link_modal.button_header') ?>
    </button>

    <div class="collapse" id="<?= 'button_settings_container_' . $row->biolink_block_id ?>">
        <div class="form-group">
            <label for="<?= 'phone_collector_name_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.name') ?></label>
            <input id="<?= 'phone_collector_name_' . $row->biolink_block_id ?>" type="text" name="name" class="form-control" value="<?= $row->settings->name ?>" maxlength="128" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_image_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-image fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.image') ?></label>
            <div data-image-container class="<?= !empty($row->settings->image) ? null : 'd-none' ?>">
                <div class="row">
                    <div class="m-1 col-6 col-xl-3">
                        <img src="<?= $row->settings->image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $row->settings->image : null ?>" class="img-fluid rounded <?= !empty($row->settings->image) ? null : 'd-none' ?>" loading="lazy" />
                    </div>
                </div>
                <div class="custom-control custom-checkbox my-2">
                    <input id="<?= $row->biolink_block_id . '_image_remove' ?>" name="image_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#<?= 'phone_collector_image_' . $row->biolink_block_id ?>').classList.add('d-none') : document.querySelector('#<?= 'phone_collector_image_' . $row->biolink_block_id ?>').classList.remove('d-none')">
                    <label class="custom-control-label" for="<?= $row->biolink_block_id . '_image_remove' ?>">
                        <span class="text-muted"><?= l('global.delete_file') ?></span>
                    </label>
                </div>
            </div>
            <input id="<?= 'phone_collector_image_' . $row->biolink_block_id ?>" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
        </div>

        <div class="form-group">
            <label for="<?= 'phone_collector_icon_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-globe fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.icon') ?></label>
            <input id="<?= 'phone_collector_icon_' . $row->biolink_block_id ?>" type="text" name="icon" class="form-control" value="<?= $row->settings->icon ?>" placeholder="<?= l('create_biolink_link_modal.input.icon_placeholder') ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.icon_help') ?></small>
        </div>

        <div <?= $this->user->plan_settings->custom_colored_links ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
            <div class="<?= $this->user->plan_settings->custom_colored_links ? null : 'container-disabled' ?>">
                <div class="form-group">
                    <label for="<?= 'phone_collector_text_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.text_color') ?></label>
                    <input id="<?= 'phone_collector_text_color_' . $row->biolink_block_id ?>" type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                    <div class="text_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="<?= 'phone_collector_background_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.background_color') ?></label>
                    <input id="<?= 'phone_collector_background_color_' . $row->biolink_block_id ?>" type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
                    <div class="background_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="<?= 'phone_collector_border_width_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-style fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_width') ?></label>
                    <input id="<?= 'phone_collector_border_width_' . $row->biolink_block_id ?>" type="range" min="0" max="5" class="form-control" name="border_width" value="<?= $row->settings->border_width ?>" required="required" />
                </div>

                <div class="form-group">
                    <label for="<?= 'phone_collector_border_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_color') ?></label>
                    <input id="<?= 'phone_collector_border_color_' . $row->biolink_block_id ?>" type="hidden" name="border_color" class="form-control" value="<?= $row->settings->border_color ?>" required="required" />
                    <div class="border_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="<?= 'phone_collector_border_radius_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-all fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_radius') ?></label>
                    <select id="<?= 'phone_collector_border_radius_' . $row->biolink_block_id ?>" name="border_radius" class="form-control">
                        <option value="straight" <?= $row->settings->border_radius == 'straight' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_straight') ?></option>
                        <option value="round" <?= $row->settings->border_radius == 'round' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_round') ?></option>
                        <option value="rounded" <?= $row->settings->border_radius == 'rounded' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_rounded') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'phone_collector_border_style_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-none fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_style') ?></label>
                    <select id="<?= 'phone_collector_border_style_' . $row->biolink_block_id ?>" name="border_style" class="form-control">
                        <option value="solid" <?= $row->settings->border_style == 'solid' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_solid') ?></option>
                        <option value="dashed" <?= $row->settings->border_style == 'dashed' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_dashed') ?></option>
                        <option value="double" <?= $row->settings->border_style == 'double' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_double') ?></option>
                        <option value="outset" <?= $row->settings->border_style == 'outset' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_outset') ?></option>
                        <option value="inset" <?= $row->settings->border_style == 'inset' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_inset') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'phone_collector_animation_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-film fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.animation') ?></label>
                    <select id="<?= 'phone_collector_animation_' . $row->biolink_block_id ?>" name="animation" class="form-control">
                        <option value="false" <?= !$row->settings->animation ? 'selected="selected"' : null ?>>-</option>
                        <?php foreach(require APP_PATH . 'includes/biolink_animations.php' as $animation): ?>
                            <option value="<?= $animation ?>" <?= $row->settings->animation == $animation ? 'selected="selected"' : null ?>><?= $animation ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'phone_collector_animation_runs_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-play-circle fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.animation_runs') ?></label>
                    <select id="<?= 'phone_collector_animation_runs_' . $row->biolink_block_id ?>" name="animation_runs" class="form-control">
                        <option value="repeat-1" <?= $row->settings->animation_runs == 'repeat-1' ? 'selected="selected"' : null ?>>1</option>
                        <option value="repeat-2" <?= $row->settings->animation_runs == 'repeat-2' ? 'selected="selected"' : null ?>>2</option>
                        <option value="repeat-3" <?= $row->settings->animation_runs == 'repeat-3' ? 'selected="selected"' : null ?>>3</option>
                        <option value="infinite" <?= $row->settings->animation_runs == 'repeat-infinite' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.animation_runs_infinite') ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
    </div>
</form>
