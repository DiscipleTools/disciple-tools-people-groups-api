<?php
/**
 * Script to convert CSV columns to JSON format
 *
 * Usage: php csv_to_json.php <column1> [column2] [column3] [output_file]
 *
 * Example: php csv_to_json.php Name
 * Example: php csv_to_json.php Name PGID PeopleDesc
 * Example: php csv_to_json.php Name PGID output.json
 */

function esc_html( $string ) {
    return $string;
}

if ( $argc < 2 ) {
    echo 'Usage: php csv_to_json.php <column1> [column2] [column3] [output_file]' . "\n";
    echo 'Example: php csv_to_json.php Name' . "\n";
    echo 'Example: php csv_to_json.php Name PGID PeopleDesc' . "\n";
    echo 'Example: php csv_to_json.php Name PGID output.json' . "\n";
    exit( 1 );
}

$csv_file = __DIR__ . '/unique_values.csv';
if ( ! file_exists( $csv_file ) ) {
    echo 'Error: unique_values.csv not found in ' . __DIR__ . "\n";
    exit( 1 );
}

// Get column names from arguments
$column1 = $argv[1];
$column2 = isset( $argv[2] ) && ! empty( $argv[2] ) && ! preg_match( '/\.json$/', $argv[2] ) ? $argv[2] : null;
$column3 = null;
$output_file = null;

// Determine if we have 2 or 3 columns, or if last arg is output file
if ( $column2 ) {
    // Check if argv[3] is column3 or output file
    if ( isset( $argv[3] ) && ! empty( $argv[3] ) ) {
        if ( preg_match( '/\.json$/', $argv[3] ) ) {
            $output_file = $argv[3];
        } else {
            $column3 = $argv[3];
            // Check if argv[4] is output file
            if ( isset( $argv[4] ) ) {
                $output_file = $argv[4];
            }
        }
    }
} else {
    // Only one column, check if argv[2] is output file
    if ( isset( $argv[2] ) && preg_match( '/\.json$/', $argv[2] ) ) {
        $output_file = $argv[2];
    }
}

// Default output file name
if ( ! $output_file ) {
    $output_file = __DIR__ . '/' . strtolower( $column1 );
    if ( $column2 ) {
        $output_file .= '_' . strtolower( $column2 );
    }
    if ( $column3 ) {
        $output_file .= '_' . strtolower( $column3 );
    }
    $output_file .= '.json';
}

// Open CSV file
$handle = fopen( $csv_file, 'r' );
if ( $handle === false ) {
    echo 'Error: Could not open CSV file' . "\n";
    exit( 1 );
}

// Read header row
$headers = fgetcsv( $handle );
if ( $headers === false ) {
    echo 'Error: Could not read CSV header' . "\n";
    fclose( $handle );
    exit( 1 );
}

// Find column indices
$col1_index = array_search( $column1, $headers, true );
$col2_index = $column2 ? array_search( $column2, $headers, true ) : false;
$col3_index = $column3 ? array_search( $column3, $headers, true ) : false;

if ( $col1_index === false ) {
    echo 'Error: Column \'' . esc_html( $column1 ) . '\' not found in CSV' . "\n";
    echo 'Available columns: ' . esc_html( implode( ', ', $headers ) ) . "\n";
    fclose( $handle );
    exit( 1 );
}

if ( $column2 && $col2_index === false ) {
    echo 'Error: Column \'' . esc_html( $column2 ) . '\' not found in CSV' . "\n";
    echo 'Available columns: ' . esc_html( implode( ', ', $headers ) ) . "\n";
    fclose( $handle );
    exit( 1 );
}

if ( $column3 && $col3_index === false ) {
    echo 'Error: Column \'' . esc_html( $column3 ) . '\' not found in CSV' . "\n";
    echo 'Available columns: ' . esc_html( implode( ', ', $headers ) ) . "\n";
    fclose( $handle );
    exit( 1 );
}

// Read data and build JSON
$data = array();
$line_number = 1;
$single_column_mode = ! $column2;

while ( ( $row = fgetcsv( $handle ) ) !== false ) {
    $line_number++;

    // Skip empty rows
    if ( empty( array_filter( $row ) ) ) {
        continue;
    }

    if ( $single_column_mode ) {
        // Single column mode: use value as key (lowercased, whitespace to underscores)
        if ( ! isset( $row[ $col1_index ] ) || empty( trim( $row[ $col1_index ] ) ) ) {
            continue;
        }

        $value = trim( $row[ $col1_index ] );
        $key = strtolower( str_replace( ' ', '_', $value ) );

        // Only add if key doesn't already exist (avoid duplicates)
        if ( ! isset( $data[ $key ] ) ) {
            $data[] = array(
                'value' => $key,
                'label' => $value,
            );
        }
    } else {
        // Multi-column mode: array of objects with label/value/description
        if ( ! isset( $row[ $col1_index ] ) || ! isset( $row[ $col2_index ] ) || empty( trim( $row[ $col1_index ] ) ) || empty( trim( $row[ $col2_index ] ) ) ) {
            continue;
        }

        // Build object
        $item = array(
            'value' => isset( $row[ $col1_index ] ) ? trim( $row[ $col1_index ] ) : '',
            'label' => isset( $row[ $col2_index ] ) ? trim( $row[ $col2_index ] ) : '',
        );

        // Add description if third column was provided
        if ( $column3 && $col3_index !== false ) {
            $item['description'] = isset( $row[ $col3_index ] ) ? trim( $row[ $col3_index ] ) : '';
        }

        // Only add non-empty items
        if ( ! empty( $item['label'] ) || ! empty( $item['value'] ) ) {
            $data[] = $item;
        }
    }
}

fclose( $handle );

// Convert to JSON
$json = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );

if ( $json === false ) {
    echo 'Error: Failed to encode JSON' . "\n";
    exit( 1 );
}

// Write to file
$result = file_put_contents( $output_file, $json );

if ( $result === false ) {
    echo 'Error: Could not write to output file: ' . esc_html( $output_file ) . "\n";
    exit( 1 );
}

$count = count( $data );
echo 'Success! Generated ' . esc_html( (string) $count ) . ' ' . ( $single_column_mode ? 'keys' : 'items' ) . "\n";
echo 'Output file: ' . esc_html( $output_file ) . "\n";
