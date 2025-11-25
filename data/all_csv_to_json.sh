#!/bin/bash

php csv-to-json.php ROP1 Affbloc rop1.json
php csv-to-json.php ROP2 PplClstr rop2.json
php csv-to-json.php ROP25 Ethne rop25.json
php csv-to-json.php ROP3 PplNm rop3.json

php csv-to-json.php ROR Rlgn RORdesc ror.json
php csv-to-json.php ROR3 RlgnBs ror3.json
php csv-to-json.php ROR4 RlgnDiv ror4.json

php csv-to-json.php AffCd Aff affcd.json
php csv-to-json.php ROG Ctry rog.json
php csv-to-json.php ISOalpha3 Ctry isoalpha3.json
php csv-to-json.php GSEC GSECbrf GSEClng gsec.json

php csv-to-json.php Regn regn.json
php csv-to-json.php RegnSub regnsub.json
php csv-to-json.php EngStat engstat.json

php csv-to-json.php ROL Lang rol.json
php csv-to-json.php SPI SPIspi spi.json
php csv-to-json.php LPI LPIname LPIdesc lpi.json
php csv-to-json.php LangFamily langfamily.json
php csv-to-json.php LangClass langclass.json --use-numeric-value
php csv-to-json.php LvlBible lblbible.json --use-numeric-value
php csv-to-json.php DoxaMaster doxamaster.json
php csv-to-json.php 'WAGF Region' wagf_region.json
php csv-to-json.php 'WAGF BLOCK' wagf_block.json
