<?php

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Disciple_Tools_People_Groups_Extras {
    public $post_type = 'peoplegroups';
    public function __construct() {
        add_filter( 'dt_custom_fields_settings', array( $this, 'dt_custom_fields_settings' ), 10, 2 );
        add_filter( 'dt_details_additional_tiles', array( $this, 'dt_details_additional_tiles' ), 10, 2 );
    }

    public function dt_custom_fields_settings( $fields, $post_type = '' ) {
        $debug = true;
        if ( $post_type === $this->post_type ) {
            $rop1_default = $this->get_default_values( 'rop1', include_value: true, sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );

            $fields['rop1'] = [
                'name' => __( 'ROP1 - Affinity Block', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The affinity block for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $rop1_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $rop2_default = $this->get_default_values( 'rop2', include_value: true, sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );
            $fields['rop2'] = [
                'name' => __( 'ROP2 - People Cluster', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The people cluster for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $rop2_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            if ( !$debug ) {
                $rop25_default = $this->get_default_values( 'rop25', include_value: true, sort_fn: function( $a, $b ) {
                    return strcmp( $a['label'], $b['label'] );
                } );
                $fields['rop25'] = [
                    'name' => __( 'ROP25 - Ethne', 'disciple-tools-people-groups-api' ),
                    'description' => __( 'The ethne of the people group', 'disciple-tools-people-groups-api' ),
                    'type' => 'key_select',
                    'default' => $rop25_default,
                    'post_type' => $this->post_type,
                    'tile' => 'people_groups',
                    'show_in_table' => 35,
                ];
            }

            $fields['rop3'] = [
                'name' => __( 'ROP3 - People Name', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The name for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'text',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $ror_default = $this->get_default_values( 'ror', include_value: true, sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );
            $fields['ror'] = [
                'name' => __( 'ROR - Religion', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The religion for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $ror_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $ror3_default = $this->get_default_values( 'ror3', include_value: true, sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );
            $fields['ror3'] = [
                'name' => __( 'ROR3 - Religion Base', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The religion base for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $ror3_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $ror4_default = $this->get_default_values( 'ror4', include_value: true, sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );
            $fields['ror4'] = [
                'name' => __( 'ROR4 - Religion Division', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The religion division for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $ror4_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
        }
        return $fields;
    }

    public function dt_details_additional_tiles( $tiles, $post_type = '' ) {
        if ( $post_type === $this->post_type ) {
            $tiles['imb'] = [
                'label' => 'IMB: PeopleGroups.org Data',
            ];
            $tiles['people_groups'] = [
                'label' => 'People Groups Data',
            ];
        }
        return $tiles;
    }

    private function get_default_values( $file, $include_value = false, $sort_fn = null ) {
        $default = file_get_contents( plugin_dir_path( __FILE__ ) . '../data/' . $file . '.json' );
        $default = json_decode( $default, true );
        if ( $sort_fn ) {
            usort( $default, $sort_fn );
        } else {
            sort( $default );
        }
        $default = array_reduce( $default, function( $acc, $item ) use ( $include_value ) {
            $acc[$item['value']] = [
                'label' => $include_value ? $item['label'] . ' - ' . $item['value'] : $item['label'],
            ];
            if ( isset( $item['description'] ) ) {
                $acc[$item['value']]['description'] = $item['description'];
            }
            return $acc;
        }, [] );
        return $default;
    }
}

new Disciple_Tools_People_Groups_Extras();
