<?php

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Disciple_Tools_People_Groups_Extras {
    public $post_type = 'peoplegroups';
    public function __construct() {
        add_filter( 'dt_custom_fields_settings', array( $this, 'dt_custom_fields_settings' ), 10, 2 );
        add_filter( 'dt_details_additional_tiles', array( $this, 'dt_details_additional_tiles' ), 10, 2 );
    }

    public function dt_custom_fields_settings( $fields, $post_type = '' ) {
        $debug = false;
        if ( $post_type === $this->post_type ) {
            $fields['imb_pgid'] = [
                'name' => __( 'IMB - People Group ID', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The IMB People Groups.org ID for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'text',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
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

            $isoalpha3_default = $this->get_default_values( 'isoalpha3', include_value: true, sort_fn: function( $a, $b ) {
                return strcmp( $a['label'], $b['label'] );
            } );
            $fields['imb_isoalpha3'] = [
                'name' => __( 'IMB - ISO Alpha 3', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The ISO Alpha 3 code for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => $isoalpha3_default,
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
            $fields['imb_pop'] = [
                'name' => __( 'IMB - Population', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The population for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'number',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_pop_cls'] = [
                'name' => __( 'IMB - Population Class', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The population class for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'Less than 10,000', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( '100,000 - 249,999', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( '25,000 - 49,999', 'disciple-tools-people-groups-api' ),
                    ],
                    '3' => [
                        'label' => __( '250,000 - 499,999', 'disciple-tools-people-groups-api' ),
                    ],
                    '4' => [
                        'label' => __( '10,000 - 24,999', 'disciple-tools-people-groups-api' ),
                    ],
                    '5' => [
                        'label' => __( '500,000 - 999,999', 'disciple-tools-people-groups-api' ),
                    ],
                    '6' => [
                        'label' => __( '50,000 - 99,999', 'disciple-tools-people-groups-api' ),
                    ],
                    '7' => [
                        'label' => __( '1,000,00 - 2,499,999', 'disciple-tools-people-groups-api' ),
                    ],
                    '8' => [
                        'label' => __( '5,000,000 - 9,999,999', 'disciple-tools-people-groups-api' ),
                    ],
                    '9' => [
                        'label' => __( '2,500,000 - 4,999,999', 'disciple-tools-people-groups-api' ),
                    ],
                    '10' => [
                        'label' => __( '10,000,000+', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_evngpct'] = [
                'name' => __( 'IMB - Evangelical Percentage', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The evangelical percentage for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'number',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_evnglvl'] = [
                'name' => __( 'IMB - Evangelical Level', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The evangelical level for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'No Known Evangelicals', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Less than 2%', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( '2% or Greater but Less than 5%', 'disciple-tools-people-groups-api' ),
                    ],
                    '3' => [
                        'label' => __( '5% or Greater but Less than 10%', 'disciple-tools-people-groups-api' ),
                    ],
                    '4' => [
                        'label' => __( '10% or Greater but Less than 15%', 'disciple-tools-people-groups-api' ),
                    ],
                    '5' => [
                        'label' => __( '15% or Greater but Less than 20%', 'disciple-tools-people-groups-api' ),
                    ],
                    '6' => [
                        'label' => __( '20% or Greater but Less than 30%', 'disciple-tools-people-groups-api' ),
                    ],
                    '7' => [
                        'label' => __( '30% or Greater but Less than 40%', 'disciple-tools-people-groups-api' ),
                    ],
                    '8' => [
                        'label' => __( '40% or Greater but Less than 50%', 'disciple-tools-people-groups-api' ),
                    ],
                    '9' => [
                        'label' => __( '50% or Greater but Less than 75%', 'disciple-tools-people-groups-api' ),
                    ],
                    '10' => [
                        'label' => __( '75% or Greater', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_congexst'] = [
                'name' => __( 'IMB - Congregation Existence', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The existence of a congregation within the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'No', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Yes', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_plnting'] = [
                'name' => __( 'IMB - Planting Status', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The planting status for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'No churches planted', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Dispersed church planting', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'Concentrated church planting', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            if ( !$debug ) {
                $rol_default = $this->get_default_values( 'rol', include_value: true, sort_fn: function( $a, $b ) {
                    return strcmp( $a['label'], $b['label'] );
                } );
                $fields['imb_rol'] = [
                    'name' => __( 'IMB - Language', 'disciple-tools-people-groups-api' ),
                    'description' => __( 'The language for the people group', 'disciple-tools-people-groups-api' ),
                    'type' => 'key_select',
                    'default' => $rol_default,
                    'post_type' => $this->post_type,
                    'tile' => 'people_groups',
                    'show_in_table' => 35,
                ];

                $langfamily_default = $this->get_default_values( 'langfamily', include_value: true, sort_fn: function( $a, $b ) {
                    return strcmp( $a['label'], $b['label'] );
                } );
                $fields['imb_langfamily'] = [
                    'name' => __( 'IMB - Language Family', 'disciple-tools-people-groups-api' ),
                    'description' => __( 'The language family for the people group', 'disciple-tools-people-groups-api' ),
                    'type' => 'key_select',
                    'default' => $langfamily_default,
                    'post_type' => $this->post_type,
                    'tile' => 'people_groups',
                    'show_in_table' => 35,
                ];

                $langclass_default = $this->get_default_values( 'langclass', include_value: true, sort_fn: function( $a, $b ) {
                    return strcmp( $a['label'], $b['label'] );
                } );
                $fields['imb_langclass'] = [
                    'name' => __( 'IMB - Language Class', 'disciple-tools-people-groups-api' ),
                    'description' => __( 'The language class for the people group', 'disciple-tools-people-groups-api' ),
                    'type' => 'key_select',
                    'default' => $langclass_default,
                    'post_type' => $this->post_type,
                    'tile' => 'people_groups',
                    'show_in_table' => 35,
                ];
            }

            $fields['imb_langspkrs'] = [
                'name' => __( 'IMB - Language Speakers', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The number of language speakers for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'number',
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


            $fields['imb_spi'] = [
                'name' => __( 'IMB - Strategic Priority Index', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The Strategic Priority Index for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'Unengaged and Unreached', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Engaged yet Unreached', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'No Longer Unreached', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_lpi'] = [
                'name' => __( 'IMB - L Priority Index', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The L Priority Index for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'Frontier Unreached People Group', 'disciple-tools-people-groups-api' ),
                        'description' => __( '< 0.1% Evangelical', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Pioneer Unreached People Group', 'disciple-tools-people-groups-api' ),
                        'description' => __( '0.1% to 0.5% Evangelical', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'Expanding Unreached People Group', 'disciple-tools-people-groups-api' ),
                        'description' => __( '0.5% to 2% Evangelical', 'disciple-tools-people-groups-api' ),
                    ],
                    '3' => [
                        'label' => __( 'Minimally Reached People Group', 'disciple-tools-people-groups-api' ),
                        'description' => __( '2% to 3% Evangelical', 'disciple-tools-people-groups-api' ),
                    ],
                    '4' => [
                        'label' => __( 'Marginally Reached People Group', 'disciple-tools-people-groups-api' ),
                        'description' => __( '3% to 6% Evangelical', 'disciple-tools-people-groups-api' ),
                    ],
                    '5' => [
                        'label' => __( 'Moderately Reached People Group', 'disciple-tools-people-groups-api' ),
                        'description' => __( '6% to 20% Evangelical', 'disciple-tools-people-groups-api' ),
                    ],
                    '6' => [
                        'label' => __( 'Significantly Reached People Group', 'disciple-tools-people-groups-api' ),
                        'description' => __( '> 20% Evangelical', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_engstat'] = [
                'name' => __( 'IMB - Engagement Status', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The engagement status for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    'engaged' => [
                        'label' => __( 'Engaged', 'disciple-tools-people-groups-api' ),
                    ],
                    'unengaged' => [
                        'label' => __( 'Unengaged', 'disciple-tools-people-groups-api' ),
                    ],
                ],
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

            $fields['imb_bible'] = [
                'name' => __( 'IMB - Bible Translation Status', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The bible translation status for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'Not Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'None', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_Jesus'] = [
                'name' => __( 'IMB - Jesus Film Status', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The Jesus film status for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'Not Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'None', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_radio'] = [
                'name' => __( 'IMB - Radio Broadcast Status', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The radio broadcast status for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'Not Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'None', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_gospel'] = [
                'name' => __( 'IMB - Gospel Translation Status', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The gospel translation status for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'Not Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'None', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_audio'] = [
                'name' => __( 'IMB - Audio Bible Status', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The audio bible status for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'Not Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'None', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_stories'] = [
                'name' => __( 'IMB - Bible Stories Translation Status', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The bible stories translation status for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'Not Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Available', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'None', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_restot'] = [
                'name' => __( 'IMB - Total Resources Available', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The total resources available for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'number',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_bible_level'] = [
                'name' => __( 'IMB - Bible Level', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The bible level for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'key_select',
                'default' => [
                    '0' => [
                        'label' => __( 'None', 'disciple-tools-people-groups-api' ),
                    ],
                    '1' => [
                        'label' => __( 'Stories', 'disciple-tools-people-groups-api' ),
                    ],
                    '2' => [
                        'label' => __( 'Selections', 'disciple-tools-people-groups-api' ),
                    ],
                    '3' => [
                        'label' => __( 'New Testament', 'disciple-tools-people-groups-api' ),
                    ],
                    '4' => [
                        'label' => __( 'Bible', 'disciple-tools-people-groups-api' ),
                    ],
                ],
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];

            $fields['imb_yrpub'] = [
                'name' => __( 'IMB - Year of Bible Publication', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The year of bible publication for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'number',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_piccredit'] = [
                'name' => __( 'IMB - Picture Credit', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The picture credit for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'text',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_picurl'] = [
                'name' => __( 'IMB - Picture URL', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The picture url for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'text',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
            $fields['imb_photo'] = [
                'name' => __( 'IMB - Photo URL', 'disciple-tools-people-groups-api' ),
                'description' => __( 'The photo url for the people group', 'disciple-tools-people-groups-api' ),
                'type' => 'boolean',
                'post_type' => $this->post_type,
                'tile' => 'people_groups',
                'show_in_table' => 35,
            ];
        }

        $fields['imb_indigenous'] = [
            'name' => __( 'IMB - Indigenous Status', 'disciple-tools-people-groups-api' ),
            'description' => __( 'The indigenous status for the people group', 'disciple-tools-people-groups-api' ),
            'type' => 'key_select',
            'default' => [
                '0' => [
                    'label' => __( 'Diaspora', 'disciple-tools-people-groups-api' ),
                ],
                '1' => [
                    'label' => __( 'Indigenous', 'disciple-tools-people-groups-api' ),
                ],
            ],
            'post_type' => $this->post_type,
            'tile' => 'people_groups',
            'show_in_table' => 35,
        ];
        $fields['imb_lat'] = [
            'name' => __( 'IMB - Latitude', 'disciple-tools-people-groups-api' ),
            'description' => __( 'The latitude for the people group', 'disciple-tools-people-groups-api' ),
            'type' => 'number',
            'post_type' => $this->post_type,
            'tile' => 'people_groups',
            'show_in_table' => 35,
        ];
        $fields['imb_lng'] = [
            'name' => __( 'IMB - Longitude', 'disciple-tools-people-groups-api' ),
            'description' => __( 'The longitude for the people group', 'disciple-tools-people-groups-api' ),
            'type' => 'number',
            'post_type' => $this->post_type,
            'tile' => 'people_groups',
            'show_in_table' => 35,
        ];
        $fields['location'] = [
            'name' => __( 'Location', 'disciple-tools-people-groups-api' ),
            'description' => __( 'The latitude, longitude location for the people group', 'disciple-tools-people-groups-api' ),
            'type' => 'location',
            'post_type' => $this->post_type,
            'tile' => 'people_groups',
            'show_in_table' => 35,
        ];
        $fields['imb_peoplesearch'] = [
            'name' => __( 'IMB - People Search Text', 'disciple-tools-people-groups-api' ),
            'description' => __( 'The people search text for the people group', 'disciple-tools-people-groups-api' ),
            'type' => 'text',
            'post_type' => $this->post_type,
            'tile' => 'people_groups',
            'show_in_table' => 35,
        ];

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
