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
            $fields['imb_peid'] = [
                'name' => __( 'IMB - PEID', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The IMB ID for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'text',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $regn_default = $this->get_default_values( 'regn', sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );
            $fields['imb_regn'] = [
                'name' => __( 'IMB - Region', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The region for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $regn_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $regnsub_default = $this->get_default_values( 'regnsub', sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );
            $fields['imb_regnsub'] = [
                'name' => __( 'IMB - Sub Region', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The subregion for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $regnsub_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $rog_default = $this->get_default_values( 'rog', include_value: true, sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );
            $fields['imb_rog'] = [
                'name' => __( 'IMB - Country', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The country for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $rog_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $affcd_default = $this->get_default_values( 'affcd', include_value: true, sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );
            $fields['imb_affcd'] = [
                'name' => __( 'IMB - Affinity Code', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The affinity code for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $affcd_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_name'] = [
                'name' => __( 'IMB - People Name', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The name for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'text',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_name_display'] = [
                'name' => __( 'IMB - Display Name', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The display name for the people group for using in the UI', 'disciple-tools-people-groups-api' ),
                'type' => 'text',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_name_alt'] = [
                'name' => __( 'IMB - Alternate Name', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The alternate name for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'text',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_location_desc'] = [
                'name' => __( 'IMB - Location Description', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The location description of where the people live', 'disciple-tools-people-groups-api' ),
                'type' => 'text',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $gsec_default = $this->get_default_values( 'gsec', include_value: true, sort_fn: function( $a, $b ) {
                return (int) $a['value'] - (int) $b['value'];
            } );
            $fields['imb_gsec'] = [
                'name' => __( 'IMB - GSEC', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The GSEC for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $gsec_default,
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

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
                'name' => __( 'ROP3 - ID', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The ROP3 ID for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'number',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['pplnm'] = [
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
