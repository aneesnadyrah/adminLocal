<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
require "config/DBFactory.php";
include "api/functions.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Assuming you retrieve the ID from the query string parameter
$refNo = isset($_GET['ref']) ? $_GET['ref'] : null;

function getReferenceData()
{
    $json_data = '[
    {
        "no_reference": "KUTT.HT/TEL/A2/05/2021/0095",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{15}",
        "length": 132
    },
    {
        "no_reference": "KUTT.K/PWR/A3/05/2021/0136",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85}",
        "length": 20250
    },
    {
        "no_reference": "KUTT.HT/TEL/A2/05/2021/0094",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{20}",
        "length": 398
    },
    {
        "no_reference": "KUTT.D/TEL/A3/04/2021/0013",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{8,9,10,11}",
        "length": 1400
    },
    {
        "no_reference": "KUTT.KT/PWR/A1/06/2021/0139",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{88}",
        "length": 7
    },
    {
        "no_reference": "KUTT.D/TEL/A3/04/2021/0014",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2,3}",
        "length": 4380
    },
    {
        "no_reference": "KUTT.K/TEL/A3/07/2021/0143",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{120}",
        "length": 6507
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/05/2021/0110",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{21,22,23,24,25,26,27,28,29,30}",
        "length": 16433
    },
    {
        "no_reference": "KUTT.K/PWR/A3/05/2021/0113",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{53,54,55}",
        "length": 1220
    },
    {
        "no_reference": "KUTT.K/PWR/A3/04/2021/0022",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{12,13,14}",
        "length": 1055
    },
    {
        "no_reference": "KUTT.KN/PWR/A1/06/2021/0134",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{86}",
        "length": 40
    },
    {
        "no_reference": "KUTT.HT/PWR/A3/07/2021/0141",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143}",
        "length": 2300
    },
    {
        "no_reference": "KUTT.KN/PWR/A1/07/2021/0151",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{170}",
        "length": 50
    },
    {
        "no_reference": "KUTT.K/PWR/A2/07/2021/0153",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{174,175,176,177}",
        "length": 610
    },
    {
        "no_reference": "KUTT.D/PWR/A3/05/2021/0124",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{31,32,33,34,35,36,37,38}",
        "length": 1680
    },
    {
        "no_reference": "KUTT.M/PWR/A3/06/2021/0140",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109}",
        "length": 21710
    },
    {
        "no_reference": "KUTT.HT/PWR/A3/06/2021/0138",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{110,111,112,113,114,115,116,117,118}",
        "length": 3900
    },
    {
        "no_reference": "KUTT.HT/PWR/A3/07/2021/0144",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163}",
        "length": 3299
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/05/2021/0114",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{56,57}",
        "length": 11686
    },
    {
        "no_reference": "KUTT.B/PWR/A3/05/2021/0112",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{45,46,47,48,49,50,51,52}",
        "length": 18390
    },
    {
        "no_reference": "KUTT.K/PWR/A1/04/2021/0006",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1}",
        "length": 70
    },
    {
        "no_reference": "KUTT.S/TEL/A2/05/2021/0115",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{58}",
        "length": 220
    },
    {
        "no_reference": "KUTT.KT/TEL/A4/08/2022/0201",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{207,208,209}",
        "length": 5233
    },
    {
        "no_reference": "KUTT.K/TEL/A3/07/2021/0146",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{165}",
        "length": 1215
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/07/2021/0152",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{171,172,173}",
        "length": 149
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/08/2022/0200",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{210}",
        "length": 230
    },
    {
        "no_reference": "KUTT.ST/TEL/A2/08/2022/0210",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{294}",
        "length": 450
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/05/2021/0117",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{59}",
        "length": 470
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/07/2021/0149",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{166}",
        "length": 350
    },
    {
        "no_reference": "KUTT.KT/TEL/A1/10/2022/0305",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{873}",
        "length": 30
    },
    {
        "no_reference": "KUTT.KT/PWR/A3/07/2021/0154",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203}",
        "length": 3646
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/02/2024/0048",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4744,4745}",
        "length": 3443
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/01/2023/0045",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1985,1986}",
        "length": 371
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0010",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1744}",
        "length": 397
    },
    {
        "no_reference": "KUTT.K/PWR/A2/05/2021/0127",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{62}",
        "length": 400
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/12/2023/0299",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4375}",
        "length": 900
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/08/2022/0221",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{369,370,371,372,373,374,375}",
        "length": 1450
    },
    {
        "no_reference": "KUTT.HT/WTR/A1/05/2021/0187",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{63}",
        "length": 4
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/08/2022/0248",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{558,559}",
        "length": 150
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/06/2021/0133",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{64}",
        "length": 240
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/12/2022/0372",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{1606,1607,1608}",
        "length": 220
    },
    {
        "no_reference": "KUTT.DN/PWR/A3/01/2023/0024",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2363,1834,1835,1836}",
        "length": 42880
    },
    {
        "no_reference": "KUTT.HT/WTR/A2/02/2023/0070",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2086,2087}",
        "length": 165
    },
    {
        "no_reference": "KUTT.ST/WTR/A3/01/2023/0034",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1965}",
        "length": 1794
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/12/2023/0309",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4488,4489}",
        "length": 276
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/12/2022/0376",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1648,1644}",
        "length": 7984
    },
    {
        "no_reference": "KUTT.KM/TEL/R2/04/2023/0146",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2431}",
        "length": 800
    },
    {
        "no_reference": "KUTT.BT/WTR/A3/02/2023/0091",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2198,2199,2200,2201}",
        "length": 1293
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/02/2023/0076",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2123}",
        "length": 6
    },
    {
        "no_reference": "KUTT.KM/WTR/A2/12/2023/0260",
        "utility_provider": {
        "provider_code": 24,
        "name": "Bekalan Air KIPC Sdn Bhd",
        "logo": "24.webp",
        "sort_name": "BAK",
        "logo_url": "https://app.kutt.my/providers/logo/24.webp"
    },
        "id_road_info": "{4368,4392}",
        "length": 677
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/10/2022/0310",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{918,919,916,917,920,921}",
        "length": 502
    },
    {
        "no_reference": "KUTT.KN/TEL/A3/10/2022/0319",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{950,951}",
        "length": 2860
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/10/2022/0318",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{946,947}",
        "length": 400
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/02/2024/0049",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4746,5296}",
        "length": 4238
    },
    {
        "no_reference": "KUTT.ST/TEL/A2/08/2022/0223",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{386}",
        "length": 130
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/08/2022/0228",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{422}",
        "length": 150
    },
    {
        "no_reference": "KUTT.KM/PWR/A3/11/2022/0334",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{954,1222}",
        "length": 1315
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/03/2024/0088",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5212,5398}",
        "length": 230
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/01/2023/0044",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1983}",
        "length": 552
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/02/2024/0051",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4747,4748,5294,5295}",
        "length": 5651
    },
    {
        "no_reference": "KUTT.KM/WTR/A2/02/2023/0053",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2004,2203,2204,2205,2206,2207,2208,2209,2210,2211,2212,2213,2214,2215,2216}",
        "length": 818
    },
    {
        "no_reference": "KUTT.KM/OTR/A1/01/2023/0030",
        "utility_provider": {
        "provider_code": 29,
        "name": "Kertih Terminals Sdn Bhd",
        "logo": "29.webp",
        "sort_name": "KTSB",
        "logo_url": "https://app.kutt.my/providers/logo/29.webp"
    },
        "id_road_info": "{1837}",
        "length": 2
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/08/2022/0204",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{244,2413,2414,2415,2416,2417,2418,2419}",
        "length": 2180
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/04/2023/0147",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2440,2441,2442,2443}",
        "length": 304
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/08/2022/0211",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{295}",
        "length": 150
    },
    {
        "no_reference": "KUTT.MG/WTR/A3/08/2022/0219",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{334,335,336,337,338,339,340,341,342,343,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,366,367,368}",
        "length": 13988
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/02/2023/0072",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2098,2097,2099,2100}",
        "length": 397
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/08/2022/0229",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{423,424}",
        "length": 270
    },
    {
        "no_reference": "KUTT.ST/PWR/A2/11/2022/0321",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{961}",
        "length": 480
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/08/2022/0237",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{488,489,490,491}",
        "length": 1075
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/08/2022/0259",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{512}",
        "length": 315
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/11/2022/0320",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{957,958}",
        "length": 5735
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0021",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1817,1816}",
        "length": 679
    },
    {
        "no_reference": "KUTT.KM/TEL/A1/08/2022/0247",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{554}",
        "length": 35
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/08/2022/0202",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{220,221,223,215,216,217,218,219,224,225,222}",
        "length": 5730
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2022/0366",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1581,1573,1583,1569,1570,1574,1575,1577,1578,1580,1567,1572,1584,1576,1582,1568,1571,1579,1585}",
        "length": 10722
    },
    {
        "no_reference": "KUTT.ST/PWR/A3/01/2024/0002",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4502,4503,4504,4505,4506,4507,4508,4509,4510,4511,4512,4513,4514,4515,4516,4517,4518}",
        "length": 3507
    },
    {
        "no_reference": "KUTT.KM/SWR/A2/10/2022/0307",
        "utility_provider": {
        "provider_code": 11,
        "name": "Indah Water Konsortium Sdn Bhd",
        "logo": "11.webp",
        "sort_name": "IWK",
        "logo_url": "https://app.kutt.my/providers/logo/11.webp"
    },
        "id_road_info": "{879}",
        "length": 20
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/10/2022/0293",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{788,2444,2445,2446,2447,2448,2449,2450,2451,2452,2453}",
        "length": 5390
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0056",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2027}",
        "length": 159
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/11/2023/0258",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4318}",
        "length": 19
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/08/2022/0241",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{494,495,496,496,496,496,496}",
        "length": 440
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/08/2022/0240",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{506,507,508,509,510,511}",
        "length": 1030
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/01/2023/0049",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2003}",
        "length": 400
    },
    {
        "no_reference": "KUTT.KM/WTR/A2/02/2023/0064",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2064,2065}",
        "length": 122
    },
    {
        "no_reference": "KUTT.KM/PWR/A3/11/2023/0245",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3790,3791,3787,3792,3789,3793,3795,3796,3797,3798,3799,3800,3801,3802,3803,3804,3805,3806,3807,3808,3809,3810,3811,3812,3813,3814,3815,3816,3817,3818,3819,3820,3821,3822,3823,3824,3825,3826,3827,3828,3829,3830}",
        "length": 6000
    },
    {
        "no_reference": "KUTT.DN/WTR/A2/12/2023/0307",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4581,4582,4583,4580,4584,4585,4586,4587,4588}",
        "length": 544
    },
    {
        "no_reference": "KUTT.KT/TEL/A1/08/2022/0263",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{609}",
        "length": 60
    },
    {
        "no_reference": "KUTT.KM/PWR/A3/08/2022/0245",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1029,1027,1026,1028,1211}",
        "length": 1220
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/08/2022/0250",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{563}",
        "length": 2900
    },
    {
        "no_reference": "KUTT.DN/WTR/A1/01/2024/0003",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4551}",
        "length": 20
    },
    {
        "no_reference": "KUTT.ST/TEL/A1/08/2022/0255",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{580}",
        "length": 50
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/08/2022/0261",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{585,586,587,588,589,590,591,592,593,594,595,596,597,598,599,600,601,602,603,604,605,606}",
        "length": 5280
    },
    {
        "no_reference": "KUTT.ST/PWR/A2/03/2023/0104",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2254}",
        "length": 250
    },
    {
        "no_reference": "KUTT.KM/TEL/A1/03/2023/0115",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2300,2301}",
        "length": 23
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/08/2022/0243",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{521,522,523,524,525,526}",
        "length": 1160
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/01/2024/0001",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4495,4496,4497,4498,4499,4500}",
        "length": 366
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0055",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2024,2025,2026}",
        "length": 591
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/01/2023/0038",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{1969}",
        "length": 190
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/03/2023/0098",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2225,2348,2349}",
        "length": 200
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/01/2023/0025",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{1667,1838,1839,1840,1841}",
        "length": 677
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0084",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2188,2189}",
        "length": 559
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/10/2022/0288",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{758,757,756,914}",
        "length": 4660
    },
    {
        "no_reference": "KUTT.ST/TEL/A1/08/2022/0257",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{581}",
        "length": 50
    },
    {
        "no_reference": "KUTT.DN/PWR/A1/09/2022/0279",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{637}",
        "length": 123
    },
    {
        "no_reference": "KUTT.ST/PWR/A3/09/2022/0267",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{631,1133,1134,1135,1131,1132}",
        "length": 1714
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/01/2023/0002",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1689,1688}",
        "length": 308
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/02/2024/0052",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4749,5292,5293}",
        "length": 10680
    },
    {
        "no_reference": "KUTT.BT/PWR/A1/09/2022/0274",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{649,650}",
        "length": 40
    },
    {
        "no_reference": "KUTT.KM/WTR/A3/01/2023/0016",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1792,1984}",
        "length": 4200
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/02/2024/0053",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4750}",
        "length": 10200
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/11/2022/0336",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{997,1002,998,999,1000,1001,1003,1004,1008,1006,1007,1010,1009,1013,1011,1012,1017,1005,1015,1016,1018,1019,1020,1021,1022,1023,1024,1014,2424,2426,2428,2430}",
        "length": 4471
    },
    {
        "no_reference": "KUTT.KN/PWR/A3/09/2022/0268",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{616,618,617}",
        "length": 1310
    },
    {
        "no_reference": "KUTT.DN/TEL/A1/09/2022/0302",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{653}",
        "length": 85
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/12/2022/0367",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1586,1587}",
        "length": 660
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0006",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1737,1745,1746,2354}",
        "length": 758
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/09/2022/0271",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{645,646}",
        "length": 4650
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2022/0382",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1673}",
        "length": 332
    },
    {
        "no_reference": "KUTT.KM/PWR/A3/10/2022/0287",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{751,752}",
        "length": 3785
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/09/2022/0294",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{657,658}",
        "length": 2400
    },
    {
        "no_reference": "KUTT.KM/TEL/A4/02/2023/0065",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2047,2048,2292}",
        "length": 10000
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/05/2023/0150",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2478,2498,2499}",
        "length": 640
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/11/2022/0348",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1228,1229,1230}",
        "length": 369
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/08/2022/0242",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{513,514,515,516,517,518,519,520}",
        "length": 1350
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2022/0386",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1691,1692,1693,1690,2352,2353}",
        "length": 943
    },
    {
        "no_reference": "KUTT.KM/PWR/A3/05/2023/0155",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2482,2483,2484,2485,2486,2487,2488,2489,2490,2491,2492,2493,2494,2495,2496}",
        "length": 2143
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/08/2022/0251",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{659}",
        "length": 6700
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/08/2022/0212",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{296}",
        "length": 305
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/10/2022/0295",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{815}",
        "length": 85
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2022/0385",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1675}",
        "length": 384
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/01/2024/0008",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4619}",
        "length": 480
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/05/2023/0149",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2477}",
        "length": 710
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/09/2022/0265",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{627,628,913,912}",
        "length": 16270
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/01/2024/0011",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4632,4630,4631}",
        "length": 165
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/03/2023/0110",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2297}",
        "length": 9
    },
    {
        "no_reference": "KUTT.KM/WTR/A3/01/2023/0031",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1993,1994,1995,1996,1997,1998,1999,2000,2001,2002,1900}",
        "length": 1584
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/08/2022/0230",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{428,429,426,427,430,431,432,647,648}",
        "length": 6130
    },
    {
        "no_reference": "KUTT.KN/TEL/A3/01/2024/0012",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4634,4635,4636,4633}",
        "length": 2899
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/01/2024/0009",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4620}",
        "length": 396
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/02/2023/0060",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2049,2050,2045}",
        "length": 260
    },
    {
        "no_reference": "KUTT.MG/PWR/A3/01/2024/0004",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4552,4627,4628,4629}",
        "length": 1687
    },
    {
        "no_reference": "KUTT.DN/WTR/A3/03/2023/0106",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2255,2256,2257,2258,2259,2260,2261}",
        "length": 2190
    },
    {
        "no_reference": "KUTT.KN/WTR/A3/02/2023/0074",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2105,2107,2108,2109,2110,2111,2106}",
        "length": 1128
    },
    {
        "no_reference": "KUTT.KM/WTR/A2/11/2022/0340",
        "utility_provider": {
        "provider_code": 24,
        "name": "Bekalan Air KIPC Sdn Bhd",
        "logo": "24.webp",
        "sort_name": "BAK",
        "logo_url": "https://app.kutt.my/providers/logo/24.webp"
    },
        "id_road_info": "{839}",
        "length": 980
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/12/2022/0360",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1385}",
        "length": 115
    },
    {
        "no_reference": "KUTT.DN/PWR/A1/12/2022/0378",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1116,1115,1739,1851}",
        "length": 58
    },
    {
        "no_reference": "KUTT.DN/WTR/A1/11/2022/0343",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1119}",
        "length": 1
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0079",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2178,2179,2180}",
        "length": 591
    },
    {
        "no_reference": "KUTT.HT/SWR/A1/05/2023/0151",
        "utility_provider": {
        "provider_code": 11,
        "name": "Indah Water Konsortium Sdn Bhd",
        "logo": "11.webp",
        "sort_name": "IWK",
        "logo_url": "https://app.kutt.my/providers/logo/11.webp"
    },
        "id_road_info": "{2501}",
        "length": 3
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/02/2023/0054",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2019,2036,2037,2038,2039,2040,2041,2042,2043,2044}",
        "length": 459
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/11/2022/0335",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{996}",
        "length": 250
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0028",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1846}",
        "length": 232
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/08/2022/0244",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{542,543,544,545,546}",
        "length": 1150
    },
    {
        "no_reference": "KUTT.KM/WTR/R2/04/2023/0128",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2376}",
        "length": 500
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/10/2022/0296",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{816,817}",
        "length": 1010
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/02/2024/0055",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4752,5291}",
        "length": 6200
    },
    {
        "no_reference": "KUTT.KM/PWR/A3/11/2022/0347",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1231,1232,1233,1377,1378,1379,1380,1381,1382,1383,1384}",
        "length": 1686
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/11/2022/0333",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{995,1171}",
        "length": 1150
    },
    {
        "no_reference": "KUTT.KM/SWR/A2/10/2022/0278",
        "utility_provider": {
        "provider_code": 11,
        "name": "Indah Water Konsortium Sdn Bhd",
        "logo": "11.webp",
        "sort_name": "IWK",
        "logo_url": "https://app.kutt.my/providers/logo/11.webp"
    },
        "id_road_info": "{733}",
        "length": 109
    },
    {
        "no_reference": "KUTT.DN/TEL/A4/05/2023/0153",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2502,2521}",
        "length": 4170
    },
    {
        "no_reference": "KUTT.DN/PWR/A2/03/2024/0071",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5043,5044}",
        "length": 120
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0094",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2217}",
        "length": 554
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/10/2022/0285",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{734,735}",
        "length": 4890
    },
    {
        "no_reference": "KUTT.MG/PWR/A2/11/2022/0339",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1112,1113}",
        "length": 1005
    },
    {
        "no_reference": "KUTT.B/PWR/A3/05/2021/0111",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{39,40,41,42,43,44}",
        "length": 15270
    },
    {
        "no_reference": "KUTT.K/PWR/A1/04/2021/0024",
        "utility_provider": {
        "provider_code": 28,
        "name": "Petronas Chemicals Ammonia Sdn Bhd",
        "logo": "28.webp",
        "sort_name": "PCA",
        "logo_url": "https://app.kutt.my/providers/logo/28.webp"
    },
        "id_road_info": "{16,17,18,19}",
        "length": 19
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/05/2021/0125",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{61}",
        "length": 13700
    },
    {
        "no_reference": "KUTT.B/TEL/A2/05/2021/0118",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{60}",
        "length": 585
    },
    {
        "no_reference": "KUTT.K/PWR/A2/06/2021/0132",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{68}",
        "length": 258
    },
    {
        "no_reference": " KUTT.KT/PWR/A1/05/2021/0131",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{65,66,67}",
        "length": 90
    },
    {
        "no_reference": "KUTT.B/PWR/A2/06/2021/0135",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{87}",
        "length": 200
    },
    {
        "no_reference": "KUTT.K/TEL/A3/07/2021/0145",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{164}",
        "length": 15100
    },
    {
        "no_reference": "KUTT.K/TEL/A3/07/2021/0142",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{144}",
        "length": 1400
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/07/2021/0150",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{167,168,169}",
        "length": 4439
    },
    {
        "no_reference": "KUTT.BT/TEL/A1/12/2022/0355",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1355}",
        "length": 27
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/02/2024/0056",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4753,5289,5290}",
        "length": 5100
    },
    {
        "no_reference": "KUTT.KT/WTR/A1/10/2023/0242",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{3674}",
        "length": 14
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0085",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2190}",
        "length": 229
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/08/2022/0209",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{292,293}",
        "length": 180
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/08/2023/0202",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2701,3100,3101,3102,3103}",
        "length": 230
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/08/2022/0253",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{569,570,571,572,573,574,575,576,577}",
        "length": 1560
    },
    {
        "no_reference": "KUTT.ST/TEL/A2/07/2023/0194",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2699,2700}",
        "length": 212
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/12/2022/0374",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1613,1615,1614}",
        "length": 528
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0080",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2181}",
        "length": 159
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/01/2024/0014",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4644,4643}",
        "length": 932
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/11/2022/0330",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{992}",
        "length": 1370
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/05/2023/0154",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2516}",
        "length": 14
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/10/2022/0297",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{830,831,832,968}",
        "length": 1405
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0034",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4719,4718}",
        "length": 3362
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/04/2023/0125",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2230}",
        "length": 668
    },
    {
        "no_reference": "KUTT.BT/TEL/A2/12/2022/0361",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1548,1549}",
        "length": 270
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/09/2022/0272",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{642}",
        "length": 765
    },
    {
        "no_reference": "KUTT.ST/WTR/A3/01/2023/0035",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1966}",
        "length": 1224
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/08/2022/0236",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{481,482,483,484,485,486,487}",
        "length": 1365
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/11/2022/0341",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1025}",
        "length": 36
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/11/2022/0351",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1241,1242,1243,1244,1238,1239,1240,1245,1246,1247,1248,1249,1250,1251,1252,1253,1254,1255,1256,1257,1258,1259,1260,1261,1262}",
        "length": 5825
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/11/2022/0331",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1884,993}",
        "length": 2200
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/02/2023/0066",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2067,2066}",
        "length": 3250
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0009",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1741,1742,1743}",
        "length": 532
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2022/0359",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1452,1453,1454,1455,1457,1458,1459,1460,1461,1462,1463,1464,1465,1466,1467,1468,1469,1470,1471,1472,1473,1474,1475,1476,1477,1478,1479,1480,1481,1482,1483,1484,1485,1486,1487,1488,1456,1489,1490,1491,1492,1493,1494,1495,1496,1497,1498,1499,1500,1501,1502,1503,1504}",
        "length": 17060
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0082",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2187}",
        "length": 234
    },
    {
        "no_reference": "KUTT.KM/PWR/R2/04/2023/0137",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2388,2389}",
        "length": 102
    },
    {
        "no_reference": "KUTT.KT/WTR/A2/02/2023/0059",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2035}",
        "length": 525
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0005",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1735,1736}",
        "length": 945
    },
    {
        "no_reference": "KUTT.KM/PWR/R1/04/2023/0138",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2390}",
        "length": 55
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/11/2022/0346",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1219,1215,1214,1216,1217,1220,1218}",
        "length": 1198
    },
    {
        "no_reference": "KUTT.MG/WTR/A2/03/2023/0102",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2244,2240,2243}",
        "length": 637
    },
    {
        "no_reference": "KUTT.KN/TEL/A4/04/2023/0148",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2368,2543}",
        "length": 5803
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/02/2024/0018",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4658}",
        "length": 22
    },
    {
        "no_reference": "KUTT.ST/PWR/A3/01/2023/0036",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1872,1873,1874,1875,1876,1877,1878,1879,1880,1881,1882}",
        "length": 7370
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/02/2024/0059",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4768,4766,4767}",
        "length": 146
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/07/2023/0182",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2592,2682,2683}",
        "length": 788
    },
    {
        "no_reference": "KUTT.MG/TEL/A2/02/2024/0058",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{4756}",
        "length": 150
    },
    {
        "no_reference": "KUTT.KM/GAS/A2/05/2023/0159",
        "utility_provider": {
        "provider_code": 27,
        "name": "Ace Gases Sdn Bhd",
        "logo": "27.webp",
        "sort_name": "AGSB",
        "logo_url": "https://app.kutt.my/providers/logo/27.webp"
    },
        "id_road_info": "{2530}",
        "length": 350
    },
    {
        "no_reference": "KUTT.HT/TEL/A2/11/2022/0327",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{988}",
        "length": 322
    },
    {
        "no_reference": "KUTT.KM/GAS/A2/05/2023/0158",
        "utility_provider": {
        "provider_code": 8,
        "name": "Gas Malaysia Distribution Sdn Bhd",
        "logo": "8.webp",
        "sort_name": "GMDSB",
        "logo_url": "https://app.kutt.my/providers/logo/8.webp"
    },
        "id_road_info": "{2528,2529}",
        "length": 600
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/03/2024/0064",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{5030,5031,5032,5033,5034,5035,5036,5037,5038,5039,5040,4778}",
        "length": 2750
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/03/2023/0100",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2228,2229}",
        "length": 7390
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/10/2022/0291",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{782,783,781,784,785,786,1166,1167,1168,1169}",
        "length": 237
    },
    {
        "no_reference": "KUTT.KT/PWR/A1/05/2023/0157",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2526,2527}",
        "length": 15
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/05/2023/0152",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2505,2506,2507,2538}",
        "length": 195
    },
    {
        "no_reference": "KUTT.MG/TEL/A2/01/2024/0016",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4649,4650,4648}",
        "length": 417
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/02/2023/0051",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2006,2007,2008,2232,2233}",
        "length": 605
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/03/2024/0082",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5363,5364,5365,5366,5367,5368}",
        "length": 5988
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0007",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1738,2235}",
        "length": 943
    },
    {
        "no_reference": "KUTT.MG/WTR/A3/01/2023/0039",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1970,1976,1971,1972,1973,1977,1978,1974,1975}",
        "length": 3529
    },
    {
        "no_reference": "KUTT.HT/WTR/A1/03/2023/0103",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2247}",
        "length": 10
    },
    {
        "no_reference": "KUTT.BT/WTR/A3/01/2023/0026",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1842,1843,1844}",
        "length": 1160
    },
    {
        "no_reference": "KUTT.DN/TEL/A4/06/2023/0165",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2548}",
        "length": 800
    },
    {
        "no_reference": "KUTT.KT/WTR/A3/08/2022/0217",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{307,308,309,310,311,312,315,319,320,796,797,798}",
        "length": 7532
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/01/2024/0006",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4615}",
        "length": 327
    },
    {
        "no_reference": "KUTT.KM/WTR/A2/03/2023/0108",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2269,2270,2271}",
        "length": 801
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/11/2022/0328",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2550,1650,990}",
        "length": 3640
    },
    {
        "no_reference": "KUTT.KM/PWR/R2/04/2023/0139",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2391,2392,2393,2394}",
        "length": 285
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/08/2023/0205",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{2704,3097,3098}",
        "length": 165
    },
    {
        "no_reference": "KUTT.KM/TEL/A4/04/2023/0142",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2370}",
        "length": 1895
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/03/2023/0105",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{2249,2250,2251,2252}",
        "length": 843
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0086",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2191,2192}",
        "length": 685
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/04/2023/0141",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2397,2398,2406,2407}",
        "length": 5488
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/02/2023/0077",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2172}",
        "length": 6
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/10/2022/0289",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{759}",
        "length": 6790
    },
    {
        "no_reference": "KUTT.KM/WTR/R1/04/2023/0129",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2378}",
        "length": 90
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/03/2023/0113",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2298}",
        "length": 80
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/03/2023/0118",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{2320,2321,2322,2323,2324,2325}",
        "length": 2440
    },
    {
        "no_reference": "KUTT.MG/PWR/A3/03/2024/0085",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5383,5384,5385,5386}",
        "length": 648
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/01/2024/0017",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4652,4653,4651,4669}",
        "length": 1660
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/03/2023/0114",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2299}",
        "length": 8
    },
    {
        "no_reference": "KUTT.ST/TEL/A2/03/2023/0116",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2305,2306,2307}",
        "length": 835
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0083",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2184,2185,2186}",
        "length": 647
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/10/2022/0304",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{969,970,971,972,973}",
        "length": 3725
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2022/0381",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1671}",
        "length": 184
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/03/2023/0120",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2346,2347,2345}",
        "length": 807
    },
    {
        "no_reference": "KUTT.KM/WTR/R2/04/2023/0130",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2379}",
        "length": 800
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0088",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2194,2195,2356}",
        "length": 715
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0018",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1795}",
        "length": 148
    },
    {
        "no_reference": "KUTT.KM/PWR/R2/04/2023/0140",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2395}",
        "length": 990
    },
    {
        "no_reference": "KUTT.KM/TEL/R2/04/2023/0126",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2372}",
        "length": 800
    },
    {
        "no_reference": "KUTT.KM/TEL/R1/04/2023/0127",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{2375}",
        "length": 70
    },
    {
        "no_reference": "KUTT.HT/TEL/A2/04/2023/0134",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2383}",
        "length": 200
    },
    {
        "no_reference": "KUTT.BT/TEL/A2/08/2022/0208",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{291,722}",
        "length": 200
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0022",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1818}",
        "length": 550
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0081",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2182,2183}",
        "length": 657
    },
    {
        "no_reference": "KUTT.BT/WTR/A3/01/2023/0019",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1796}",
        "length": 1681
    },
    {
        "no_reference": "KUTT.BT/PWR/A1/03/2024/0078",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5179,5180,5181}",
        "length": 75
    },
    {
        "no_reference": "KUTT.KM/PWR/R2/04/2023/0136",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2387}",
        "length": 930
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/01/2023/0047",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1988}",
        "length": 245
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/10/2023/0240",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3675,3679,3680}",
        "length": 148
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/01/2023/0046",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1987}",
        "length": 316
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/01/2023/0048",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1990,1991,1992}",
        "length": 870
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/01/2023/0014",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1787,1788,1789}",
        "length": 178
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0020",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1812}",
        "length": 386
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2022/0380",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1670}",
        "length": 158
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/11/2022/0325",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{977}",
        "length": 7950
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/10/2022/0300",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{850,849,851,852,854,855,856,857,858,865,860,861,862,863,864,866,867,868,853,859,2060,2061,2062,2063}",
        "length": 9103
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/08/2022/0203",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{227,226,228,230,232,229,231,234,233,237,235,236,238,239,240}",
        "length": 8205
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/02/2023/0052",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2009,2010,2011}",
        "length": 698
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/02/2023/0050",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2005}",
        "length": 290
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0057",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2028,2029,2030,2355}",
        "length": 666
    },
    {
        "no_reference": "KUTT.ST/TEL/A2/10/2022/0312",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{926,927}",
        "length": 170
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2022/0389",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1668}",
        "length": 430
    },
    {
        "no_reference": "KUTT.ST/TEL/A1/10/2022/0303",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{822,870}",
        "length": 35
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/09/2022/0283",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{723,724,725}",
        "length": 3000
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2022/0354",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1694,1695}",
        "length": 467
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0027",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1845}",
        "length": 776
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/09/2022/0282",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{721}",
        "length": 2390
    },
    {
        "no_reference": "KUTT.BT/TEL/A2/01/2023/0041",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{1980}",
        "length": 450
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2022/0384",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1672}",
        "length": 161
    },
    {
        "no_reference": "KUTT.BT/PWR/A1/10/2022/0308",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{882,985}",
        "length": 30
    },
    {
        "no_reference": "KUTT.ST/TEL/A1/08/2022/0256",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{579}",
        "length": 50
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0029",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1847,1848}",
        "length": 484
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2022/0388",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1669}",
        "length": 696
    },
    {
        "no_reference": "KUTT.BT/WTR/A1/11/2022/0326",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{987}",
        "length": 20
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0017",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1793,1794}",
        "length": 350
    },
    {
        "no_reference": "KUTT.BT/WTR/A1/01/2023/0008",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1740}",
        "length": 140
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0015",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1782,1783,1781,2351}",
        "length": 492
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/08/2022/0205",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{260,1901,1902,1918,1903,1904,1905,1906,1907,1908,1909,1910,1911,1912,1913,1914,1915,1916,1917,1919,1920,1921,1922,1923,1924,1925,1926,1927,1928,1929,1930,1931,1932,1933,1934,1935,1936,1937,1938,1939,1940,1941,1942,1943,1944,1945,1946}",
        "length": 10008
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0011",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1747,1748}",
        "length": 478
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/08/2022/0216",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{302,301,303,304,305,709,710,711}",
        "length": 7475
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0089",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2196}",
        "length": 287
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0092",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2202}",
        "length": 250
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0095",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2218,2219,2220,2221}",
        "length": 776
    },
    {
        "no_reference": "KUTT.ST/PWR/A3/11/2022/0350",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1237,1856,1852,1853,1854,1855,1857,1858,1859,1860,2272}",
        "length": 18120
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0087",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2193}",
        "length": 662
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/02/2023/0090",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2197,2350}",
        "length": 169
    },
    {
        "no_reference": "KUTT.BT/WTR/A1/01/2023/0040",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1979}",
        "length": 50
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/12/2022/0375",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1627,1626,1630,1628,1631,1629,1632,1633,1634,1635}",
        "length": 3620
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/01/2023/0043",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1982}",
        "length": 296
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/01/2023/0012",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{1749}",
        "length": 230
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/08/2022/0225",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1388,1391,1390,1393,1394,1396,1397,1398,1399,1387,1392,1395,1400,2366}",
        "length": 1723
    },
    {
        "no_reference": "KUTT.ST/WTR/A2/06/2023/0173",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2573}",
        "length": 940
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/07/2023/0181",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2591}",
        "length": 4
    },
    {
        "no_reference": "KUTT.KM/WTR/R1/08/2023/0204",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2703,3099}",
        "length": 55
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/09/2023/0222",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{3137,3138}",
        "length": 14
    },
    {
        "no_reference": "KUTT.DN/PWR/A1/06/2023/0168",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2556}",
        "length": 80
    },
    {
        "no_reference": "KUTT.HT/WTR/A2/06/2023/0172",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2584,2572}",
        "length": 350
    },
    {
        "no_reference": "KUTT.DN/WTR/R1/07/2023/0177",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2583,1235}",
        "length": 55
    },
    {
        "no_reference": "KUTT.KM/TEL/A1/08/2023/0207",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{2707}",
        "length": 90
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/12/2022/0373",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1609,1610,1611,1612}",
        "length": 7693
    },
    {
        "no_reference": "KUTT.DN/PWR/A2/07/2023/0192",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2690,3037,3038,3039,3040,3041,3042}",
        "length": 546
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/12/2022/0363",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1556,1557,1555}",
        "length": 209
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/12/2022/0353",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1266,1561}",
        "length": 127
    },
    {
        "no_reference": "KUTT.DN/TEL/A1/03/2023/0099",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2226}",
        "length": 45
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/02/2024/0036",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4722,4723,5395,5396}",
        "length": 5624
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/11/2022/0332",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1957,1958,1959}",
        "length": 410
    },
    {
        "no_reference": "KUTT.HT/WTR/A2/02/2023/0069",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2085}",
        "length": 830
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/01/2024/0010",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4613,4614}",
        "length": 1377
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/11/2022/0342",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1136,1137,1138,1139,1140,1141,1142,1143,1144}",
        "length": 1711
    },
    {
        "no_reference": "KUTT.DN/TEL/A2/12/2022/0364",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1560,1558,1559}",
        "length": 835
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/01/2023/0042",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{1950,1951,1952,1953,1954,1955,1956}",
        "length": 5710
    },
    {
        "no_reference": "KUTT.MG/TEL/A2/04/2023/0145",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2412,2545}",
        "length": 280
    },
    {
        "no_reference": "KUTT.DN/TEL/R2/07/2023/0176",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{1263,2580,3057}",
        "length": 891
    },
    {
        "no_reference": "KUTT.DN/PWR/R2/07/2023/0175",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1551,2582}",
        "length": 900
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/07/2023/0185",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2611,2612,2614,2613}",
        "length": 505
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/01/2023/0032",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{1947,1948,1949}",
        "length": 17278
    },
    {
        "no_reference": "KUTT.MG/PWR/A2/08/2022/0213",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{287,288,289}",
        "length": 210
    },
    {
        "no_reference": "KUTT.DN/TEL/R2/07/2023/0174",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{2410}",
        "length": 291
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/10/2022/0276",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{2479,2480,2481}",
        "length": 13380
    },
    {
        "no_reference": "KUTT.ST/WTR/A3/02/2024/0019",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4755,4664,4661}",
        "length": 888
    },
    {
        "no_reference": "KUTT.MG/WTR/A2/06/2023/0164",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2546,2547}",
        "length": 202
    },
    {
        "no_reference": "KUTT.KT/PWR/A3/04/2024/0090",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5408,5397}",
        "length": 2430
    },
    {
        "no_reference": "KUTT.KN/PWR/A4/02/2024/0023",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4696,4697,4699,4698}",
        "length": 550
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/02/2024/0021",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4902,4692,4693,4691}",
        "length": 275
    },
    {
        "no_reference": "KUTT.HT/PWR/A2/02/2024/0022",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4679}",
        "length": 12
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0024",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4703,4702}",
        "length": 8843
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/03/2024/0066",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{5041,4781,4782,5042}",
        "length": 550
    },
    {
        "no_reference": "KUTT.ST/PWR/A2/09/2023/0224",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3144}",
        "length": 700
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/12/2022/0357",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{1439,1440,1437,1438}",
        "length": 700
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0025",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4705,4704,5360}",
        "length": 3220
    },
    {
        "no_reference": "KUTT.MG/PWR/A2/12/2022/0365",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1564,1565,1562,1563}",
        "length": 457
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/09/2023/0225",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3145,3164}",
        "length": 2
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0026",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4706}",
        "length": 5950
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/11/2023/0256",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4306,4307,4308,4309}",
        "length": 170
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/04/2023/0144",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{2401,2402,2403,2535}",
        "length": 3890
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/02/2024/0020",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4674,4675}",
        "length": 210
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/04/2024/0091",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{5407}",
        "length": 1000
    },
    {
        "no_reference": "KUTT.KN/PWR/A1/03/2024/0086",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5392,5391}",
        "length": 45
    },
    {
        "no_reference": "KUTT.HT/PWR/A3/02/2023/0062",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2057,2059,2058,2056,2055}",
        "length": 1970
    },
    {
        "no_reference": "KUTT.KM/TEL/A1/09/2023/0228",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{3168,3169}",
        "length": 90
    },
    {
        "no_reference": "KUTT.KM/PWR/A3/09/2023/0230",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3183,3184,3175,3176,3177,3178,3179,3180,3181,3182,3185}",
        "length": 1010
    },
    {
        "no_reference": "KUTT.DN/PWR/A1/07/2023/0189",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2679,3032}",
        "length": 90
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/10/2023/0236",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3666}",
        "length": 60
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/03/2024/0067",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4787,4788,4789,4790,4791,4792,4793,4794,4795,4796,4797,4798,4814,4815,4816,4819,4799,4800,4801,4802,4803,4804,4805,4806,4807,4808,4809,4810,4811,4812,4813,4817,4821,4820,4818,4822,4823,4824,4825,4826,4827,4828,4829,4830,4831,4832,4833,4834,4835,4836,4837,4838,4839,4840,4841,4842,4843,4844,4845,4846,4847,4848,4849,4865,4850,4851,4852,4854,4855,4856,4857,4858,4859,4860,4861,4862,4863,4864,4866,4867,4868,4869,4870,4871,4872,4873,4874,4875,4876,4877,4878,4879,4880,4881,4882,4883,4884,4885,4886,4887,4888,4853,4901,4889,4890,4891,4892,4893,4894,4895,4896,5369}",
        "length": 3712
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/08/2022/0222",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{376,377,378,379,380,381,382,383,384,385}",
        "length": 1680
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/03/2023/0101",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2238,2237}",
        "length": 465
    },
    {
        "no_reference": "KUTT.KT/WTR/A2/02/2023/0058",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2031,2032,2033,2034}",
        "length": 357
    },
    {
        "no_reference": "KUTT.KT/PWR/A1/01/2023/0037",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1968,1967}",
        "length": 45
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/06/2023/0161",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2497,2566,2567,2568}",
        "length": 82
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0028",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4709}",
        "length": 6756
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/03/2024/0081",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5123,5125,5126,5127,5128,5122,5124,5120,5121,5119}",
        "length": 662
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/02/2024/0043",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4734,4735}",
        "length": 5612
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/12/2023/0298",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4374}",
        "length": 3700
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/11/2023/0247",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4280,4281,4282,4283,4284,4285,4279}",
        "length": 681
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/03/2024/0073",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5115,5116}",
        "length": 1875
    },
    {
        "no_reference": "KUTT.KT/PWR/A3/08/2023/0209",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2987,2989,2990,2980,2978,2979,2981,3011,2982,3003,2984,2985,2991,2992,2993,2994,2995,2996,2997,2999,2976,3000,3001,3002,2998,3004,3005,3006,3007,3008,3009,2983,3010,3012,3013,3014,3015,3016,3017,3020,3018,3019,3021,2986,2988,2977,3022,3024,3025,3026,3023,3832}",
        "length": 10601
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/10/2022/0292",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{776,777,778,775,779,780}",
        "length": 622
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/03/2024/0068",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{5029}",
        "length": 9
    },
    {
        "no_reference": "KUTT.KN/WTR/A1/03/2024/0063",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4903}",
        "length": 15
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0029",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4710}",
        "length": 4763
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/10/2023/0235",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3667}",
        "length": 30
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/09/2022/0284",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{732,980,2104}",
        "length": 750
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/07/2023/0186",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2617,3106,3107,3108,3109,3110,3111}",
        "length": 995
    },
    {
        "no_reference": "KUTT.HT/PWR/A3/01/2023/0013",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1760,1755,1761,1757,1758,1759,1750,1767,1768,1769,1770,1771,1772,1774,1775,1776,1764,1773,1777,1778,1779,1780,1765,1766,2341,2342,2343,1754}",
        "length": 17010
    },
    {
        "no_reference": "KUTT.KM/TEL/R2/08/2023/0203",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{3091,2702,3090,3092}",
        "length": 312
    },
    {
        "no_reference": "KUTT.KM/TEL/A1/09/2023/0231",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{3186}",
        "length": 20
    },
    {
        "no_reference": "KUTT.KM/TEL/R2/06/2023/0167",
        "utility_provider": {
        "provider_code": 19,
        "name": "Fiberail Sdn Bhd",
        "logo": "19.webp",
        "sort_name": "FRSB",
        "logo_url": "https://app.kutt.my/providers/logo/19.webp"
    },
        "id_road_info": "{2515}",
        "length": 5
    },
    {
        "no_reference": "KUTT.HT/TEL/A2/03/2024/0089",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5403,5404,5405,5406}",
        "length": 403
    },
    {
        "no_reference": "KUTT.KM/WTR/A3/06/2023/0171",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2551,2552,2553}",
        "length": 1150
    },
    {
        "no_reference": "KUTT.MG/PWR/A1/07/2023/0193",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2694}",
        "length": 20
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/09/2022/0280",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{708,794,795}",
        "length": 220
    },
    {
        "no_reference": "KUTT.ST/PWR/A3/07/2023/0188",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2666,2667,2668,2669,2670,2671,2672,2673,2674,2675}",
        "length": 4071
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0030",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4711,5358,5359}",
        "length": 4763
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/09/2022/0275",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2046,979,652,2103}",
        "length": 490
    },
    {
        "no_reference": "KUTT.KT/WTR/A1/08/2022/0260",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{584}",
        "length": 5
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/08/2022/0258",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{583}",
        "length": 100
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/08/2022/0249",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{560,561,562,562}",
        "length": 590
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/08/2022/0233",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{457}",
        "length": 80
    },
    {
        "no_reference": "KUTT.KN/TEL/A1/08/2022/0232",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{456}",
        "length": 80
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/08/2022/0220",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{331,332,333}",
        "length": 370
    },
    {
        "no_reference": "KUTT.KN/WTR/A3/08/2022/0218",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{325,326,328,329,330}",
        "length": 4100
    },
    {
        "no_reference": "KUTT.KN/PWR/A1/02/2023/0078",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2175,2176,2177}",
        "length": 85
    },
    {
        "no_reference": "KUTT.KN/TEL/A3/08/2022/0214",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{297,298,638,639,641,915}",
        "length": 3681
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/08/2023/0201",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2962,2963,2960,2961}",
        "length": 210
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/03/2024/0074",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5130,5131,5132,5134,5135,5136,5137,5139,5140,5142,5143,5141,5144,5145,5146,5138,5133}",
        "length": 3154
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0032",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4714,4715,5357}",
        "length": 9890
    },
    {
        "no_reference": "KUTT.KT/PWR/A3/08/2023/0213",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3058,3060,3061,3063,3064,3065,3059,3062,4289,4290,4291,4292,4293}",
        "length": 3517
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/11/2022/0323",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{966,967}",
        "length": 5060
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/08/2023/0199",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2722,2723,2724}",
        "length": 32
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/05/2023/0160",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2532,2533,2534,2531}",
        "length": 135
    },
    {
        "no_reference": "KUTT.KM/TEL/A4/06/2023/0169",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2560,2561,2563}",
        "length": 6000
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/03/2023/0097",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2224,2360}",
        "length": 300
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/11/2022/0324",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{975,976}",
        "length": 7620
    },
    {
        "no_reference": "KUTT.ST/PWR/A3/09/2023/0220",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3112,3113,3114,3115,3116,3117,3118,3119,3120,3121,3122,3123}",
        "length": 2053
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/11/2022/0345",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1813,1814,1815,1198,1234,1202,1203,1204,1205,1206,1207,1208,1210,1209,1200,1199,1201,2513,2514}",
        "length": 11425
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/08/2023/0198",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2721,2718,2719,2720}",
        "length": 635
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0270",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4339,4574}",
        "length": 4600
    },
    {
        "no_reference": "KUTT.KN/TEL/A4/04/2024/0097",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{5431,5447,5448,5449,5450,5451,5452,5453}",
        "length": 310
    },
    {
        "no_reference": "KUTT.KN/TEL/A3/04/2023/0143",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{2399,2400}",
        "length": 6486
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/08/2022/0224",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{398,400,399,401,415,394,390,397,403,405,406,407,408,392,409,388,416,3027,3028,3029,3030,3031}",
        "length": 6279
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/08/2023/0211",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{3034,3035}",
        "length": 85
    },
    {
        "no_reference": "KUTT.BT/TEL/A1/08/2023/0210",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{3033}",
        "length": 30
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/08/2022/0206",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{267,268,269,270,271,272,273,278,276,277,281,282,262,275,263,261,265,266,274,280,279,264,2090,2091,2092}",
        "length": 12010
    },
    {
        "no_reference": "KUTT.MG/TEL/A2/08/2022/0234",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{461,462,463,464}",
        "length": 750
    },
    {
        "no_reference": "KUTT.KT/PWR/A1/07/2023/0191",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2689,2688}",
        "length": 40
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/08/2022/0238",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{492,493}",
        "length": 1010
    },
    {
        "no_reference": "KUTT.ST/PWR/A3/09/2023/0221",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3124,3125,3126,3127,3128,3129,3130,3131,3132,3133,3134,3135,3136}",
        "length": 3725
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0272",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4519,4341}",
        "length": 6370
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/08/2023/0214",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3067,3068,3069}",
        "length": 112
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/08/2023/0217",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3087,3088,3089}",
        "length": 629
    },
    {
        "no_reference": "KUTT.MG/PWR/A2/08/2023/0216",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3078,3079,3080,3081,3082,3083,3084,3085,3086}",
        "length": 528
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/08/2023/0219",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{3104,3105}",
        "length": 3995
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/08/2023/0218",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{3096}",
        "length": 30
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0273",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4342}",
        "length": 5527
    },
    {
        "no_reference": "KUTT.KT/PWR/A3/02/2023/0067",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2069,2070,2071,2072,2073,2074,2076,2077,2078,2079,2082,2068,2080,2083,2081,2075}",
        "length": 1420
    },
    {
        "no_reference": "KUTT.ST/PWR/A2/06/2023/0163",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2542,2540,2541,3066}",
        "length": 330
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0035",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4721,4720,5355}",
        "length": 6920
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/08/2023/0206",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2706}",
        "length": 130
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/04/2024/0093",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5412,5413,5414,5415,5416,5417,5418,5419,5420,5421,5422,5423,5425,5426,5427,5428,5429,5424}",
        "length": 12385
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/11/2023/0244",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{3781,3782}",
        "length": 5627
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/08/2022/0227",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1505,1506,1507,1508,1509,1510,1511,1512,1513,1514,1515,1516,1517,1518,1519,1520,1521,1522,1523,1524,1525,1526,1527,1528,1529,1530,1531,1532,1533,1534,1535,1536,1537,1538,1539,1540,1541,1542,1543,1544,1545,1546,1547}",
        "length": 11625
    },
    {
        "no_reference": "KUTT.DN/PWR/A2/11/2023/0248",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4286,4287,4288}",
        "length": 895
    },
    {
        "no_reference": "KUTT.HT/WTR/A1/07/2023/0178",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2586}",
        "length": 8
    },
    {
        "no_reference": "KUTT.KM/WTR/R1/08/2023/0197",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2717}",
        "length": 90
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/10/2022/0306",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{871,872}",
        "length": 5210
    },
    {
        "no_reference": "KUTT.MG/PWR/A1/08/2022/0215",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{299,300}",
        "length": 60
    },
    {
        "no_reference": "KUTT.MG/TEL/A2/08/2022/0231",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{454,455}",
        "length": 130
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/08/2022/0239",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{500,501,502,503,504,505}",
        "length": 1120
    },
    {
        "no_reference": "KUTT.MG/WTR/R2/09/2022/0264",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{619}",
        "length": 36
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/09/2022/0273",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{644,643,983,982}",
        "length": 2675
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/09/2022/0281",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{718}",
        "length": 1550
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/10/2022/0286",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{747,750,748,749}",
        "length": 9020
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/10/2022/0301",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1850,874,875,960,876}",
        "length": 5195
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/10/2022/0315",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1849,928,2227}",
        "length": 4406
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/10/2022/0317",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{945}",
        "length": 1755
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/11/2022/0337",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1036,1038,1042,1044,1052,1053,1055,1057,1059,1061,1063,1069,1071,1073,1075,1083,1085,1087,1089,1081,1095,1091,1093,1097,1099,1101,1103,1105,1106,1107,1108,1109,1065,1079,1040,1046,1186,1187,1188,1189,1190,1191,1192,1193,1048,1050,1067,1077,1197,1195,1196,1194}",
        "length": 10926
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/12/2022/0356",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1356,1357,1358,1360,1361,1362,1363,1364,1365,1366,1367,1368,1369,1370,1371,1372,1373,1374,1375,1376,1359,1550}",
        "length": 5716
    },
    {
        "no_reference": "KUTT.MG/PWR/A2/01/2023/0004",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1883,1588,1589,1590}",
        "length": 465
    },
    {
        "no_reference": "KUTT.MG/PWR/A3/01/2023/0023",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1820,1827,1819,1831,1826,1823,1832,1821,1824,1825,1828,1829,1830,1833,1822,2023}",
        "length": 15948
    },
    {
        "no_reference": "KUTT.MG/PWR/A2/01/2023/0033",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1961,1962,1963}",
        "length": 515
    },
    {
        "no_reference": "KUTT.MG/WTR/A2/02/2023/0093",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2173,2174,2234}",
        "length": 558
    },
    {
        "no_reference": "KUTT.MG/TEL/A2/03/2023/0109",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2276,2277,2278,2273,2275,2274,2340}",
        "length": 938
    },
    {
        "no_reference": "KUTT.HT/WTR/A3/03/2023/0111",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2364,2236,2303,2396}",
        "length": 3804
    },
    {
        "no_reference": "KUTT.HT/TEL/A2/02/2023/0073",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2102}",
        "length": 82
    },
    {
        "no_reference": "KUTT.HT/TEL/A1/12/2022/0377",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1649}",
        "length": 43
    },
    {
        "no_reference": "KUTT.HT/PWR/A2/03/2023/0112",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2294,2296,2295}",
        "length": 443
    },
    {
        "no_reference": "KUTT.HT/WTR/A2/02/2023/0068",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2084}",
        "length": 350
    },
    {
        "no_reference": "KUTT.HT/TEL/A1/11/2022/0329",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{991}",
        "length": 30
    },
    {
        "no_reference": "KUTT.MG/TEL/A1/02/2023/0061",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2579}",
        "length": 90
    },
    {
        "no_reference": "KUTT.MG/PWR/A2/05/2023/0162",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2539}",
        "length": 400
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/10/2022/0290",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{764,760,765,762,763,761,766,1146,1145,1152,1147,1173,1174,1175,1176,1153,1154,1155,1156,1157,1158,1177,1178,1179,1180,1181,1182,1183,1184,1185,1160,1161,1162,1163,1164,1165,1159,1149,1150,1151,1148}",
        "length": 9766
    },
    {
        "no_reference": "KUTT.HT/TEL/A1/08/2022/0254",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{578}",
        "length": 30
    },
    {
        "no_reference": "KUTT.HT/TEL/A2/08/2022/0246",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{550,551,552}",
        "length": 490
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2022/0371",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1601,1602}",
        "length": 1518
    },
    {
        "no_reference": "KUTT.HT/TEL/A2/08/2022/0207",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{290}",
        "length": 550
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/08/2022/0226",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1401,1406,1402,1403,1404,1431,1432,1433,1413,1414,1434,1435,1418,1419,1420,1421,1422,1423,1424,1425,1426,1427,1428,1429,1436,1415,1407,1408,1409,1410,1411,1412,1416,1417,1430,1405}",
        "length": 9733
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/02/2024/0062",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4690,4777}",
        "length": 515
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/10/2022/0298",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{835,834,833}",
        "length": 990
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/01/2024/0005",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4618,4617}",
        "length": 434
    },
    {
        "no_reference": "KUTT.KT/SWR/A1/03/2024/0069",
        "utility_provider": {
        "provider_code": 11,
        "name": "Indah Water Konsortium Sdn Bhd",
        "logo": "11.webp",
        "sort_name": "IWK",
        "logo_url": "https://app.kutt.my/providers/logo/11.webp"
    },
        "id_road_info": "{5045}",
        "length": 7
    },
    {
        "no_reference": "KUTT.KM/TEL/A1/03/2024/0079",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{5303}",
        "length": 5
    },
    {
        "no_reference": "KUTT.ST/PWR/A3/07/2023/0184",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2600,2601,2602,2603,2604,2605,2606}",
        "length": 4925
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/08/2022/0235",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{465,466,467,468,470,471,472,473,474,475,476,477,478,469,743}",
        "length": 682
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/07/2023/0195",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2695,2696,2697}",
        "length": 60
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/10/2022/0277",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{741,740}",
        "length": 3550
    },
    {
        "no_reference": "KUTT.MG/PWR/A1/04/2023/0135",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2384,2385,2386}",
        "length": 55
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/01/2024/0007",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4616}",
        "length": 195
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/02/2024/0037",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4724,4725,5354}",
        "length": 5713
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/11/2023/0251",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4295}",
        "length": 8
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/09/2023/0229",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{3171}",
        "length": 6
    },
    {
        "no_reference": "KUTT.HT/TEL/A1/12/2022/0370",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{1595,1594,1596,1597,1598}",
        "length": 491
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/09/2023/0232",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{2729,2730,2731,2732,2733,2734,2735,2736,2737,2738,2739,2740,2741,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2759,2760,2761,2762,2763,2764,2765,2766,2767,2768,2769,2770,2771,2772,2773,2774,2775,2776,2777,2778,2779,2780,2781,2783,2784,2785,2786,2787,2788,2789,2790,2791,2792,2793,2794,2795,2796,2797,2798,2799,2800,2801,2802,2803,2804,2805}",
        "length": 17710
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0277",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4520,4346}",
        "length": 6800
    },
    {
        "no_reference": "KUTT.DN/WTR/A3/12/2023/0306",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4553,4554,4555,4559,4560,4561,4556,4557,4558,4562,4563,4564,4571,4572,4573,4565,4566,4567,4568,4569,4570}",
        "length": 1151
    },
    {
        "no_reference": "KUTT.KN/WTR/A1/07/2023/0187",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2575}",
        "length": 6
    },
    {
        "no_reference": "KUTT.KT/WTR/A3/02/2023/0075",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2119,2118,2120}",
        "length": 1402
    },
    {
        "no_reference": "KUTT.KM/TEL/A4/10/2023/0239",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{3672,3671,3673}",
        "length": 25000
    },
    {
        "no_reference": "KUTT.KM/OTR/A3/08/2023/0200",
        "utility_provider": {
        "provider_code": 30,
        "name": "Eastern Steel Sdn Bhd",
        "logo": "30.webp",
        "sort_name": "ESSB",
        "logo_url": "https://app.kutt.my/providers/logo/30.webp"
    },
        "id_road_info": "{2728}",
        "length": 8100
    },
    {
        "no_reference": "KUTT.MG/PWR/A1/09/2023/0233",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3493,3492}",
        "length": 55
    },
    {
        "no_reference": "KUTT.KM/PWR/R2/08/2023/0196",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2710}",
        "length": 180
    },
    {
        "no_reference": "KUTT.KM/GAS/A1/04/2024/0094",
        "utility_provider": {
        "provider_code": 9,
        "name": "Petroliam Nasional Berhad",
        "logo": "9.webp",
        "sort_name": "PNB",
        "logo_url": "https://app.kutt.my/providers/logo/9.webp"
    },
        "id_road_info": "{5432}",
        "length": 25
    },
    {
        "no_reference": "KUTT.BT/PWR/A3/08/2023/0212",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3682,3683,3684,3685,3758,3760,3762,3764,3766,3768,3759,3761,3763,3765,3767,3769}",
        "length": 9885
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/02/2024/0038",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4726}",
        "length": 2023
    },
    {
        "no_reference": "KUTT.KM/SWR/A3/12/2023/0000",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4321}",
        "length": 1500
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2023/0262",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4534,4535,4536,4537,4538,4539,4540}",
        "length": 569
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0278",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4521,4347}",
        "length": 5300
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/02/2024/0039",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4727}",
        "length": 2328
    },
    {
        "no_reference": "KUTT.KT/WTR/A2/03/2023/0122",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2357}",
        "length": 543
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0279",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4525,4589,4348}",
        "length": 4410
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0287",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4359,4684,4685}",
        "length": 10650
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/11/2023/0252",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4299}",
        "length": 121
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/04/2024/0095",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{5434}",
        "length": 240
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/02/2024/0040",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4728,4729,5393,5394}",
        "length": 5550
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/12/2023/0264",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4333}",
        "length": 4205
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/11/2023/0246",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3834,3833}",
        "length": 47
    },
    {
        "no_reference": "KUTT.HT/PWR/A2/11/2023/0249",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4294}",
        "length": 510
    },
    {
        "no_reference": "KUTT.KM/TEL/A1/11/2023/0255",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{4311,4312,4313,4314}",
        "length": 80
    },
    {
        "no_reference": "KUTT.KT/PWR/A1/11/2023/0253",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4300}",
        "length": 24
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/02/2024/0041",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4730,4731,5350,5351,5352,5353}",
        "length": 8030
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/10/2023/0237",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3669,3670,3779}",
        "length": 316
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/12/2023/0265",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4334}",
        "length": 1682
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/12/2023/0266",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4335}",
        "length": 3100
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0280",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4349}",
        "length": 4100
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/02/2024/0042",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4732,4733}",
        "length": 1847
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/05/2023/0156",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{2525,2524}",
        "length": 698
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/12/2023/0301",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4377,4688}",
        "length": 7019
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/12/2023/0305",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4475,4476,4477,4478,4479,4385}",
        "length": 465
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0288",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4360}",
        "length": 11200
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0296",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4372}",
        "length": 3700
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0281",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4350}",
        "length": 4000
    },
    {
        "no_reference": "KUTT.KT/TEL/A1/10/2023/0238",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{3664}",
        "length": 30
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0289",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4361}",
        "length": 4200
    },
    {
        "no_reference": "KUTT.KT/PWR/A1/11/2023/0250",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4304,4298,4303}",
        "length": 40
    },
    {
        "no_reference": "KUTT.KN/WTR/A1/11/2023/0243",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{3770}",
        "length": 75
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/12/2023/0297",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4373}",
        "length": 3700
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/12/2023/0267",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4336}",
        "length": 1641
    },
    {
        "no_reference": "KUTT.MG/PWR/A3/12/2023/0310",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4592,4593,4594,4591,4595,4596,4597,4598,4590,4612,4605,4599,4600,4601,4602,4603,4604,4606,4607,4608,4609,4610,4611,4656,4657}",
        "length": 2104
    },
    {
        "no_reference": "KUTT.KT/PWR/A1/06/2023/0170",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2569,2571}",
        "length": 30
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0282",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4351}",
        "length": 11300
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2022/0358",
        "utility_provider": {
        "provider_code": 20,
        "name": "VC Telecoms Sdn Bhd",
        "logo": "20.webp",
        "sort_name": "VCT",
        "logo_url": "https://app.kutt.my/providers/logo/20.webp"
    },
        "id_road_info": "{1450,1445,1446,1447,1448,1449,1451,1443,1444,1442}",
        "length": 3839
    },
    {
        "no_reference": "KUTT.BT/TEL/A3/12/2023/0268",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4337}",
        "length": 6502
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0271",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4575,4576,4577,4340}",
        "length": 2800
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0274",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4343}",
        "length": 2474
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0275",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4522,4578,4579,4344}",
        "length": 7032
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0050",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4738,5349}",
        "length": 4815
    },
    {
        "no_reference": "KUTT.DN/PWR/E1/12/2023/0001",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4394}",
        "length": 15
    },
    {
        "no_reference": "KUTT.KN/WTR/A2/06/2023/0166",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2549,2574}",
        "length": 200
    },
    {
        "no_reference": "KUTT.KN/PWR/A3/01/2023/0003",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1714,1719,1712,1717,1716,1718,1713,1715}",
        "length": 2850
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0295",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4371}",
        "length": 2700
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/11/2023/0257",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4315,4320,4316,4319}",
        "length": 182
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0290",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4362,4686,4687}",
        "length": 12516
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0293",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4365}",
        "length": 6400
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0294",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4370,4682,4683}",
        "length": 16460
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/12/2022/0369",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1593}",
        "length": 485
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0291",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4363}",
        "length": 1100
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0292",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4364}",
        "length": 3400
    },
    {
        "no_reference": "KUTT.KM/SWR/A3/12/2023/0261",
        "utility_provider": {
        "provider_code": 11,
        "name": "Indah Water Konsortium Sdn Bhd",
        "logo": "11.webp",
        "sort_name": "IWK",
        "logo_url": "https://app.kutt.my/providers/logo/11.webp"
    },
        "id_road_info": "{4386,4387,4388,4389,4390,4391,4329}",
        "length": 1074
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/11/2022/0344",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{1170,1213}",
        "length": 107
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/12/2023/0285",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4526,4354,4689}",
        "length": 4400
    },
    {
        "no_reference": "KUTT.HT/PWR/A3/05/2024/0107",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5435,5436,5437,5438,5439}",
        "length": 2172
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/12/2023/0276",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4345,4523,4524}",
        "length": 7900
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0283",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4352}",
        "length": 17000
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/12/2023/0284",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4353}",
        "length": 690
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/12/2023/0286",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4358}",
        "length": 2900
    },
    {
        "no_reference": "KUTT.BT/WTR/A2/12/2023/0308",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4547,4548,4549,4550,4546}",
        "length": 462
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0046",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4741,5348}",
        "length": 6650
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/12/2023/0300",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4376}",
        "length": 3200
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/10/2022/0311",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{923,924}",
        "length": 130
    },
    {
        "no_reference": "KUTT.KT/TEL/A1/10/2022/0313",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{922}",
        "length": 25
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/04/2024/0099",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5456}",
        "length": 20
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/12/2023/0303",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4379,4673}",
        "length": 10000
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/03/2024/0087",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5362,5361}",
        "length": 302
    },
    {
        "no_reference": "KUTT.MG/TEL/A2/12/2023/0302",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4378,4671,4672}",
        "length": 980
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/04/2024/0100",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5457}",
        "length": 20
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0027",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4707,4708}",
        "length": 6184
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/10/2022/0299",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{836,1117,1118}",
        "length": 430
    },
    {
        "no_reference": "KUTT.KN/TEL/A4/03/2023/0096",
        "utility_provider": {
        "provider_code": 7,
        "name": "Telekom Malaysia Berhad",
        "logo": "7.webp",
        "sort_name": "TMB",
        "logo_url": "https://app.kutt.my/providers/logo/7.webp"
    },
        "id_road_info": "{2290,2223,2279,2280,2281,2282,2283,2284,2285,2286,2287,2288,2289,2222,2291}",
        "length": 10616
    },
    {
        "no_reference": "KUTT.KT/PWR/A1/07/2023/0180",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{2588,2587}",
        "length": 50
    },
    {
        "no_reference": "KUTT.KT/WTR/A2/03/2023/0121",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{2304,2408,2409}",
        "length": 274
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/11/2023/0254",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{4310}",
        "length": 250
    },
    {
        "no_reference": "KUTT.KT/TEL/A2/09/2023/0234",
        "utility_provider": {
        "provider_code": 3,
        "name": "TIME dotCom Bhd",
        "logo": "3.webp",
        "sort_name": "TTDC",
        "logo_url": "https://app.kutt.my/providers/logo/3.webp"
    },
        "id_road_info": "{3495,3496,3494}",
        "length": 456
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/01/2024/0013",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4642,4641,4665,4640}",
        "length": 3858
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0033",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4717,4716,5356}",
        "length": 7016
    },
    {
        "no_reference": "KUTT.KT/TEL/A1/08/2022/0262",
        "utility_provider": {
        "provider_code": 1,
        "name": "Celcom Axiata Berhad",
        "logo": "1.webp",
        "sort_name": "CLM",
        "logo_url": "https://app.kutt.my/providers/logo/1.webp"
    },
        "id_road_info": "{608,635}",
        "length": 55
    },
    {
        "no_reference": "KUTT.KT/PWR/A1/12/2022/0362",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{1554,1566}",
        "length": 62
    },
    {
        "no_reference": "KUTT.KN/PWR/A3/10/2022/0309",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{890,883,884,885,2124,2125,2126,2127,2128,2129,2130,2131,2132,2133,2134,2135,2136,2137,2138,2139,2140,2141,2142,2143,2144,2145,2146,2147,2148,2149,2150,2151,2152,2153,2154,2155,2156,2157,2158,2159,2160,2161,2162,2163,2164,2165,2166,2167,2168,2169,2170,2171}",
        "length": 12944
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/11/2022/0322",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{962,963,964}",
        "length": 150
    },
    {
        "no_reference": "KUTT.KN/TEL/A3/10/2022/0316",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{929,930,932,938,933,934,935,936,937,939,940,941,942,943,944}",
        "length": 4290
    },
    {
        "no_reference": "KUTT.KN/WTR/A2/10/2022/0314",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{925}",
        "length": 350
    },
    {
        "no_reference": "KUTT.DN/PWR/A1/03/2024/0077",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5175}",
        "length": 80
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/12/2023/0304",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4381,4380,4383,4382,4384}",
        "length": 236
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/01/2024/0015",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{4647,4645,4646,4667}",
        "length": 2993
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/04/2024/0092",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5411}",
        "length": 293
    },
    {
        "no_reference": "KUTT.KM/WTR/A1/04/2024/0098",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{5455}",
        "length": 7
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/03/2024/0070",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5046,5047,5048,5049,5050,5051,5052,5053,5054,5055,5056,5057,5058,5059,5060,5061,5062,5063,5064,5065,5066,5067,5068,5069,5070}",
        "length": 5067
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/12/2023/0269",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4694}",
        "length": 4881
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/03/2024/0084",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5378}",
        "length": 130
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/02/2024/0045",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4739,4740}",
        "length": 6120
    },
    {
        "no_reference": "KUTT.DN/PWR/A3/08/2023/0208",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{3610,3611,3612,3613,3614,3615,3616,3617,3618,3620,3621,3622,3623,3624,3651,4242,4243,4244,4245,4246,4247,4248,4249,4250,4251,4252,4253,4254,4255,4256,4257,4258,4259,4260,4261,4262,4263,4264,4265,4266,4267,4268,4269,4270,4271,4272,4273,4274,4275,4276,4277,4278}",
        "length": 37506
    },
    {
        "no_reference": "KUTT.HT/TEL/A3/02/2024/0031",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4713,4712}",
        "length": 4520
    },
    {
        "no_reference": "KUTT.KN/TEL/A2/08/2023/0215",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{3076,3077}",
        "length": 220
    },
    {
        "no_reference": "KUTT.KT/TEL/A3/02/2024/0044",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4736}",
        "length": 3811
    },
    {
        "no_reference": "KUTT.KM/TEL/A2/04/2024/0096",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5401,5399,5400}",
        "length": 885
    },
    {
        "no_reference": "KUTT.HT/TEL/A1/09/2023/0227",
        "utility_provider": {
        "provider_code": 2,
        "name": "Digi.Com Berhad",
        "logo": "2.webp",
        "sort_name": "DIGI",
        "logo_url": "https://app.kutt.my/providers/logo/2.webp"
    },
        "id_road_info": "{3165}",
        "length": 60
    },
    {
        "no_reference": "KUTT.KM/GAS/A1/03/2024/0080",
        "utility_provider": {
        "provider_code": 9,
        "name": "Petroliam Nasional Berhad",
        "logo": "9.webp",
        "sort_name": "PNB",
        "logo_url": "https://app.kutt.my/providers/logo/9.webp"
    },
        "id_road_info": "{5253}",
        "length": 15
    },
    {
        "no_reference": "KUTT.KN/PWR/A2/03/2024/0065",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5024,5025}",
        "length": 130
    },
    {
        "no_reference": "KUTT.BT/PWR/A2/02/2024/0061",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4681,4774}",
        "length": 370
    },
    {
        "no_reference": "KUTT.KM/PWR/A2/02/2024/0057",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{4759,4760,4762,4763,4764,4765,4761,4654}",
        "length": 102
    },
    {
        "no_reference": "KUTT.KN/PWR/A1/03/2024/0076",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5147}",
        "length": 25
    },
    {
        "no_reference": "KUTT.DN/TEL/A3/02/2024/0047",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4742,4743,5297,5298,5299,5300,5301,5302}",
        "length": 25680
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/05/2024/0104",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5466}",
        "length": 30
    },
    {
        "no_reference": "KUTT.KN/PWR/A1/04/2024/0101",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5464,5465,5486}",
        "length": 65
    },
    {
        "no_reference": "KUTT.KM/PWR/A1/04/2024/0102",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5454}",
        "length": 10
    },
    {
        "no_reference": "KUTT.HT/WTR/A1/02/2024/0060",
        "utility_provider": {
        "provider_code": 21,
        "name": "Syarikat Air Terengganu Sdn Bhd",
        "logo": "21.webp",
        "sort_name": "SATU",
        "logo_url": "https://app.kutt.my/providers/logo/21.webp"
    },
        "id_road_info": "{4769}",
        "length": 23
    },
    {
        "no_reference": "KUTT.ST/TEL/A3/03/2024/0075",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5149,5148,5153,5154,5151,5152,5156,5157,5158,5150,5155,5159,5161,5162,5163,5164,5165,5166,5167,5168,5169,5170,5171,5172,5160}",
        "length": 2496
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/05/2024/0103",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5458,5459}",
        "length": 250
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/02/2024/0054",
        "utility_provider": {
        "provider_code": 23,
        "name": "ILAUNCH Sdn Bhd",
        "logo": "23.webp",
        "sort_name": "ILCH",
        "logo_url": "https://app.kutt.my/providers/logo/23.webp"
    },
        "id_road_info": "{4751}",
        "length": 3263
    },
    {
        "no_reference": "KUTT.KT/PWR/A2/05/2024/0105",
        "utility_provider": {
        "provider_code": 5,
        "name": "Tenaga Nasional Berhad",
        "logo": "5.webp",
        "sort_name": "TNB",
        "logo_url": "https://app.kutt.my/providers/logo/5.webp"
    },
        "id_road_info": "{5467,5468,5469,5470,5471,5472,5473}",
        "length": 480
    },
    {
        "no_reference": "KUTT.KM/TEL/A3/03/2024/0083",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5370,5371,5372,5373,5374,5375,5376,5377,5460,5461,5462}",
        "length": 7440
    },
    {
        "no_reference": "KUTT.MG/TEL/A3/03/2024/0072",
        "utility_provider": {
        "provider_code": 13,
        "name": "Maxis Berhad",
        "logo": "13.webp",
        "sort_name": "MXS",
        "logo_url": "https://app.kutt.my/providers/logo/13.webp"
    },
        "id_road_info": "{5072,5073,5074,5075,5076,5077,5078,5079,5080,5081,5082,5083,5084,5085,5086,5087,5088,5089,5090,5092,5093,5094,5096,5097,5098,5100,5091,5095,5099,5108,5109,5110,5111,5101,5102,5103,5104,5105,5106,5107,5071}",
        "length": 5370
    },
    {
        "no_reference": "KUTT.DN/PWR/A3/05/2024/0106",
        "utility_provider": {
        "provider_code": 99,
        "name": "Gas Processing Santong",
        "logo": "99.webp",
        "sort_name": "UNK",
        "logo_url": "https://app.kutt.my/providers/logo/99.webp"
    },
        "id_road_info": "{5482,5484,5485}",
        "length": 3300
    }
]';
    return $json_data;
}

