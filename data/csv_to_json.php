<?php
/**
 * Script to convert CSV columns to JSON format
 *
 * Usage: php csv_to_json.php <column1> <column2> [column3] [output_file]
 *
 * Example: php csv_to_json.php Name PGID PeopleDesc
 * Example: php csv_to_json.php Name PGID output.json
 */

if ($argc < 3) {
    echo "Usage: php csv_to_json.php <column1> <column2> [column3] [output_file]\n";
    echo "Example: php csv_to_json.php Name PGID PeopleDesc\n";
    echo "Example: php csv_to_json.php Name PGID output.json\n";
    exit(1);
}

$csv_file = __DIR__ . '/unique_values.csv';
if (!file_exists($csv_file)) {
    echo "Error: unique_values.csv not found in " . __DIR__ . "\n";
    exit(1);
}

// Get column names from arguments
$column1 = $argv[1];
$column2 = $argv[2];
$column3 = isset($argv[3]) && !empty($argv[3]) && !preg_match('/\.json$/', $argv[3]) ? $argv[3] : null;
$output_file = null;

// Check if last argument is output file
if (isset($argv[3]) && preg_match('/\.json$/', $argv[3])) {
    $output_file = $argv[3];
} elseif (isset($argv[4])) {
    $output_file = $argv[4];
}

// Default output file name
if (!$output_file) {
    $output_file = __DIR__ . '/' . strtolower($column1) . '_' . strtolower($column2);
    if ($column3) {
        $output_file .= '_' . strtolower($column3);
    }
    $output_file .= '.json';
}

// Open CSV file
$handle = fopen($csv_file, 'r');
if ($handle === false) {
    echo "Error: Could not open CSV file\n";
    exit(1);
}

// Read header row
$headers = fgetcsv($handle);
if ($headers === false) {
    echo "Error: Could not read CSV header\n";
    fclose($handle);
    exit(1);
}

// Find column indices
$col1_index = array_search($column1, $headers);
$col2_index = array_search($column2, $headers);
$col3_index = $column3 ? array_search($column3, $headers) : false;

if ($col1_index === false) {
    echo "Error: Column '$column1' not found in CSV\n";
    echo "Available columns: " . implode(', ', $headers) . "\n";
    fclose($handle);
    exit(1);
}

if ($col2_index === false) {
    echo "Error: Column '$column2' not found in CSV\n";
    echo "Available columns: " . implode(', ', $headers) . "\n";
    fclose($handle);
    exit(1);
}

if ($column3 && $col3_index === false) {
    echo "Error: Column '$column3' not found in CSV\n";
    echo "Available columns: " . implode(', ', $headers) . "\n";
    fclose($handle);
    exit(1);
}

// Read data and build JSON array
$data = [];
$line_number = 1;

while (($row = fgetcsv($handle)) !== false) {
    $line_number++;

    // Skip empty rows
    if (empty(array_filter($row))) {
        continue;
    }

    if ( !isset( $row[$col1_index] ) || !isset( $row[$col2_index] ) || empty( trim( $row[$col1_index] ) ) || empty( trim( $row[$col2_index] ) ) ) {
        continue;
    }

    // Build object
    $item = [
        'value' => isset($row[$col1_index]) ? trim($row[$col1_index]) : '',
        'label' => isset($row[$col2_index]) ? trim($row[$col2_index]) : ''
    ];

    // Add description if third column was provided
    if ($column3 && $col3_index !== false) {
        $item['description'] = isset($row[$col3_index]) ? trim($row[$col3_index]) : '';
    }

    // Only add non-empty items
    if (!empty($item['label']) || !empty($item['value'])) {
        $data[] = $item;
    }
}

fclose($handle);

// Convert to JSON
$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if ($json === false) {
    echo "Error: Failed to encode JSON\n";
    exit(1);
}

// Write to file
$result = file_put_contents($output_file, $json);

if ($result === false) {
    echo "Error: Could not write to output file: $output_file\n";
    exit(1);
}

echo "Success! Generated " . count($data) . " items\n";
echo "Output file: $output_file\n";

