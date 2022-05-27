<?php defined('ALTUMCODE') || die() ?>

<form name="update_biolink_" method="post" role="form" enctype="multipart/form-data">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
    <input type="hidden" name="request_type" value="update" />
    <input type="hidden" name="block_type" value="video" />
    <input type="hidden" name="biolink_block_id" value="<?= $row->biolink_block_id ?>" />

    <div class="notification-container"></div>

    <div class="form-group">
        <label for="<?= 'video_file_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-video fa-sm text-muted mr-1"></i> <?= l('create_biolink_video_modal.file') ?></label>
        <input id="<?= 'video_file_' . $row->biolink_block_id ?>" type="file" name="file" accept="<?= implode(', ', array_map(function($value) { return '.' . $value; }, $data->biolink_blocks['video']['whitelisted_file_extensions'])) ?>" class="form-control-file" />
        <small class="form-text text-muted"><?= l('create_biolink_video_modal.file_help') ?></small>
    </div>

    <div class="form-group">
        <label for="<?= 'video_poster_url_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-image fa-sm text-muted mr-1"></i> <?= l('create_biolink_video_modal.poster_url') ?></label>
        <input id="<?= 'video_poster_url_' . $row->biolink_block_id ?>" type="url" name="poster_url" maxlength="2048" class="form-control" value="<?= $row->settings->poster_url ?>" />
    </div>

    <div class="form-group">
        <label for="<?= 'video_name_' . $row->biolink_block_id ?>"><i class="fa fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('create_biolink_link_modal.input.name') ?></label>
        <input id="<?= 'video_name_' . $row->biolink_block_id ?>" type="text" name="name" class="form-control" value="<?= $row->settings->name ?>" maxlength="128" required="required" />
    </div>

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
    </div>
</form>
