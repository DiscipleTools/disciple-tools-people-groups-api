#!/usr/bin/python3

import csv
import sys
import os

# output usage if 3 arguments are not provided
if len(sys.argv) != 4:
    print("""
    This script converts the raw Doxa master list into a csv that can be imported into the Disciple.Tools theme.

    The csv_file is the raw Doxa master list.
    The dictionary_csv_file is a csv file that contains the mapping of the raw Doxa master list to the Disciple.Tools fields.
    The out_csv_file is the output csv file name that will be imported into the Disciple.Tools theme.

    Usage: python convert_csv.py <csv_file> <dictionary_csv_file> <out_csv_file>

    E.g. python convert_csv.py doxa_master_list.csv doxa_dt_fields_dictionary.csv doxa_data_for_importing.csv
    """)
    sys.exit(1)

# take the csv name as an argument
csv_file = sys.argv[1]
dictionary_file = sys.argv[2]
out_csv_file = sys.argv[3]

# output usage if the csv file does not exist
if not os.path.exists(csv_file):
    print(f"Error: {csv_file} does not exist")
    sys.exit(1)

# output usage if the dictionary file does not exist
if not os.path.exists(dictionary_file):
    print(f"Error: {dictionary_file} does not exist")
    sys.exit(1)

if os.path.exists(out_csv_file):
    # ask the user if they want to overwrite the file
    overwrite = input(f"The file {out_csv_file} already exists. Do you want to overwrite it? (y/n)")
    if overwrite != 'y':
        sys.exit(1)

print(f"Converting {csv_file} to {out_csv_file} using dictionary {dictionary_file}")

def create_dictionary(dictionary_file):
    with open(dictionary_file, 'r') as file:
        reader = csv.reader(file)
        keys = next(reader)
        values = next(reader)
        shouldSnakeCase = next(reader)
        dictionary = {}
        for key, value, convertToSnakeCase in zip(keys, values, shouldSnakeCase):
            dictionary[key] = {
                'value': value,
                'shouldSnakeCase': convertToSnakeCase == 'yes',
            }
    return dictionary

def get_headers(dictionary):
    return [dictionary[key]['value'] for key in dictionary.keys() if dictionary[key]['value'] != '']

# copy across the data from the original csv file to the new csv file
def copy_data(csv_file, new_csv_file, dictionary, headers):
    with open(new_csv_file, 'w+', newline='') as out_file:
        with open(csv_file, 'r') as in_file:
            reader = csv.reader(in_file)
            writer = csv.DictWriter(out_file, fieldnames=headers, delimiter=',', quotechar='"', quoting=csv.QUOTE_ALL)
            writer.writeheader()

            # for each row in the original file
            # if the column header is in the headers array, write the value to the new file
            in_headers = next(reader)
            column_indexes_to_keep = [in_headers.index(header) for header in in_headers if header in dictionary and dictionary[header]['value'] != '']
            for row in reader:
                new_row = {}
                for index, column in enumerate(row):
                    if index in column_indexes_to_keep:
                        key = dictionary[in_headers[index]]['value']
                        shouldSnakeCase = dictionary[in_headers[index]]['shouldSnakeCase']
                        if dictionary[in_headers[index]]['shouldSnakeCase']:
                            column = column.replace(' ', '_')
                            column = column.replace('/', '')
                            column = column.lower()
                        new_row[key] = column
                writer.writerow(new_row)

# main function
if __name__ == "__main__":
    dictionary = create_dictionary(dictionary_file)
    headers = get_headers(dictionary)
    copy_data(csv_file, out_csv_file, dictionary, headers)
