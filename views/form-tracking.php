<?php
/**
 * Created by PhpStorm.
 * User: joakimcarlsten
 * Date: 2017-09-25
 * Time: 15:14
 */
?>

<form class="">
    <div class="form-group">
        <div class="input-group">
            <label class="sr-only" for="trackingNumber"><?= __('Tracking number', 'ysds'); ?></label>
            <input type="text"
                   class="form-control"
                   id="trackingNumber"
                   placeholder="<?= __('Tracking number/AWB', 'ysds'); ?>"/>
            <span class="input-group-btn">
        <button class="btn btn-primary js-track-shipment"><?= __('Track', 'ysds'); ?></button>
            </span>
        </div>
    </div>
</form>
<div id="trackingResult"></div>