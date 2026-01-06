<?php
/**
 * Daily Cron Sync for People Groups Prayer Data
 *
 * Fetches prayer data from external API and updates people groups.
 *
 * Cron Hook: dt_people_groups_prayer_sync
 * Schedule: daily
 *
 * API Response Format:
 *   {
 *     "campaigns": [
 *       { "dt_id": "602", "people_praying": 4, "daily_prayer_duration": 308 },
 *       ...
 *     ]
 *   }
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Disciple_Tools_People_Groups_API_Sync {

    private static $_instance = null;

    private $api_url = 'https://pray.doxa.life/api/campaigns';

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action( 'dt_people_groups_prayer_sync', [ $this, 'run_sync' ] );

        if ( ! wp_next_scheduled( 'dt_people_groups_prayer_sync' ) ) {
            wp_schedule_event( time(), 'daily', 'dt_people_groups_prayer_sync' );
        }
    }

    public function run_sync(): array {
        $campaigns = $this->fetch_campaigns();

        if ( is_wp_error( $campaigns ) ) {
            dt_write_log( 'People Groups Prayer Sync Error: ' . $campaigns->get_error_message() );
            return [
                'success' => false,
                'error'   => $campaigns->get_error_message(),
            ];
        }

        $results = $this->process_batch( $campaigns );

        update_option( 'dt_people_groups_prayer_sync_last_run', [
            'time'    => current_time( 'mysql' ),
            'results' => $results,
        ] );

        return $results;
    }

    private function fetch_campaigns() {
        $response = wp_remote_get( $this->api_url, [
            'timeout' => 60,
        ] );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'json_error', 'Failed to parse API response' );
        }

        if ( empty( $data['campaigns'] ) || ! is_array( $data['campaigns'] ) ) {
            return new WP_Error( 'invalid_response', 'Missing or invalid campaigns array' );
        }

        return $data['campaigns'];
    }

    private function process_batch( array $campaigns ): array {
        global $wpdb;

        // Get all valid peoplegroups post IDs in one query
        $valid_ids = $wpdb->get_col(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'peoplegroups' AND post_status = 'publish'"
        );
        $valid_ids_map = array_flip( $valid_ids );

        $total_processed = count( $campaigns );
        $total_updated = 0;
        $total_skipped = 0;

        $people_praying_updates = [];
        $duration_updates = [];

        // Build update arrays
        foreach ( $campaigns as $campaign ) {
            if ( empty( $campaign['dt_id'] ) ) {
                $total_skipped++;
                continue;
            }

            $id = absint( $campaign['dt_id'] );

            if ( ! isset( $valid_ids_map[ $id ] ) ) {
                $total_skipped++;
                continue;
            }

            if ( isset( $campaign['people_praying'] ) ) {
                $people_praying_updates[ $id ] = absint( $campaign['people_praying'] );
            }

            if ( isset( $campaign['daily_prayer_duration'] ) ) {
                $duration_updates[ $id ] = absint( $campaign['daily_prayer_duration'] );
            }

            $total_updated++;
        }

        // Bulk update people_praying
        if ( ! empty( $people_praying_updates ) ) {
            $this->bulk_update_meta( $people_praying_updates, 'people_praying' );
        }

        // Bulk update daily_prayer_duration
        if ( ! empty( $duration_updates ) ) {
            $this->bulk_update_meta( $duration_updates, 'daily_prayer_duration' );
        }

        return [
            'success'   => true,
            'processed' => $total_processed,
            'updated'   => $total_updated,
            'skipped'   => $total_skipped,
        ];
    }

    private function bulk_update_meta( array $updates, string $meta_key ): void {
        global $wpdb;

        $post_ids = array_keys( $updates );
        $placeholders = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );

        // Delete existing meta for these post IDs and key
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN ($placeholders)",
                array_merge( [ $meta_key ], $post_ids )
            )
        );

        // Batch insert new values in chunks of 500
        $chunks = array_chunk( $updates, 500, true );

        foreach ( $chunks as $chunk ) {
            $values = [];
            $value_placeholders = [];

            foreach ( $chunk as $post_id => $meta_value ) {
                $values[] = $post_id;
                $values[] = $meta_key;
                $values[] = $meta_value;
                $value_placeholders[] = '(%d, %s, %s)';
            }

            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES " . implode( ', ', $value_placeholders ),
                    $values
                )
            );
        }
    }
}

Disciple_Tools_People_Groups_API_Sync::instance();