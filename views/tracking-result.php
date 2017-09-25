<?php
/**
 * Created by PhpStorm.
 * User: joakimcarlsten
 * Date: 2017-09-25
 * Time: 17:20
 */

/**
 * @var string $trackingNumber
 * @var \Vinnia\Shipping\Tracking $result
 */

//$trackingResult = $data['Tracks'][0];
$header = sprintf(__('Results from tracking of %s', 'ysds'), $trackingNumber);
$activities = $result->activities;
?>

<div class="page-header">
    <h3 id="timeline"><?= $header ?></h3>
</div>

<ul class="timeline">
    <?php
    $counter = 0;
    /** @var array $activities */
    foreach ($activities as $activity) :
        /**
         * @var \Vinnia\Shipping\TrackingActivity $activity
         */
        $invertedClass = '';
        if (1 === $counter % 2) {
            $invertedClass = 'class="timeline-inverted"';
        }
        $time = $activity->date;
        ?>
        <li <?= $invertedClass; ?> >
            <div class="timeline-badge <?= Vinnia_Tracker::STATUS_CODES[$activity->status]['class']; ?>"><i
                    class="fa <?= Vinnia_Tracker::STATUS_CODES[$activity->status]['icon']; ?>"></i></div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h4 class="timeline-title"><?= $activity->description ?></h4>
                    <?php if (false !== $time) : ?>
                        <p>
                            <small class="text-muted"><i
                                    class="glyphicon glyphicon-time"></i> <?= $time->format('Y-m-d H:i'); ?></small>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="timeline-body">
                    <address>
                        <span class="timeline__city"><?= $activity->address->city ?></span><br>
                        <span class="timeline__country"><?= $activity->address->countryCode ?></span><br>
                    </address>
                </div>
            </div>
        </li>
        <?php
        $counter++;
    endforeach;
    ?>
</ul>