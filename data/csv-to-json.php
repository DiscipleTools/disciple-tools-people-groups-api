<?php
/**
 * Script to convert CSV columns to JSON or PHP array format
 *
 * Usage: php csv_to_json.php <column1> [column2] [column3] [output_file] [--use-numeric-value]
 *
 * Example: php csv_to_json.php Name
 * Example: php csv_to_json.php Name PGID PeopleDesc
 * Example: php csv_to_json.php Name PGID output.json
 * Example: php csv_to_json.php Name PGID output.php
 * Example: php csv_to_json.php Name output.php --use-numeric-value
 */

function esc_html( $string ) {
    return $string;
}

/**
 * Format PHP array for output
 *
 * @param array $data The data to format.
 * @return string Formatted PHP array code.
 */
function format_php_array( $data ) {
    $output = var_export( $data, true );

    // Fix formatting: remove space between 'array' and '('
    $output = str_replace( 'array (', 'array(', $output );

    // Fix formatting: remove newline after '=>' when followed by 'array('
    // Pattern: "=>\n  array(" becomes "=> array("
    $output = preg_replace( '/=>\s*\n\s*array\(/', '=> array(', $output );

    return $output;
}

if ( $argc < 2 ) {
    echo 'Usage: php csv_to_json.php <column1> [column2] [column3] [output_file] [--use-numeric-value]' . "\n";
    echo 'Example: php csv_to_json.php Name' . "\n";
    echo 'Example: php csv_to_json.php Name PGID PeopleDesc' . "\n";
    echo 'Example: php csv_to_json.php Name PGID output.json' . "\n";
    echo 'Example: php csv_to_json.php Name PGID output.php' . "\n";
    echo 'Example: php csv_to_json.php Name output.php --use-numeric-value' . "\n";
    exit( 1 );
}

$csv_file = __DIR__ . '/doxa_unique_values.csv';
if ( ! file_exists( $csv_file ) ) {
    echo 'Error: unique_values.csv not found in ' . __DIR__ . "\n";
    exit( 1 );
}

// Check for --use-numeric-value flag
$use_numeric_value = in_array( '--use-numeric-value', $argv, true );

// Get column names from arguments (excluding the flag)
$filtered_args = array_filter( $argv, function( $arg ) {
    return $arg !== '--use-numeric-value';
} );
$filtered_args = array_values( $filtered_args ); // Re-index array

$column1 = isset( $filtered_args[1] ) ? $filtered_args[1] : null;
$column2 = isset( $filtered_args[2] ) && ! empty( $filtered_args[2] ) && ! preg_match( '/\.(json|php)$/', $filtered_args[2] ) ? $filtered_args[2] : null;
$column3 = null;
$output_file = null;

// Determine if we have 2 or 3 columns, or if last arg is output file
if ( $column2 ) {
    // Check if filtered_args[3] is column3 or output file
    if ( isset( $filtered_args[3] ) && ! empty( $filtered_args[3] ) ) {
        if ( preg_match( '/\.(json|php)$/', $filtered_args[3] ) ) {
            $output_file = $filtered_args[3];
        } else {
            $column3 = $filtered_args[3];
            // Check if filtered_args[4] is output file
            if ( isset( $filtered_args[4] ) ) {
                $output_file = $filtered_args[4];
            }
        }
    }
} else {
    // Only one column, check if filtered_args[2] is output file
    if ( isset( $filtered_args[2] ) && preg_match( '/\.(json|php)$/', $filtered_args[2] ) ) {
        $output_file = $filtered_args[2];
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

// Determine output format based on file extension
$output_format = preg_match( '/\.php$/', $output_file ) ? 'php' : 'json';

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

if ( $single_column_mode ) {
    // Single column mode: collect all unique values first
    $unique_values = array();

    while ( ( $row = fgetcsv( $handle ) ) !== false ) {
        $line_number++;

        // Skip empty rows
        if ( empty( array_filter( $row ) ) ) {
            continue;
        }

        if ( ! isset( $row[ $col1_index ] ) || trim( $row[ $col1_index ] ) === '' ) {
            continue;
        }

        $value = trim( $row[ $col1_index ] );

        // Store unique values (use value as key to avoid duplicates)
        if ( ! isset( $unique_values[ $value ] ) ) {
            $unique_values[ $value ] = $value;
        }
    }

    // Sort values alphabetically (case-insensitive)
    uksort( $unique_values, function( $a, $b ) {
        return strcasecmp( $a, $b );
    } );

    // Build data array with numeric or lowercase keys
    $index = 0;
    foreach ( $unique_values as $value ) {
        if ( $use_numeric_value ) {
            // Use numeric incremented values
            $data[] = array(
                'value' => (string) $index,
                'label' => $value,
            );
            $index++;
        } else {
            // Use lowercase values with underscores
            $key = strtolower( str_replace( ' ', '_', $value ) );
            $data[] = array(
                'value' => $key,
                'label' => $value,
            );
        }
    }
} else {
    // Multi-column mode
    while ( ( $row = fgetcsv( $handle ) ) !== false ) {
        $line_number++;

        // Skip empty rows
        if ( empty( array_filter( $row ) ) ) {
            continue;
        }
        // Multi-column mode: array of objects with label/value/description
        if ( ! isset( $row[ $col1_index ] ) || ! isset( $row[ $col2_index ] ) || trim( $row[ $col1_index ] ) === '' || trim( $row[ $col2_index ] ) === '' ) {
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

        // Only add non-empty items (check for empty string, not falsy values)
        if ( $item['label'] !== '' || $item['value'] !== '' ) {
            $data[] = $item;
        }
    }
}

fclose( $handle );

// Generate output based on format
if ( $output_format === 'php' ) {
    // Generate PHP array code
    $output = "<?php\n";
    $output .= "// Auto-generated file - do not edit manually\n";
    $output .= 'return ' . format_php_array( $data ) . ";\n";
} else {
    // Convert to JSON
    $output = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    if ( $output === false ) {
        echo 'Error: Failed to encode JSON' . "\n";
        exit( 1 );
    }
}

// Write to file
$result = file_put_contents( $output_file, $output );

if ( $result === false ) {
    echo 'Error: Could not write to output file: ' . esc_html( $output_file ) . "\n";
    exit( 1 );
}

$count = count( $data );
echo 'Success! Generated ' . esc_html( (string) $count ) . ' ' . ( $single_column_mode ? 'keys' : 'items' ) . "\n";
echo 'Output file: ' . esc_html( $output_file ) . "\n";
