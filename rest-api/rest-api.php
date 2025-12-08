<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Disciple_Tools_People_Groups_API_Endpoints
{
    /**
     * @todo Set the permissions your endpoint needs
     * @link https://github.com/DiscipleTools/Documentation/blob/master/theme-core/capabilities.md
     * @var string[]
     */
    public $permissions = [ 'access_contacts', 'dt_all_access_contacts', 'view_project_metrics' ];


    /**
     * @todo define the name of the $namespace
     * @todo define the name of the rest route
     * @todo defne method (CREATABLE, READABLE)
     * @todo apply permission strategy. '__return_true' essentially skips the permission check.
     */
    //See https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
    public function add_api_routes() {
        $namespace = 'dt-public/disciple-tools-people-groups-api/v1';

        register_rest_route(
            $namespace, '/list', [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_people_groups' ],
                'permission_callback' => function( WP_REST_Request $request ) {
                    return true;
                },
            ]
        );
    }


    public function get_people_groups( WP_REST_Request $request ) {
        $fields_to_return = [
            'doxa_wagf_region',
            'doxa_wagf_block',
            'doxa_wagf_member',
            'name',
            'imb_display_name',
            'imb_location_description',
            'imb_population',
            'imb_reg_of_religion',
            'imb_isoalpha3',
            'rop1',
        ];

        $people_groups = DT_Posts::list_posts( 'peoplegroups', $fields_to_return, false );
        return $people_groups;
    }

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
    }
    public function has_permission(){
        $pass = false;
        foreach ( $this->permissions as $permission ){
            if ( current_user_can( $permission ) ){
                $pass = true;
            }
        }
        return $pass;
    }
}
Disciple_Tools_People_Groups_API_Endpoints::instance();
