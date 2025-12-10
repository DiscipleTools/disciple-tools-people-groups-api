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
        register_rest_route(
            $namespace, '/data/engagement', [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_people_groups_engagement' ],
                'permission_callback' => function( WP_REST_Request $request ) {
                    return true;
                },
            ]
        );
    }


    public function get_people_groups( WP_REST_Request $request ) {
        $fields_to_return = [
            'id',
            'doxa_wagf_region',
            'doxa_wagf_block',
            'doxa_wagf_member',
            'name',
            'imb_display_name',
            'imb_location_description',
            'imb_population',
            'imb_reg_of_religion',
            'imb_isoalpha3',
            'imb_reg_of_people_1',
            'imb_has_photo',
            'imb_picture_url',
            'imb_picture_credit_html',
        ];

        $pagination_query = $this->get_pagination_query( $request );

        $search_and_filter_query = array_merge( $pagination_query, [
            'fields_to_return' => $fields_to_return,
        ] );

        $people_groups = DT_Posts::list_posts( 'peoplegroups', $search_and_filter_query, false );

        $return = [
            'posts' => [],
            'total' => $people_groups['total'],
        ];

        foreach ( $people_groups['posts'] as $people_group ) {
            $strip_code = function( $label ) {
                return str_contains( $label, ':' ) ? trim( explode( ':', $label )[1] ) : $label;
            };

            $return['posts'][] = [
                'id' => $people_group['ID'],
                'name' => $people_group['name'],
                'display_name' => $people_group['imb_display_name'],
                'wagf_region' => [
                    'key' => $people_group['doxa_wagf_region']['key'],
                    'label' => $strip_code( $people_group['doxa_wagf_region']['label'] ),
                ],
                'wagf_block' => [
                    'key' => $people_group['doxa_wagf_block']['key'],
                    'label' => $strip_code( $people_group['doxa_wagf_block']['label'] ),
                ],
                'wagf_member' => [
                    'key' => $people_group['doxa_wagf_member']['key'],
                    'label' => $strip_code( $people_group['doxa_wagf_member']['label'] ),
                ],
                'location_description' => $people_group['imb_location_description'],
                'country' => [
                    'key' => $people_group['imb_isoalpha3']['key'],
                    'label' => $strip_code( $people_group['imb_isoalpha3']['label'] ),
                ],
                'population' => $people_group['imb_population'],
                'religion' => [
                    'key' => $people_group['imb_reg_of_religion']['key'],
                    'label' => $strip_code( $people_group['imb_reg_of_religion']['label'] ),
                ],
                'rop1' => [
                    'key' => $people_group['imb_reg_of_people_1']['key'],
                    'label' => $strip_code( $people_group['imb_reg_of_people_1']['label'] ),
                ],
                'has_photo' => $people_group['imb_has_photo'],
                'picture_url' => $people_group['imb_picture_url'],
                'picture_credit_html' => $people_group['imb_picture_credit_html'],
            ];
        }

        return $return;
    }

    public function get_people_groups_engagement( WP_REST_Request $request ) {

        $pagination_query = $this->get_pagination_query( $request );
        $search_and_filter_query = array_merge( $pagination_query, [
            'fields_to_return' => [
                'id',
                'imb_display_name',
                'imb_engagement_status',
                'imb_gsec',
                'imb_lat',
                'imb_lng',
            ],
        ] );
        $people_groups = DT_Posts::list_posts( 'peoplegroups', $search_and_filter_query, false );
        return $people_groups;
    }

    private function get_pagination_query( WP_REST_Request $request ) {
        $search_and_filter_query = [];

        $s = $request->get_param( 's' );
        if ( $s ) {
            $search_and_filter_query['text'] = $s;
            $search_and_filter_query['fields_to_search'] = [
                'name',
                'imb_display_name',
                'imb_location_description',
            ];
        }
        $limit = $request->get_param( 'limit' );
        if ( $limit ) {
            $search_and_filter_query['limit'] = $limit;
        }
        $offset = $request->get_param( 'offset' );
        if ( $offset ) {
            $search_and_filter_query['offset'] = $offset;
        }
        $sort = $request->get_param( 'sort' );
        if ( $sort ) {
            $search_and_filter_query['sort'] = $sort;
        }

        return $search_and_filter_query;
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
