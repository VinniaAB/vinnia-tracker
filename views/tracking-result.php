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

$header = sprintf(__('Results from tracking of %s', 'vinnia-tracker'), $trackingNumber);
$trackings = $result->tracking ?? [];

?>

<?php if (empty($trackings)) : ?>
    <div class="page-header">
        <h3><?= sprintf(__('No shipments found with tracking number %s', 'vinnia-tracker'), $trackingNumber); ?></h3>
    </div>
<?php else: ?>
    <?php
    /** TODO: Let's only take the first for now - ugly
    Should instead inform the viewer about these multiple cases and ask him to choose
     */
    $activities = $trackings->activities ?? null;

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
            /** @var DateTimeImmutable $time */
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
                                            class="glyphicon glyphicon-time"></i> <?= $time->format('Y-m-d H:i'); ?>
                                </small>
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
<?php endif;