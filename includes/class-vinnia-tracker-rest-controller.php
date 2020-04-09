<?php

if (!defined('ABSPATH')) exit;

class Vinnia_Tracker_Rest_Controller
{
    /**
     * @var \Vinnia\Shipping\ServiceInterface
     */
    protected $services;

    // Here initialize our namespace and resource name.
    public function __construct( array $services ) {
        $this->namespace     = 'vinnia/v1';
        $this->resource_name = 'track';
        $this->services = $services;
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->resource_name, array(
            array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_tracking_information' ),
            )
        ));
    }

    public function get_tracking_information( WP_REST_Request $request ) {

        $tracking_number = $request->get_query_params()['tracking_number'] ?? '';

        if ( ! $tracking_number ) {
            return new WP_Error(400, __('Tracking number is required!', 'vinnia-tracker'));
        }

        $tracker = new \Vinnia\Shipping\CompositeTracker($this->services);

        $promise = $tracker->getTrackingStatus($tracking_number)
            ->then(function($result) use ($tracking_number) {
                return [
                    'success' => true,
                    'data' => $result,
                    'trackingNo' => $tracking_number
                ];
            }, function($result) {
                return [
                    'success' => false,
                    'data' => $result,
                    'trackingNo' => 'none'
                ];
            });

        $result = $promise->wait();

        return new WP_REST_Response($result);
    }
}
