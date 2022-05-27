<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="create_link" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_link_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_link" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="link" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label for="location_url"><i class="fa fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('create_link_modal.input.location_url') ?></label>
                        <input id="location_url" type="text" class="form-control" name="location_url" maxlength="2048" required="required" placeholder="<?= l('create_link_modal.input.location_url_placeholder') ?>" />
                    </div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-anchor fa-sm text-muted mr-1"></i> <?= l('create_link_modal.input.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Middlewares\Authentication::is_admin()): ?>
                                            <option value=""><?= SITE_URL ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                        <option value="<?= $row->domain_id ?>"><?= $row->url ?></option>
                                        <?php endforeach ?>
                                    </select>
                                <?php else: ?>
                                    <span class="input-group-text"><?= SITE_URL ?></span>
                                <?php endif ?>
                            </div>
                            <input
                                type="text"
                                class="form-control"
                                name="url"
                                maxlength="256"
                                onchange="update_this_value(this, get_slug)"
                                onkeyup="update_this_value(this, get_slug)"
                                placeholder="<?= $this->user->plan_settings->custom_url ? l('create_link_modal.input.url_placeholder_custom') : l('create_link_modal.input.url_placeholder') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('create_link_modal.input.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('create_link_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="create_biolink" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= l('create_biolink_modal.header') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="create_biolink" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="create" />
                    <input type="hidden" name="type" value="biolink" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-anchor fa-sm text-muted mr-1"></i> <?= l('create_biolink_modal.input.url') ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <?php if(count($data->domains)): ?>
                                    <select name="domain_id" class="appearance-none select-custom-altum form-control input-group-text">
                                        <?php if(settings()->links->main_domain_is_enabled || \Altum\Middlewares\Authentication::is_admin()): ?>
                                            <option value=""><?= SITE_URL ?></option>
                                        <?php endif ?>

                                        <?php foreach($data->domains as $row): ?>
                                            <option value="<?= $row->domain_id ?>"><?= $row->url ?></option>
                                        <?php endforeach ?>
                                    </select>
                                <?php else: ?>
                                    <span class="input-group-text"><?= SITE_URL ?></span>
                                <?php endif ?>
                            </div>
                            <input
                                type="text"
                                class="form-control"
                                name="url"
                                maxlength="256"
                                onchange="update_this_value(this, get_slug)"
                                onkeyup="update_this_value(this, get_slug)"
                                placeholder="<?= $this->user->plan_settings->custom_url ? l('create_biolink_modal.input.url_placeholder_custom') :  l('create_biolink_modal.input.url_placeholder') ?>"
                                <?= !$this->user->plan_settings->custom_url ? 'readonly="readonly"' : null ?>
                                <?= $this->user->plan_settings->custom_url ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>
                            />
                        </div>
                        <small class="form-text text-muted"><?= l('create_biolink_modal.input.url_help') ?></small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('create_biolink_modal.input.submit') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    $('form[name="create_link"],form[name="create_biolink"]').on('submit', event => {

        $.ajax({
            type: 'POST',
            url: `${url}link-ajax`,
            data: $(event.currentTarget).serialize(),
            success: (data) => {
                let notification_container = event.currentTarget.querySelector('.notification-container');
                notification_container.innerHTML = '';

                if(data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    /* Fade out refresh */
                    fade_out_redirect({ url: data.details.url, full: true });

                }
            },
            dataType: 'json'
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
