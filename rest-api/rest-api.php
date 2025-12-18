<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Disciple_Tools_People_Groups_API_Endpoints
{
    public $permissions = [ 'access_contacts', 'dt_all_access_contacts', 'view_project_metrics' ];

    public function add_api_routes() {
        $namespace = 'dt-public/disciple-tools-people-groups-api/v1';

        register_rest_route(
            $namespace, '/detail/(?P<id>\d+)', [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_people_group_detail' ],
                'permission_callback' => function( WP_REST_Request $request ) {
                    return true;
                },
            ]
        );
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
        register_rest_route(
            $namespace, '/data/all', [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_all_people_groups' ],
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

            $return['posts'][] = [
                'id' => $people_group['ID'],
                'name' => $people_group['name'],
                'display_name' => $people_group['imb_display_name'],
                'wagf_region' => [
                    'key' => $people_group['doxa_wagf_region']['key'],
                    'label' => $this->strip_code( $people_group['doxa_wagf_region']['label'] ),
                ],
                'wagf_block' => [
                    'key' => $people_group['doxa_wagf_block']['key'],
                    'label' => $this->strip_code( $people_group['doxa_wagf_block']['label'] ),
                ],
                'wagf_member' => [
                    'key' => $people_group['doxa_wagf_member']['key'],
                    'label' => $this->strip_code( $people_group['doxa_wagf_member']['label'] ),
                ],
                'location_description' => $people_group['imb_location_description'] ?? '',
                'country' => [
                    'key' => $people_group['imb_isoalpha3']['key'],
                    'label' => $this->strip_code( $people_group['imb_isoalpha3']['label'] ),
                ],
                'population' => $people_group['imb_population'],
                'religion' => [
                    'key' => $people_group['imb_reg_of_religion']['key'],
                    'label' => $this->strip_code( $people_group['imb_reg_of_religion']['label'] ),
                ],
                'rop1' => [
                    'key' => $people_group['imb_reg_of_people_1']['key'],
                    'label' => $this->strip_code( $people_group['imb_reg_of_people_1']['label'] ),
                ],
                'has_photo' => $people_group['imb_has_photo'],
                'picture_url' => $people_group['imb_picture_url'],
                'picture_credit_html' => $people_group['imb_picture_credit_html'],
            ];
        }

        return $return;
    }

    public function get_people_groups_engagement( WP_REST_Request $request ) {
        global $wpdb;

        $results = $wpdb->get_results( "
            SELECT
                p.ID,
                MAX(CASE WHEN pm.meta_key = 'imb_display_name' THEN pm.meta_value END) as name,
                MAX(CASE WHEN pm.meta_key = 'imb_engagement_status' THEN pm.meta_value END) as status,
                MAX(CASE WHEN pm.meta_key = 'imb_lat' THEN pm.meta_value END) as lat,
                MAX(CASE WHEN pm.meta_key = 'imb_lng' THEN pm.meta_value END) as lng
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                AND pm.meta_key IN ('imb_display_name', 'imb_engagement_status', 'imb_lat', 'imb_lng')
            WHERE p.post_type = 'peoplegroups'
                AND p.post_status = 'publish'
            GROUP BY p.ID
        ", ARRAY_A );

        return [
            'posts' => $results,
            'total' => count( $results ),
        ];
    }


    public function valid_keys(){
        return [
            'name',
            'doxa_masteruid',
            'doxa_wagf_region',
            'doxa_wagf_block',
            'doxa_wagf_member',
            'doxa_wagf_uid',
            'imb_uid',
            'imb_pgid',
            'imb_peid',
            'imb_people_name',
            'imb_display_name',
            'imb_alternate_name',
            'imb_isoalpha3',
            'imb_region',
            'imb_subregion',
            'imb_affinity_code',
            'imb_people_description',
            'imb_location_description',
            'imb_population',
            'imb_population_class',
            'imb_evangelical_percentage',
            'imb_evangelical_level',
            'imb_congregation_existing',
            'imb_church_planting',
            'imb_engagement_status',
            'imb_gsec',
            'imb_strategic_priority_index',
            'imb_lostness_priority_index',
            'imb_reg_of_language',
            'imb_language_family',
            'imb_language_class',
            'imb_language_speakers',
            'imb_reg_of_religion', //Registry of religion
            'imb_reg_of_religion_3', //ROR3
            'imb_reg_of_religion_4', //ROR4
            'imb_reg_of_people_3', //ROP3
            'imb_reg_of_people_25',
            'imb_reg_of_people_2',
            'imb_reg_of_people_1',
            'imb_bible_available',
            'imb_jesus_film_available',
            'imb_radio_broadcast_available',
            'imb_gospel_recordings_available',
            'imb_audio_scripture_available',
            'imb_bible_stories_available',
            'imb_total_resources_available',
            'imb_bible_translation_level',
            'imb_bible_year_published',
            'imb_picture_credit_html',
            'imb_picture_url',
            'imb_has_photo',
            'imb_is_indigenous',
            'imb_lat',
            'imb_lng',
            'imb_people_search_text',
            'slug'
        ];
    }

    public function get_all_people_groups( WP_REST_Request $request ) {
        global $wpdb;

        $valid_keys = $this->valid_keys();
        $value_to_key = array_flip( $valid_keys );

        $default_fields = [
            'imb_display_name',
            'slug',
            'reg_of_people_1',
            'doxa_wagf_region',
            'doxa_wagf_block',
            'imb_population',
            'imb_reg_of_religion',
            'imb_reg_of_religion_3',
            'imb_isoalpha3',
            'imb_has_photo',
            'imb_picture_url',
            'imb_picture_credit_html',
            'imb_lat',
            'imb_lng',
            'imb_people_description'
        ];

        $requested_fields = $request->get_param( 'fields' );
        if ( $requested_fields === 'all' ) {
            $requested_fields = array_values( $valid_keys );
        } elseif ( $requested_fields ) {
            $requested_fields = array_map( 'trim', explode( ',', $requested_fields ) );
        } else {
            $requested_fields = $default_fields;
        }

        $resolved_fields = [];
        foreach ( $requested_fields as $field ) {
            if ( isset( $valid_keys[ $field ] ) ) {
                $resolved_fields[ $field ] = $valid_keys[ $field ];
            } elseif ( isset( $value_to_key[ $field ] ) ) {
                $resolved_fields[ $value_to_key[ $field ] ] = $field;
            }
        }

        if ( empty( $resolved_fields ) ) {
            return [
                'posts' => [],
                'total' => 0,
                'error' => 'No valid fields requested',
            ];
        }

        $select_parts = [ 'p.ID as dt_id' ];
        $meta_keys = [];

        foreach ( $resolved_fields as $meta_key ) {
            if ( $meta_key === 'name' ) {
                $select_parts[] = 'p.post_title as name';
            } else {
                //for using key
                $select_parts[] = "MAX(CASE WHEN pm.meta_key = '{$meta_key}' THEN pm.meta_value END) as {$meta_key}";
                $meta_keys[] = "'{$meta_key}'";
            }
        }

        $select_sql = implode( ",\n                ", $select_parts );
        $meta_keys_sql = implode( ', ', $meta_keys );

        $join_clause = '';
        if ( !empty( $meta_keys ) ) {
            $join_clause = "LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                AND pm.meta_key IN ( {$meta_keys_sql} )";
        }

        $results = $wpdb->get_results( "
            SELECT
                {$select_sql}
            FROM {$wpdb->posts} p
            {$join_clause}
            WHERE p.post_type = 'peoplegroups'
                AND p.post_status = 'publish'
            GROUP BY p.ID
        ", ARRAY_A );

        return [
            'posts' => $results,
            'total' => count( $results ),
        ];
    }

    public function get_people_group_detail( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $people_group_post = get_post( $id, ARRAY_A );

        $metadata = get_post_meta( $id );
        $post_settings = DT_Posts::get_post_settings( 'peoplegroups' );
        $fields = $post_settings['fields'];
        foreach ( $fields as $field_key => $field_value ) {
            if ( isset( $metadata[ $field_key ] ) ) {
                if ( $field_value['type'] === 'key_select' ) {
                    $people_group_post[ $field_key ] = [
                        'key' => $metadata[ $field_key ][0],
                        'label' => $this->strip_code( $field_value['default'][ $metadata[ $field_key ][0] ]['label'] ),
                    ];
                } else {
                    $people_group_post[ $field_key ] = $metadata[ $field_key ][0];
                }
            }
        }

        if ( is_wp_error( $people_group_post ) ) {
            return new WP_REST_Response( [ 'error' => $people_group_post->get_error_message() ], 404 );
        }

        if ( is_null( $people_group_post['ID'] ) ) {
            return new WP_REST_Response( [ 'error' => 'People group not found' ], 404 );
        }

        return new WP_REST_Response( $people_group_post, 200 );
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

    private function strip_code( $label ) {
        return str_contains( $label, ':' ) ? trim( explode( ':', $label )[1] ) : $label;
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
