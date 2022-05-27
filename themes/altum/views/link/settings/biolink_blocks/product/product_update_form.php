<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="product" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'product_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'product_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_product_modal.product_header') ?>
    </button>

    <div class="collapse" id="<?= 'product_settings_container_' . $row->biolink_block_id ?>">
        <div class="form-group">
            <label for="<?= 'product_file_' . $row->biolink_block_id ?>"><?= l('create_biolink_product_modal.file') ?></label>
            <input id="<?= 'product_file_' . $row->biolink_block_id ?>" type="file" name="file" accept="<?= implode(', ', array_map(function($value) { return '.' . $value; }, $data->biolink_blocks['product']['whitelisted_file_extensions'])) ?>" class="form-control-file" />
            <?php if($row->settings->file): ?>
                <small class="form-text text-muted"><?= $row->settings->file ?></small>
            <?php endif ?>
        </div>

        <div class="form-group">
            <label for="<?= 'product_title_' . $row->biolink_block_id ?>"><?= l('create_biolink_product_modal.title') ?></label>
            <input type="text" id="<?= 'product_title_' . $row->biolink_block_id ?>" name="title" class="form-control" value="<?= $row->settings->title ?? null ?>" maxlength="<?= $data->biolink_blocks['product']['fields']['title']['max_length'] ?>" />
        </div>

        <div class="form-group">
            <label for="<?= 'product_description_' . $row->biolink_block_id ?>"><?= l('create_biolink_product_modal.description') ?></label>
            <input type="text" id="<?= 'product_description_' . $row->biolink_block_id ?>" name="description" class="form-control" value="<?= $row->settings->description ?? null ?>" maxlength="<?= $data->biolink_blocks['product']['fields']['description']['max_length'] ?>" />
        </div>

        <div class="form-group">
            <label for="<?= 'product_price_' . $row->biolink_block_id ?>"><?= l('create_biolink_product_modal.price') ?></label>
            <input type="number" min="0" step="0.01" id="<?= 'product_price_' . $row->biolink_block_id ?>" name="price" class="form-control" value="<?= $row->settings->price ?? null ?>" />
        </div>

        <div class="form-group">
            <label for="<?= 'product_minimum_price_' . $row->biolink_block_id ?>"><?= l('create_biolink_product_modal.minimum_price') ?></label>
            <input type="number" min="0" step="0.01" id="<?= 'product_minimum_price_' . $row->biolink_block_id ?>" name="minimum_price" class="form-control" value="<?= $row->settings->minimum_price ?? null ?>" />
        </div>

        <div class="form-group">
            <label for="<?= 'product_currency_' . $row->biolink_block_id ?>"><?= l('create_biolink_product_modal.currency') ?></label>
            <input type="text" id="<?= 'product_currency_' . $row->biolink_block_id ?>" name="currency" class="form-control" value="<?= $row->settings->currency ?? null ?>" maxlength="<?= $data->biolink_blocks['product']['fields']['currency']['max_length'] ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_product_modal.currency_help') ?></small>
        </div>

        <div class="custom-control custom-switch my-3">
            <input id="<?= 'product_allow_custom_price_' . $row->biolink_block_id ?>" name="allow_custom_price" type="checkbox" class="custom-control-input" <?= ($row->settings->allow_custom_price ?? null) ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="<?= 'product_allow_custom_price_' . $row->biolink_block_id ?>"><?= l('create_biolink_product_modal.allow_custom_price') ?></label>
        </div>

        <div class="form-group">
            <label for="<?= 'product_thank_you_title_' . $row->biolink_block_id ?>"><?= l('create_biolink_product_modal.thank_you_title') ?></label>
            <input type="text" id="<?= 'product_thank_you_title_' . $row->biolink_block_id ?>" name="thank_you_title" class="form-control" value="<?= $row->settings->thank_you_title ?? null ?>" maxlength="<?= $data->biolink_blocks['product']['fields']['thank_you_title']['max_length'] ?>" />
        </div>

        <div class="form-group">
            <label for="<?= 'product_thank_you_description_' . $row->biolink_block_id ?>"><?= l('create_biolink_product_modal.thank_you_description') ?></label>
            <input type="text" id="<?= 'product_thank_you_description_' . $row->biolink_block_id ?>" name="thank_you_description" class="form-control" value="<?= $row->settings->thank_you_description ?? null ?>" maxlength="<?= $data->biolink_blocks['product']['fields']['thank_you_description']['max_length'] ?>" />
        </div>

        <div class="mb-3">
            <div class="d-flex flex-column flex-xl-row justify-content-between">
                <label for="<?= 'product_payment_processors_ids_' . $row->biolink_block_id ?>"><?= l('payment_processors.payment_processors_ids') ?></label>
                <a href="<?= url('payment-processor-create') ?>" target="_blank" class="small mb-2"><i class="fa fa-fw fa-sm fa-plus mr-1"></i> <?= l('payment_processors.create') ?></a>
            </div>

            <?php foreach($data->payment_processors as $payment_processor): ?>
                <div class="custom-control custom-checkbox my-2">
                    <input id="<?= 'product_payment_processors_ids' . $payment_processor->payment_processor_id . '_' . $row->biolink_block_id ?>" name="payment_processors_ids[]" value="<?= $payment_processor->payment_processor_id ?>" type="checkbox" class="custom-control-input" <?= in_array($payment_processor->payment_processor_id, $row->settings->payment_processors_ids ?? []) ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="<?= 'product_payment_processors_ids' . $payment_processor->payment_processor_id . '_' . $row->biolink_block_id ?>">
                        <span class="mr-1"><?= $payment_processor->name ?></span>
                        <small class="badge badge-light badge-pill"><?= l('pay.custom_plan.' . $payment_processor->processor) ?></small>
                    </label>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'product_data_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'product_data_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_block_modal.data_header') ?>
    </button>

    <div class="collapse" id="<?= 'product_data_container_' . $row->biolink_block_id ?>">
        <div class="alert alert-info">
            <i class="fa fa-fw fa-sm fa-info-circle mr-1"></i> <?= l('create_biolink_block_modal.data_help') ?>
        </div>

        <div class="form-group">
            <label for="<?= 'product_email_notification_' . $row->biolink_block_id ?>"><?= l('create_biolink_block_modal.email_notification') ?></label>
            <input type="text" id="<?= 'product_email_notification_' . $row->biolink_block_id ?>" name="email_notification" class="form-control" value="<?= $row->settings->email_notification ?? null ?>" maxlength="<?= $data->biolink_blocks['service']['fields']['email_notification']['max_length'] ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_block_modal.email_notification_help') ?></small>
        </div>

        <div class="form-group">
            <label for="<?= 'product_webhook_url_' . $row->biolink_block_id ?>"><?= l('create_biolink_block_modal.webhook_url') ?></label>
            <input type="url" id="<?= 'product_webhook_url_' . $row->biolink_block_id ?>" name="webhook_url" class="form-control" value="<?= $row->settings->webhook_url ?? null ?>" maxlength="<?= $data->biolink_blocks['service']['fields']['webhook_url']['max_length'] ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_block_modal.webhook_url_help') ?></small>
        </div>
    </div>

    <button class="btn btn-block btn-gray-300 my-4" type="button" data-toggle="collapse" data-target="#<?= 'button_settings_container_' . $row->biolink_block_id ?>" aria-expanded="false" aria-controls="<?= 'button_settings_container_' . $row->biolink_block_id ?>">
        <?= l('create_biolink_link_modal.button_header') ?>
    </button>

    <div class="collapse" id="<?= 'button_settings_container_' . $row->biolink_block_id ?>">
        <div class="form-group">
            <label for="<?= 'product_name_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.name') ?></label>
            <input id="<?= 'product_name_' . $row->biolink_block_id ?>" type="text" name="name" class="form-control" value="<?= $row->settings->name ?>" maxlength="128" required="required" />
        </div>

        <div class="form-group">
            <label for="<?= 'product_image_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-image fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.image') ?></label>
            <div data-image-container class="<?= !empty($row->settings->image) ? null : 'd-none' ?>">
                <div class="row">
                    <div class="m-1 col-6 col-xl-3">
                        <img src="<?= $row->settings->image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $row->settings->image : null ?>" class="img-fluid rounded <?= !empty($row->settings->image) ? null : 'd-none' ?>" loading="lazy" />
                    </div>
                </div>
                <div class="custom-control custom-checkbox my-2">
                    <input id="<?= $row->biolink_block_id . '_image_remove' ?>" name="image_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#<?= 'product_image_' . $row->biolink_block_id ?>').classList.add('d-none') : document.querySelector('#<?= 'product_image_' . $row->biolink_block_id ?>').classList.remove('d-none')">
                    <label class="custom-control-label" for="<?= $row->biolink_block_id . '_image_remove' ?>">
                        <span class="text-muted"><?= l('global.delete_file') ?></span>
                    </label>
                </div>
            </div>
            <input id="<?= 'product_image_' . $row->biolink_block_id ?>" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
        </div>

        <div class="form-group">
            <label for="<?= 'product_icon_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-globe fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.icon') ?></label>
            <input id="<?= 'product_icon_' . $row->biolink_block_id ?>" type="text" name="icon" class="form-control" value="<?= $row->settings->icon ?>" placeholder="<?= l('create_biolink_link_modal.input.icon_placeholder') ?>" />
            <small class="form-text text-muted"><?= l('create_biolink_link_modal.input.icon_help') ?></small>
        </div>

        <div <?= $this->user->plan_settings->custom_colored_links ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
            <div class="<?= $this->user->plan_settings->custom_colored_links ? null : 'container-disabled' ?>">
                <div class="form-group">
                    <label for="<?= 'product_text_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.text_color') ?></label>
                    <input id="<?= 'product_text_color_' . $row->biolink_block_id ?>" type="hidden" name="text_color" class="form-control" value="<?= $row->settings->text_color ?>" required="required" />
                    <div class="text_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="<?= 'product_background_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.background_color') ?></label>
                    <input id="<?= 'product_background_color_' . $row->biolink_block_id ?>" type="hidden" name="background_color" class="form-control" value="<?= $row->settings->background_color ?>" required="required" />
                    <div class="background_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="<?= 'product_border_width_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-style fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_width') ?></label>
                    <input id="<?= 'product_border_width_' . $row->biolink_block_id ?>" type="range" min="0" max="5" class="form-control" name="border_width" value="<?= $row->settings->border_width ?>" required="required" />
                </div>

                <div class="form-group">
                    <label for="<?= 'product_border_color_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_color') ?></label>
                    <input id="<?= 'product_border_color_' . $row->biolink_block_id ?>" type="hidden" name="border_color" class="form-control" value="<?= $row->settings->border_color ?>" required="required" />
                    <div class="border_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="<?= 'product_border_radius_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-all fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_radius') ?></label>
                    <select id="<?= 'product_border_radius_' . $row->biolink_block_id ?>" name="border_radius" class="form-control">
                        <option value="straight" <?= $row->settings->border_radius == 'straight' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_straight') ?></option>
                        <option value="round" <?= $row->settings->border_radius == 'round' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_round') ?></option>
                        <option value="rounded" <?= $row->settings->border_radius == 'rounded' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_radius_rounded') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'product_border_style_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-border-none fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.border_style') ?></label>
                    <select id="<?= 'product_border_style_' . $row->biolink_block_id ?>" name="border_style" class="form-control">
                        <option value="solid" <?= $row->settings->border_style == 'solid' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_solid') ?></option>
                        <option value="dashed" <?= $row->settings->border_style == 'dashed' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_dashed') ?></option>
                        <option value="double" <?= $row->settings->border_style == 'double' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_double') ?></option>
                        <option value="outset" <?= $row->settings->border_style == 'outset' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_outset') ?></option>
                        <option value="inset" <?= $row->settings->border_style == 'inset' ? 'selected="selected"' : null ?>><?= l('create_biolink_link_modal.input.border_style_inset') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'product_animation_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-film fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.animation') ?></label>
                    <select id="<?= 'product_animation_' . $row->biolink_block_id ?>" name="animation" class="form-control">
                        <option value="false" <?= !$row->settings->animation ? 'selected="selected"' : null ?>>-</option>
                        <?php foreach(require APP_PATH . 'includes/biolink_animations.php' as $animation): ?>
                            <option value="<?= $animation ?>" <?= $row->settings->animation == $animation ? 'selected="selected"' : null ?>><?= $animation ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="<?= 'product_animation_runs_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-play-circle fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.animation_runs') ?></label>
                    <select id="<?= 'product_animation_runs_' . $row->biolink_block_id ?>" name="animation_runs" class="form-control">
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