function getProviderData()
{
    $providers = [
        [
            "provider_code" => 1,
            "name" => "Celcom Axiata Berhad",
            "logo" => "1.webp",
            "sort_name" => "CLM",
            "logo_url" => "https://app.kutt.my/providers/logo/1.webp"
        ],
        [
            "provider_code" => 2,
            "name" => "Digi.Com Berhad",
            "logo" => "2.webp",
            "sort_name" => "DIGI",
            "logo_url" => "https://app.kutt.my/providers/logo/2.webp"
        ],
        [
            "provider_code" => 3,
            "name" => "TIME dotCom Berhad",
            "logo" => "3.webp",
            "sort_name" => "TTDC",
            "logo_url" => "https://app.kutt.my/providers/logo/3.webp"
        ],
        [
            "provider_code" => 4,
            "name" => "FGV Prodata Systems Sdn Bhd",
            "logo" => "4.webp",
            "sort_name" => "FGV",
            "logo_url" => "https://app.kutt.my/providers/logo/4.webp"
        ],
        [
            "provider_code" => 5,
            "name" => "Tenaga Nasional Berhad",
            "logo" => "5.webp",
            "sort_name" => "TNB",
            "logo_url" => "https://app.kutt.my/providers/logo/5.webp"
        ],
        [
            "provider_code" => 6,
            "name" => "Pengurusan Air Pahang Berhad",
            "logo" => "6.webp",
            "sort_name" => "PAIP",
            "logo_url" => "https://app.kutt.my/providers/logo/6.webp"
        ],
        [
            "provider_code" => 7,
            "name" => "Telekom Malaysia Berhad",
            "logo" => "7.webp",
            "sort_name" => "TMB",
            "logo_url" => "https://app.kutt.my/providers/logo/7.webp"
        ],
        [
            "provider_code" => 8,
            "name" => "Gas Malaysia Distribution Sdn Bhd",
            "logo" => "8.webp",
            "sort_name" => "GMDSB",
            "logo_url" => "https://app.kutt.my/providers/logo/8.webp"
        ],
        [
            "provider_code" => 9,
            "name" => "Petroliam Nasional Berhad",
            "logo" => "9.webp",
            "sort_name" => "PNB",
            "logo_url" => "https://app.kutt.my/providers/logo/9.webp"
        ],
        [
            "provider_code" => 10,
            "name" => "SWCorp Malaysia",
            "logo" => "10.webp",
            "sort_name" => "SWC",
            "logo_url" => "https://app.kutt.my/providers/logo/10.webp"
        ],
        [
            "provider_code" => 11,
            "name" => "Indah Water Konsortium Sdn Bhd",
            "logo" => "11.webp",
            "sort_name" => "IWK",
            "logo_url" => "https://app.kutt.my/providers/logo/11.webp"
        ],
        [
            "provider_code" => 12,
            "name" => "Sapura Energy Berhad",
            "logo" => "12.webp",
            "sort_name" => "SEB",
            "logo_url" => "https://app.kutt.my/providers/logo/12.webp"
        ],
        [
            "provider_code" => 13,
            "name" => "Maxis Berhad",
            "logo" => "13.webp",
            "sort_name" => "MXS",
            "logo_url" => "https://app.kutt.my/providers/logo/13.webp"
        ],
        [
            "provider_code" => 14,
            "name" => "edotco Group Sdn Bhd",
            "logo" => "14.webp",
            "sort_name" => "EDC",
            "logo_url" => "https://app.kutt.my/providers/logo/14.webp"
        ],
        [
            "provider_code" => 15,
            "name" => "YTL Communications Sdn Bhd",
            "logo" => "15.webp",
            "sort_name" => "YES",
            "logo_url" => "https://app.kutt.my/providers/logo/15.webp"
        ],
        [
            "provider_code" => 16,
            "name" => "Allo Technology Sdn Bhd",
            "logo" => "16.webp",
            "sort_name" => "ALLO",
            "logo_url" => "https://app.kutt.my/providers/logo/16.webp"
        ],
        [
            "provider_code" => 17,
            "name" => "FTJ Bio Power Sdn Bhd",
            "logo" => "17.webp",
            "sort_name" => "FTJ",
            "logo_url" => "https://app.kutt.my/providers/logo/17.webp"
        ],
        [
            "provider_code" => 18,
            "name" => "Fibrecomm Network (M) Sdn Bhd",
            "logo" => "18.webp",
            "sort_name" => "FNSB",
            "logo_url" => "https://app.kutt.my/providers/logo/18.webp"
        ],
        [
            "provider_code" => 19,
            "name" => "Fiberail Sdn Bhd",
            "logo" => "19.webp",
            "sort_name" => "FRSB",
            "logo_url" => "https://app.kutt.my/providers/logo/19.webp"
        ],
        [
            "provider_code" => 20,
            "name" => "VC Telecoms Sdn Bhd",
            "logo" => "20.webp",
            "sort_name" => "VCT",
            "logo_url" => "https://app.kutt.my/providers/logo/20.webp"
        ],
        [
            "provider_code" => 21,
            "name" => "Syarikat Air Terengganu Sdn Bhd",
            "logo" => "21.webp",
            "sort_name" => "SATU",
            "logo_url" => "https://app.kutt.my/providers/logo/21.webp"
        ],
        [
            "provider_code" => 22,
            "name" => "Lembaga Air Perak",
            "logo" => "22.webp",
            "sort_name" => "LAP",
            "logo_url" => "https://app.kutt.my/providers/logo/22.webp"
        ],
        [
            "provider_code" => 23,
            "name" => "ILAUNCH Sdn Bhd",
            "logo" => "23.webp",
            "sort_name" => "ILCH",
            "logo_url" => "https://app.kutt.my/providers/logo/23.webp"
        ],
        [
            "provider_code" => 24,
            "name" => "Bekalan Air KIPC Sdn Bhd",
            "logo" => "24.webp",
            "sort_name" => "BAK",
            "logo_url" => "https://app.kutt.my/providers/logo/24.webp"
        ],
        [
            "provider_code" => 25,
            "name" => "Permint Granite Sdn Bhd",
            "logo" => "25.webp",
            "sort_name" => "PGSB",
            "logo_url" => "https://app.kutt.my/providers/logo/25.webp"
        ],
        [
            "provider_code" => 26,
            "name" => "Touch Mindscape",
            "logo" => "26.webp",
            "sort_name" => "TMS",
            "logo_url" => "https://app.kutt.my/providers/logo/26.webp"
        ],
        [
            "provider_code" => 27,
            "name" => "Ace Gases Sdn Bhd",
            "logo" => "27.webp",
            "sort_name" => "AGSB",
            "logo_url" => "https://app.kutt.my/providers/logo/27.webp"
        ],
        [
            "provider_code" => 28,
            "name" => "Petronas Chemicals Ammonia Sdn Bhd",
            "logo" => "28.webp",
            "sort_name" => "PCA",
            "logo_url" => "https://app.kutt.my/providers/logo/28.webp"
        ],
        [
            "provider_code" => 29,
            "name" => "Kertih Terminals Sdn Bhd",
            "logo" => "29.webp",
            "sort_name" => "KTSB",
            "logo_url" => "https://app.kutt.my/providers/logo/29.webp"
        ],
        [
            "provider_code" => 30,
            "name" => "Eastern Steel Sdn Bhd",
            "logo" => "30.webp",
            "sort_name" => "ESSB",
            "logo_url" => "https://app.kutt.my/providers/logo/30.webp"
        ],
        [
            "provider_code" => 31,
            "name" => "PR1MA Communications Sdn Bhd",
            "logo" => "31.webp",
            "sort_name" => "P1C",
            "logo_url" => "https://app.kutt.my/providers/logo/31.webp"
        ],
        [
            "provider_code" => 99,
            "name" => "Tidak Diketahui",
            "logo" => "99.webp",
            "sort_name" => "UNK",
            "logo_url" => "https://app.kutt.my/providers/logo/99.webp"
        ]
    ];

    return $providers;
}

// Function to get length by no_reference
// function getLengthByNoReference($array, $no_reference) {
//     foreach ($array as $item) {
//         if ($item['no_reference'] === $no_reference) {
//             return $item['length'];
//         }
//     }
//     return null; // Return null if no match found
// }
function getLengthByNoReference($array, $no_reference)
{
    // Extract the last 9 characters of the provided no_reference
    if ($no_reference != null) {
        $lastNineChars = substr($no_reference, -9);

        foreach ($array as $item) {
            // Extract the last 9 characters of the item's no_reference
            if (substr($item['no_reference'], -9) == $lastNineChars) {
                return $item['length'];
            }
        }
    }
    return null; // Return null if no match found
}

function getUPByNoReference($array, $no_reference)
{
    // Extract the last 9 characters of the provided no_reference
    if ($no_reference != null) {
        $lastNineChars = substr($no_reference, -9);

        foreach ($array as $item) {
            // Extract the last 9 characters of the item's no_reference
            if (substr($item['no_reference'], -9) == $lastNineChars) {
                return $item['utility_provider'];
            }
        }
    }
    return [
        "provider_code" => 99,
        "name" => "Tidak Diketahui",
        "logo" => "99.webp",
        "sort_name" => "UNK",
        "logo_url" => "https://app.kutt.my/providers/logo/99.webp"
    ]; // Return null if no match found
}

function getUPByUPNo($array, $upNo)
{
    // Extract the last 9 characters of the provided no_reference
    if ($upNo != null) {
        if (is_int($upNo)) {

            foreach ($array as $item) {
                if ($item['provider_code'] == $upNo) {
                    return $item;
                }
            }
        }
        return [
            "provider_code" => 99,
            "name" => "Tidak Diketahui",
            "logo" => "99.webp",
            "sort_name" => "UNK",
            "logo_url" => "https://app.kutt.my/providers/logo/99.webp"
        ];
    }
    return [
        "provider_code" => 99,
        "name" => "Tidak Diketahui",
        "logo" => "99.webp",
        "sort_name" => "UNK",
        "logo_url" => "https://app.kutt.my/providers/logo/99.webp"
    ]; // Return null if no match found
}

// Example function to fetch project details based on the ID
function fetchProjectDetails($projectId)
{
    $json_data = getReferenceData();
    $referenceData = json_decode($json_data, true);
    $upData = getProviderData();

    if ($projectId == 'all') {
        $projectInfo = new ProjectInfo();
        $projectDetailsAll = $projectInfo->getProjectInfoAllWithAuth();

        $response = [];

        foreach ($projectDetailsAll as $projectDetails) {
            // if ($projectDetails->application_length == null) {

            //     $length = getLengthByNoReference($referenceData, $projectDetails->reference_no);

            //     $projectDetails->application_length = $length;

            //     $projectInfo->updateEntry($projectDetails->system_id, 'application_length', $projectDetails->application_length);
            // }

            if($projectDetails->old_system == 'imohon' || $projectDetails->old_system == 'v2'){
                $parts = explode('/', $projectDetails->reference_no);
                $first_part = $parts[0]; // Index 0 contains "KUTT.BT"
                $parts_inside_first_part = explode('.', $first_part);
                $districtCode = $parts_inside_first_part[1]; // Index  example Correct
                if ($districtCode == 'BT' && $projectDetails->districts != '{01}') {
                    $projectInfo->updateEntry($projectDetails->system_id, 'districts', '{01}');
                } else if ($districtCode == 'DN' && $projectDetails->districts != '{02}') {
                    $projectInfo->updateEntry($projectDetails->system_id, 'districts', '{02}');
                } else if ($districtCode == 'KM' && $projectDetails->districts != '{03}') {
                    $projectInfo->updateEntry($projectDetails->system_id, 'districts', '{03}');
                } else if ($districtCode == 'KT' && $projectDetails->districts != '{04}') {
                    $projectInfo->updateEntry($projectDetails->system_id, 'districts', '{04}');
                } else if ($districtCode == 'HT' && $projectDetails->districts != '{05}') {
                    $projectInfo->updateEntry($projectDetails->system_id, 'districts', '{05}');
                } else if ($districtCode == 'MG' && $projectDetails->districts != '{06}') {
                    $projectInfo->updateEntry($projectDetails->system_id, 'districts', '{06}');
                } else if ($districtCode == 'ST' && $projectDetails->districts != '{07}') {
                    $projectInfo->updateEntry($projectDetails->system_id, 'districts', '{07}');
                } else if ($districtCode == 'KN' && $projectDetails->districts != '{08}') {
                    $projectInfo->updateEntry($projectDetails->system_id, 'districts', '{08}');
                }
            }

            // get utiliti provider details
            if ($projectDetails->utility_provider == 99) {
                $upDetails = getUPByNoReference($referenceData, $projectDetails->reference_no);

                // if ($up != null) {
                //     $projectDetails->utility_provider = $up['provider_code'];

                $projectInfo->updateEntry($projectDetails->system_id, 'utility_provider', $upDetails['provider_code']);
                // }
            } else {
                $upDetails = getUPByUPNo($upData, $projectDetails->utility_provider);
            }

            // setup open and close date for lifespan
            if (!isset($projectDetails->application_close_date)) {
                $projectDetails->application_close_date = date('Y-m-d');
            }

            // setup survey distance
            if (!isset($projectDetails->survey_distance)) {
                $projectDetails->survey_distance = null;
            }

            // calculate the lifespan
            if ($projectDetails->application_date == null) {
                $projectDetails->application_date = '2022-08-05';
            }
            $dateTime1 = new DateTime($projectDetails->application_date);
            $dateTime2 = new DateTime($projectDetails->application_close_date);
            $difference = $dateTime1->diff($dateTime2);
            $projectDetails->project_lifespan = $difference->days;

            // Get attachments udm
            if (!isset($projectDetails->attachment_udm)) {
                $projectDetails->attachment_udm = null;
            }

            // Get authorities details
            // Postgres json cleaning
            $cleaning = str_replace('{"{', '[{', $projectDetails->authorities);
            $cleaning = str_replace('}"}', '}]', $cleaning);
            $cleaning = str_replace('","', ',', $cleaning);
            $cleaning = str_replace('\\', '', $cleaning);

            $authorityInfo = json_decode($cleaning);

            // Initialize an empty array for storing authority details
            $authorityDetails = [];

            // Iterate over each authority object
            foreach ($authorityInfo as $authority) {
                // Prepare details for each authority
                if ($authority->name != null) {
                    $details = [
                        'name' => $authority->name,
                        'logo' => 'https://' . $_SERVER['HTTP_HOST'] . '/assets/media/authorities/' . $authority->logo . '.png',
                        'wayleave_date' => $authority->wl_date,
                        'work_permit_date' => $authority->work_permit_id,
                        'work_finish_date' => $authority->work_finish_id,
                        'work_done_date' => $authority->deposit_returns_id,
                        // Add more fields as needed
                    ];
    
                    // Append details to the authorityDetails array
                    $authorityDetails[] = $details;
                }
            }

            // Construct the response for each project
            $response[] = [
                'title' => $projectDetails->project_title,
                'reference_no' => $projectDetails->reference_no,
                'utility_provider' => $upDetails,
                'districts' => $projectDetails->district_name,
                'application_date' => $projectDetails->application_date,
                'application_distance' => $projectDetails->application_length,
                'project_lifespan' => $projectDetails->project_lifespan,
                'survey_distance' => $projectDetails->survey_distance,
                'authorities' => $authorityDetails,
                'attachment_bkil' => $projectDetails->attachment_udm,
                'attachment_udm' => $projectDetails->attachment_udm,
                // Add more details as needed
            ];
        }

        return $response;

    } else {
        // Handle single project case (not 'all')
        $projectInfo = new ProjectInfo();
        $projectDetails = $projectInfo->getProjectInfoByRef($projectId);

        if ($projectDetails == null) {
            return null;
        } else {
            if ($projectDetails->application_length == null) {
                $length = getLengthByNoReference($referenceData, $projectDetails->reference_no);

                $projectDetails->application_length = $length;

                $projectInfo->updateEntry($projectDetails->system_id, 'application_length', $projectDetails->application_length);
            }
            $upDetails = getUPByUPNo($upData, $projectDetails->utility_provider);
            // setup open and close date for lifespan
            if (!isset($projectDetails->application_close_date)) {
                $projectDetails->application_close_date = date('Y-m-d');
            }
            // setup survey distance
            if (!isset($projectDetails->survey_distance)) {
                $projectDetails->survey_distance = null;
            }

            // calculate the lifespan
            if ($projectDetails->application_date == null) {
                $projectDetails->application_date = '2022-08-05';
            }
            $dateTime1 = new DateTime($projectDetails->application_date);
            $dateTime2 = new DateTime($projectDetails->application_close_date);
            $difference = $dateTime1->diff($dateTime2);
            $projectDetails->project_lifespan = $difference->days;

            // Get attachments udm
            if (!isset($projectDetails->attachment_udm)) {
                $projectDetails->attachment_udm = null;
            }

            // Get authorities details
            $authorityInfo = $projectInfo->getAuthorityProjectInfo($projectDetails->system_id);

            // Initialize an empty array for storing authority details
            $authorityDetails = [];

            // Iterate over each authority object
            foreach ($authorityInfo as $authority) {
                // Prepare details for each authority
                $details = [
                    'name' => $authority->name,
                    'logo' => 'https://' . $_SERVER['HTTP_HOST'] . '/assets/media/authorities/' . $authority->logo . '.png',
                    'wayleave_date' => $authority->wl_date,
                    'work_permit_date' => $authority->work_permit_id,
                    'work_finish_date' => $authority->work_finish_id,
                    'work_done_date' => $authority->deposit_returns_id,
                    // Add more fields as needed
                ];

                // Append details to the authorityDetails array
                $authorityDetails[] = $details;
            }

            // Construct and return the response for single project
            return [
                'title' => $projectDetails->project_title,
                'reference_no' => $projectDetails->reference_no,
                'utility_provider' => $upDetails,
                'districts' => $projectDetails->district_name,
                'application_date' => $projectDetails->application_date,
                'application_distance' => $projectDetails->application_length,
                'project_lifespan' => $projectDetails->project_lifespan,
                'survey_distance' => $projectDetails->survey_distance,
                'authorities' => $authorityDetails,
                'attachment_bkil' => $projectDetails->attachment_udm,
                'attachment_udm' => $projectDetails->attachment_udm,
                // Add more details as needed
            ];
        }
    }
}

// Check if ID parameter is provided
if ($refNo == null) {
    // Fetch project details collection
    $projectCollection = fetchProjectDetails('all');

    // Encode project details as JSON and output
    echo json_encode($projectCollection);

} else if (strlen($refNo) < 26) {
    // Not a complete reference number passed
    // continue searching for reference number suggestions
} else {
    // Fetch project details based on the provided ID
    $projectDetails = fetchProjectDetails($refNo);

    // Check if project details were found
    if ($projectDetails == null) {
        http_response_code(404); // Not Found
        echo json_encode(array('error' => 'Project details not found'));
        exit;
    } else {
        // Encode project details as JSON and output
        echo json_encode($projectDetails);
    }
}