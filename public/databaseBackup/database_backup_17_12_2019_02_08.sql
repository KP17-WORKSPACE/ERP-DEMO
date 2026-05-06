DROP TABLE categories;

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories VALUES("1","Production","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO categories VALUES("2","Purchasing","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO categories VALUES("3","Merchandising","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO categories VALUES("4","Research and Development","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO categories VALUES("5","Marketing","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO categories VALUES("6","Customer Service","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO categories VALUES("7","Accountants","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO categories VALUES("8","Human Resource Management","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO categories VALUES("9","Accounting and Finance","1","2019-12-15 14:03:12","2019-12-15 14:03:12");



DROP TABLE comments;

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `ticket_id` int(10) unsigned NOT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_ticket_id_foreign` (`ticket_id`),
  CONSTRAINT `comments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE continents;

CREATE TABLE `continents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO continents VALUES("1","AF","Africa","1","","");
INSERT INTO continents VALUES("2","AN","Antarctica","1","","");
INSERT INTO continents VALUES("3","AS","Asia","1","","");
INSERT INTO continents VALUES("4","EU","Europe","1","","");
INSERT INTO continents VALUES("5","NA","North America","1","","");
INSERT INTO continents VALUES("6","OC","Oceania","1","","");
INSERT INTO continents VALUES("7","SA","South America","1","","");



DROP TABLE countries;

CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `native` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `continent` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capital` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `languages` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO countries VALUES("1","AD","Andorra","Andorra","376","EU","Andorra la Vella","EUR","ca","1","","");
INSERT INTO countries VALUES("2","AE","United Arab Emirates","دولة الإمارات العربية المتحدة","971","AS","Abu Dhabi","AED","ar","1","","");
INSERT INTO countries VALUES("3","AF","Afghanistan","افغانستان","93","AS","Kabul","AFN","ps,uz,tk","1","","");
INSERT INTO countries VALUES("4","AG","Antigua and Barbuda","Antigua and Barbuda","1268","NA","Saint John\'s","XCD","en","1","","");
INSERT INTO countries VALUES("5","AI","Anguilla","Anguilla","1264","NA","The Valley","XCD","en","1","","");
INSERT INTO countries VALUES("6","AL","Albania","Shqipëria","355","EU","Tirana","ALL","sq","1","","");
INSERT INTO countries VALUES("7","AM","Armenia","Հայաստան","374","AS","Yerevan","AMD","hy,ru","1","","");
INSERT INTO countries VALUES("8","AO","Angola","Angola","244","AF","Luanda","AOA","pt","1","","");
INSERT INTO countries VALUES("9","AQ","Antarctica","Antarctica","672","AN","","","","1","","");
INSERT INTO countries VALUES("10","AR","Argentina","Argentina","54","SA","Buenos Aires","ARS","es,gn","1","","");
INSERT INTO countries VALUES("11","AS","American Samoa","American Samoa","1684","OC","Pago Pago","USD","en,sm","1","","");
INSERT INTO countries VALUES("12","AT","Austria","Österreich","43","EU","Vienna","EUR","de","1","","");
INSERT INTO countries VALUES("13","AU","Australia","Australia","61","OC","Canberra","AUD","en","1","","");
INSERT INTO countries VALUES("14","AW","Aruba","Aruba","297","NA","Oranjestad","AWG","nl,pa","1","","");
INSERT INTO countries VALUES("15","AX","Åland","Åland","358","EU","Mariehamn","EUR","sv","1","","");
INSERT INTO countries VALUES("16","AZ","Azerbaijan","Azərbaycan","994","AS","Baku","AZN","az","1","","");
INSERT INTO countries VALUES("17","BA","Bosnia and Herzegovina","Bosna i Hercegovina","387","EU","Sarajevo","BAM","bs,hr,sr","1","","");
INSERT INTO countries VALUES("18","BB","Barbados","Barbados","1246","NA","Bridgetown","BBD","en","1","","");
INSERT INTO countries VALUES("19","BD","Bangladesh","Bangladesh","880","AS","Dhaka","BDT","bn","1","","");
INSERT INTO countries VALUES("20","BE","Belgium","België","32","EU","Brussels","EUR","nl,fr,de","1","","");
INSERT INTO countries VALUES("21","BF","Burkina Faso","Burkina Faso","226","AF","Ouagadougou","XOF","fr,ff","1","","");
INSERT INTO countries VALUES("22","BG","Bulgaria","България","359","EU","Sofia","BGN","bg","1","","");
INSERT INTO countries VALUES("23","BH","Bahrain","‏البحرين","973","AS","Manama","BHD","ar","1","","");
INSERT INTO countries VALUES("24","BI","Burundi","Burundi","257","AF","Bujumbura","BIF","fr,rn","1","","");
INSERT INTO countries VALUES("25","BJ","Benin","Bénin","229","AF","Porto-Novo","XOF","fr","1","","");
INSERT INTO countries VALUES("26","BL","Saint Barthélemy","Saint-Barthélemy","590","NA","Gustavia","EUR","fr","1","","");
INSERT INTO countries VALUES("27","BM","Bermuda","Bermuda","1441","NA","Hamilton","BMD","en","1","","");
INSERT INTO countries VALUES("28","BN","Brunei","Negara Brunei Darussalam","673","AS","Bandar Seri Begawan","BND","ms","1","","");
INSERT INTO countries VALUES("29","BO","Bolivia","Bolivia","591","SA","Sucre","BOB,BOV","es,ay,qu","1","","");
INSERT INTO countries VALUES("30","BQ","Bonaire","Bonaire","5997","NA","Kralendijk","USD","nl","1","","");
INSERT INTO countries VALUES("31","BR","Brazil","Brasil","55","SA","Brasília","BRL","pt","1","","");
INSERT INTO countries VALUES("32","BS","Bahamas","Bahamas","1242","NA","Nassau","BSD","en","1","","");
INSERT INTO countries VALUES("33","BT","Bhutan","ʼbrug-yul","975","AS","Thimphu","BTN,INR","dz","1","","");
INSERT INTO countries VALUES("34","BV","Bouvet Island","Bouvetøya","47","AN","","NOK","no,nb,nn","1","","");
INSERT INTO countries VALUES("35","BW","Botswana","Botswana","267","AF","Gaborone","BWP","en,tn","1","","");
INSERT INTO countries VALUES("36","BY","Belarus","Белару́сь","375","EU","Minsk","BYR","be,ru","1","","");
INSERT INTO countries VALUES("37","BZ","Belize","Belize","501","NA","Belmopan","BZD","en,es","1","","");
INSERT INTO countries VALUES("38","CA","Canada","Canada","1","NA","Ottawa","CAD","en,fr","1","","");
INSERT INTO countries VALUES("39","CC","Cocos [Keeling] Islands","Cocos (Keeling) Islands","61","AS","West Island","AUD","en","1","","");
INSERT INTO countries VALUES("40","CD","Democratic Republic of the Congo","République démocratique du Congo","243","AF","Kinshasa","CDF","fr,ln,kg,sw,lu","1","","");
INSERT INTO countries VALUES("41","CF","Central African Republic","Ködörösêse tî Bêafrîka","236","AF","Bangui","XAF","fr,sg","1","","");
INSERT INTO countries VALUES("42","CG","Republic of the Congo","République du Congo","242","AF","Brazzaville","XAF","fr,ln","1","","");
INSERT INTO countries VALUES("43","CH","Switzerland","Schweiz","41","EU","Bern","CHE,CHF,CHW","de,fr,it","1","","");
INSERT INTO countries VALUES("44","CI","Ivory Coast","Côte d\'Ivoire","225","AF","Yamoussoukro","XOF","fr","1","","");
INSERT INTO countries VALUES("45","CK","Cook Islands","Cook Islands","682","OC","Avarua","NZD","en","1","","");
INSERT INTO countries VALUES("46","CL","Chile","Chile","56","SA","Santiago","CLF,CLP","es","1","","");
INSERT INTO countries VALUES("47","CM","Cameroon","Cameroon","237","AF","Yaoundé","XAF","en,fr","1","","");
INSERT INTO countries VALUES("48","CN","China","中国","86","AS","Beijing","CNY","zh","1","","");
INSERT INTO countries VALUES("49","CO","Colombia","Colombia","57","SA","Bogotá","COP","es","1","","");
INSERT INTO countries VALUES("50","CR","Costa Rica","Costa Rica","506","NA","San José","CRC","es","1","","");
INSERT INTO countries VALUES("51","CU","Cuba","Cuba","53","NA","Havana","CUC,CUP","es","1","","");
INSERT INTO countries VALUES("52","CV","Cape Verde","Cabo Verde","238","AF","Praia","CVE","pt","1","","");
INSERT INTO countries VALUES("53","CW","Curacao","Curaçao","5999","NA","Willemstad","ANG","nl,pa,en","1","","");
INSERT INTO countries VALUES("54","CX","Christmas Island","Christmas Island","61","AS","Flying Fish Cove","AUD","en","1","","");
INSERT INTO countries VALUES("55","CY","Cyprus","Κύπρος","357","EU","Nicosia","EUR","el,tr,hy","1","","");
INSERT INTO countries VALUES("56","CZ","Czech Republic","Česká republika","420","EU","Prague","CZK","cs,sk","1","","");
INSERT INTO countries VALUES("57","DE","Germany","Deutschland","49","EU","Berlin","EUR","de","1","","");
INSERT INTO countries VALUES("58","DJ","Djibouti","Djibouti","253","AF","Djibouti","DJF","fr,ar","1","","");
INSERT INTO countries VALUES("59","DK","Denmark","Danmark","45","EU","Copenhagen","DKK","da","1","","");
INSERT INTO countries VALUES("60","DM","Dominica","Dominica","1767","NA","Roseau","XCD","en","1","","");
INSERT INTO countries VALUES("61","DO","Dominican Republic","República Dominicana","1809,1829,1849","NA","Santo Domingo","DOP","es","1","","");
INSERT INTO countries VALUES("62","DZ","Algeria","الجزائر","213","AF","Algiers","DZD","ar","1","","");
INSERT INTO countries VALUES("63","EC","Ecuador","Ecuador","593","SA","Quito","USD","es","1","","");
INSERT INTO countries VALUES("64","EE","Estonia","Eesti","372","EU","Tallinn","EUR","et","1","","");
INSERT INTO countries VALUES("65","EG","Egypt","مصر‎","20","AF","Cairo","EGP","ar","1","","");
INSERT INTO countries VALUES("66","EH","Western Sahara","الصحراء الغربية","212","AF","El Aaiún","MAD,DZD,MRU","es","1","","");
INSERT INTO countries VALUES("67","ER","Eritrea","ኤርትራ","291","AF","Asmara","ERN","ti,ar,en","1","","");
INSERT INTO countries VALUES("68","ES","Spain","España","34","EU","Madrid","EUR","es,eu,ca,gl,oc","1","","");
INSERT INTO countries VALUES("69","ET","Ethiopia","ኢትዮጵያ","251","AF","Addis Ababa","ETB","am","1","","");
INSERT INTO countries VALUES("70","FI","Finland","Suomi","358","EU","Helsinki","EUR","fi,sv","1","","");
INSERT INTO countries VALUES("71","FJ","Fiji","Fiji","679","OC","Suva","FJD","en,fj,hi,ur","1","","");
INSERT INTO countries VALUES("72","FK","Falkland Islands","Falkland Islands","500","SA","Stanley","FKP","en","1","","");
INSERT INTO countries VALUES("73","FM","Micronesia","Micronesia","691","OC","Palikir","USD","en","1","","");
INSERT INTO countries VALUES("74","FO","Faroe Islands","Føroyar","298","EU","Tórshavn","DKK","fo","1","","");
INSERT INTO countries VALUES("75","FR","France","France","33","EU","Paris","EUR","fr","1","","");
INSERT INTO countries VALUES("76","GA","Gabon","Gabon","241","AF","Libreville","XAF","fr","1","","");
INSERT INTO countries VALUES("77","GB","United Kingdom","United Kingdom","44","EU","London","GBP","en","1","","");
INSERT INTO countries VALUES("78","GD","Grenada","Grenada","1473","NA","St. George\'s","XCD","en","1","","");
INSERT INTO countries VALUES("79","GE","Georgia","საქართველო","995","AS","Tbilisi","GEL","ka","1","","");
INSERT INTO countries VALUES("80","GF","French Guiana","Guyane française","594","SA","Cayenne","EUR","fr","1","","");
INSERT INTO countries VALUES("81","GG","Guernsey","Guernsey","44","EU","St. Peter Port","GBP","en,fr","1","","");
INSERT INTO countries VALUES("82","GH","Ghana","Ghana","233","AF","Accra","GHS","en","1","","");
INSERT INTO countries VALUES("83","GI","Gibraltar","Gibraltar","350","EU","Gibraltar","GIP","en","1","","");
INSERT INTO countries VALUES("84","GL","Greenland","Kalaallit Nunaat","299","NA","Nuuk","DKK","kl","1","","");
INSERT INTO countries VALUES("85","GM","Gambia","Gambia","220","AF","Banjul","GMD","en","1","","");
INSERT INTO countries VALUES("86","GN","Guinea","Guinée","224","AF","Conakry","GNF","fr,ff","1","","");
INSERT INTO countries VALUES("87","GP","Guadeloupe","Guadeloupe","590","NA","Basse-Terre","EUR","fr","1","","");
INSERT INTO countries VALUES("88","GQ","Equatorial Guinea","Guinea Ecuatorial","240","AF","Malabo","XAF","es,fr","1","","");
INSERT INTO countries VALUES("89","GR","Greece","Ελλάδα","30","EU","Athens","EUR","el","1","","");
INSERT INTO countries VALUES("90","GS","South Georgia and the South Sandwich Islands","South Georgia","500","AN","King Edward Point","GBP","en","1","","");
INSERT INTO countries VALUES("91","GT","Guatemala","Guatemala","502","NA","Guatemala City","GTQ","es","1","","");
INSERT INTO countries VALUES("92","GU","Guam","Guam","1671","OC","Hagåtña","USD","en,ch,es","1","","");
INSERT INTO countries VALUES("93","GW","Guinea-Bissau","Guiné-Bissau","245","AF","Bissau","XOF","pt","1","","");
INSERT INTO countries VALUES("94","GY","Guyana","Guyana","592","SA","Georgetown","GYD","en","1","","");
INSERT INTO countries VALUES("95","HK","Hong Kong","香港","852","AS","City of Victoria","HKD","zh,en","1","","");
INSERT INTO countries VALUES("96","HM","Heard Island and McDonald Islands","Heard Island and McDonald Islands","61","AN","","AUD","en","1","","");
INSERT INTO countries VALUES("97","HN","Honduras","Honduras","504","NA","Tegucigalpa","HNL","es","1","","");
INSERT INTO countries VALUES("98","HR","Croatia","Hrvatska","385","EU","Zagreb","HRK","hr","1","","");
INSERT INTO countries VALUES("99","HT","Haiti","Haïti","509","NA","Port-au-Prince","HTG,USD","fr,ht","1","","");
INSERT INTO countries VALUES("100","HU","Hungary","Magyarország","36","EU","Budapest","HUF","hu","1","","");
INSERT INTO countries VALUES("101","ID","Indonesia","Indonesia","62","AS","Jakarta","IDR","id","1","","");
INSERT INTO countries VALUES("102","IE","Ireland","Éire","353","EU","Dublin","EUR","ga,en","1","","");
INSERT INTO countries VALUES("103","IL","Israel","יִשְׂרָאֵל","972","AS","Jerusalem","ILS","he,ar","1","","");
INSERT INTO countries VALUES("104","IM","Isle of Man","Isle of Man","44","EU","Douglas","GBP","en,gv","1","","");
INSERT INTO countries VALUES("105","IN","India","भारत","91","AS","New Delhi","INR","hi,en","1","","");
INSERT INTO countries VALUES("106","IO","British Indian Ocean Territory","British Indian Ocean Territory","246","AS","Diego Garcia","USD","en","1","","");
INSERT INTO countries VALUES("107","IQ","Iraq","العراق","964","AS","Baghdad","IQD","ar,ku","1","","");
INSERT INTO countries VALUES("108","IR","Iran","ایران","98","AS","Tehran","IRR","fa","1","","");
INSERT INTO countries VALUES("109","IS","Iceland","Ísland","354","EU","Reykjavik","ISK","is","1","","");
INSERT INTO countries VALUES("110","IT","Italy","Italia","39","EU","Rome","EUR","it","1","","");
INSERT INTO countries VALUES("111","JE","Jersey","Jersey","44","EU","Saint Helier","GBP","en,fr","1","","");
INSERT INTO countries VALUES("112","JM","Jamaica","Jamaica","1876","NA","Kingston","JMD","en","1","","");
INSERT INTO countries VALUES("113","JO","Jordan","الأردن","962","AS","Amman","JOD","ar","1","","");
INSERT INTO countries VALUES("114","JP","Japan","日本","81","AS","Tokyo","JPY","ja","1","","");
INSERT INTO countries VALUES("115","KE","Kenya","Kenya","254","AF","Nairobi","KES","en,sw","1","","");
INSERT INTO countries VALUES("116","KG","Kyrgyzstan","Кыргызстан","996","AS","Bishkek","KGS","ky,ru","1","","");
INSERT INTO countries VALUES("117","KH","Cambodia","Kâmpŭchéa","855","AS","Phnom Penh","KHR","km","1","","");
INSERT INTO countries VALUES("118","KI","Kiribati","Kiribati","686","OC","South Tarawa","AUD","en","1","","");
INSERT INTO countries VALUES("119","KM","Comoros","Komori","269","AF","Moroni","KMF","ar,fr","1","","");
INSERT INTO countries VALUES("120","KN","Saint Kitts and Nevis","Saint Kitts and Nevis","1869","NA","Basseterre","XCD","en","1","","");
INSERT INTO countries VALUES("121","KP","North Korea","북한","850","AS","Pyongyang","KPW","ko","1","","");
INSERT INTO countries VALUES("122","KR","South Korea","대한민국","82","AS","Seoul","KRW","ko","1","","");
INSERT INTO countries VALUES("123","KW","Kuwait","الكويت","965","AS","Kuwait City","KWD","ar","1","","");
INSERT INTO countries VALUES("124","KY","Cayman Islands","Cayman Islands","1345","NA","George Town","KYD","en","1","","");
INSERT INTO countries VALUES("125","KZ","Kazakhstan","Қазақстан","76,77","AS","Astana","KZT","kk,ru","1","","");
INSERT INTO countries VALUES("126","LA","Laos","ສປປລາວ","856","AS","Vientiane","LAK","lo","1","","");
INSERT INTO countries VALUES("127","LB","Lebanon","لبنان","961","AS","Beirut","LBP","ar,fr","1","","");
INSERT INTO countries VALUES("128","LC","Saint Lucia","Saint Lucia","1758","NA","Castries","XCD","en","1","","");
INSERT INTO countries VALUES("129","LI","Liechtenstein","Liechtenstein","423","EU","Vaduz","CHF","de","1","","");
INSERT INTO countries VALUES("130","LK","Sri Lanka","śrī laṃkāva","94","AS","Colombo","LKR","si,ta","1","","");
INSERT INTO countries VALUES("131","LR","Liberia","Liberia","231","AF","Monrovia","LRD","en","1","","");
INSERT INTO countries VALUES("132","LS","Lesotho","Lesotho","266","AF","Maseru","LSL,ZAR","en,st","1","","");
INSERT INTO countries VALUES("133","LT","Lithuania","Lietuva","370","EU","Vilnius","EUR","lt","1","","");
INSERT INTO countries VALUES("134","LU","Luxembourg","Luxembourg","352","EU","Luxembourg","EUR","fr,de,lb","1","","");
INSERT INTO countries VALUES("135","LV","Latvia","Latvija","371","EU","Riga","EUR","lv","1","","");
INSERT INTO countries VALUES("136","LY","Libya","‏ليبيا","218","AF","Tripoli","LYD","ar","1","","");
INSERT INTO countries VALUES("137","MA","Morocco","المغرب","212","AF","Rabat","MAD","ar","1","","");
INSERT INTO countries VALUES("138","MC","Monaco","Monaco","377","EU","Monaco","EUR","fr","1","","");
INSERT INTO countries VALUES("139","MD","Moldova","Moldova","373","EU","Chișinău","MDL","ro","1","","");
INSERT INTO countries VALUES("140","ME","Montenegro","Црна Гора","382","EU","Podgorica","EUR","sr,bs,sq,hr","1","","");
INSERT INTO countries VALUES("141","MF","Saint Martin","Saint-Martin","590","NA","Marigot","EUR","en,fr,nl","1","","");
INSERT INTO countries VALUES("142","MG","Madagascar","Madagasikara","261","AF","Antananarivo","MGA","fr,mg","1","","");
INSERT INTO countries VALUES("143","MH","Marshall Islands","M̧ajeļ","692","OC","Majuro","USD","en,mh","1","","");
INSERT INTO countries VALUES("144","MK","Macedonia","Македонија","389","EU","Skopje","MKD","mk","1","","");
INSERT INTO countries VALUES("145","ML","Mali","Mali","223","AF","Bamako","XOF","fr","1","","");
INSERT INTO countries VALUES("146","MM","Myanmar [Burma]","မြန်မာ","95","AS","Naypyidaw","MMK","my","1","","");
INSERT INTO countries VALUES("147","MN","Mongolia","Монгол улс","976","AS","Ulan Bator","MNT","mn","1","","");
INSERT INTO countries VALUES("148","MO","Macao","澳門","853","AS","","MOP","zh,pt","1","","");
INSERT INTO countries VALUES("149","MP","Northern Mariana Islands","Northern Mariana Islands","1670","OC","Saipan","USD","en,ch","1","","");
INSERT INTO countries VALUES("150","MQ","Martinique","Martinique","596","NA","Fort-de-France","EUR","fr","1","","");
INSERT INTO countries VALUES("151","MR","Mauritania","موريتانيا","222","AF","Nouakchott","MRU","ar","1","","");
INSERT INTO countries VALUES("152","MS","Montserrat","Montserrat","1664","NA","Plymouth","XCD","en","1","","");
INSERT INTO countries VALUES("153","MT","Malta","Malta","356","EU","Valletta","EUR","mt,en","1","","");
INSERT INTO countries VALUES("154","MU","Mauritius","Maurice","230","AF","Port Louis","MUR","en","1","","");
INSERT INTO countries VALUES("155","MV","Maldives","Maldives","960","AS","Malé","MVR","dv","1","","");
INSERT INTO countries VALUES("156","MW","Malawi","Malawi","265","AF","Lilongwe","MWK","en,ny","1","","");
INSERT INTO countries VALUES("157","MX","Mexico","México","52","NA","Mexico City","MXN","es","1","","");
INSERT INTO countries VALUES("158","MY","Malaysia","Malaysia","60","AS","Kuala Lumpur","MYR","ms","1","","");
INSERT INTO countries VALUES("159","MZ","Mozambique","Moçambique","258","AF","Maputo","MZN","pt","1","","");
INSERT INTO countries VALUES("160","NA","Namibia","Namibia","264","AF","Windhoek","NAD,ZAR","en,af","1","","");
INSERT INTO countries VALUES("161","NC","New Caledonia","Nouvelle-Calédonie","687","OC","Nouméa","XPF","fr","1","","");
INSERT INTO countries VALUES("162","NE","Niger","Niger","227","AF","Niamey","XOF","fr","1","","");
INSERT INTO countries VALUES("163","NF","Norfolk Island","Norfolk Island","672","OC","Kingston","AUD","en","1","","");
INSERT INTO countries VALUES("164","NG","Nigeria","Nigeria","234","AF","Abuja","NGN","en","1","","");
INSERT INTO countries VALUES("165","NI","Nicaragua","Nicaragua","505","NA","Managua","NIO","es","1","","");
INSERT INTO countries VALUES("166","NL","Netherlands","Nederland","31","EU","Amsterdam","EUR","nl","1","","");
INSERT INTO countries VALUES("167","NO","Norway","Norge","47","EU","Oslo","NOK","no,nb,nn","1","","");
INSERT INTO countries VALUES("168","NP","Nepal","नपल","977","AS","Kathmandu","NPR","ne","1","","");
INSERT INTO countries VALUES("169","NR","Nauru","Nauru","674","OC","Yaren","AUD","en,na","1","","");
INSERT INTO countries VALUES("170","NU","Niue","Niuē","683","OC","Alofi","NZD","en","1","","");
INSERT INTO countries VALUES("171","NZ","New Zealand","New Zealand","64","OC","Wellington","NZD","en,mi","1","","");
INSERT INTO countries VALUES("172","OM","Oman","عمان","968","AS","Muscat","OMR","ar","1","","");
INSERT INTO countries VALUES("173","PA","Panama","Panamá","507","NA","Panama City","PAB,USD","es","1","","");
INSERT INTO countries VALUES("174","PE","Peru","Perú","51","SA","Lima","PEN","es","1","","");
INSERT INTO countries VALUES("175","PF","French Polynesia","Polynésie française","689","OC","Papeetē","XPF","fr","1","","");
INSERT INTO countries VALUES("176","PG","Papua New Guinea","Papua Niugini","675","OC","Port Moresby","PGK","en","1","","");
INSERT INTO countries VALUES("177","PH","Philippines","Pilipinas","63","AS","Manila","PHP","en","1","","");
INSERT INTO countries VALUES("178","PK","Pakistan","Pakistan","92","AS","Islamabad","PKR","en,ur","1","","");
INSERT INTO countries VALUES("179","PL","Poland","Polska","48","EU","Warsaw","PLN","pl","1","","");
INSERT INTO countries VALUES("180","PM","Saint Pierre and Miquelon","Saint-Pierre-et-Miquelon","508","NA","Saint-Pierre","EUR","fr","1","","");
INSERT INTO countries VALUES("181","PN","Pitcairn Islands","Pitcairn Islands","64","OC","Adamstown","NZD","en","1","","");
INSERT INTO countries VALUES("182","PR","Puerto Rico","Puerto Rico","1787,1939","NA","San Juan","USD","es,en","1","","");
INSERT INTO countries VALUES("183","PS","Palestine","فلسطين","970","AS","Ramallah","ILS","ar","1","","");
INSERT INTO countries VALUES("184","PT","Portugal","Portugal","351","EU","Lisbon","EUR","pt","1","","");
INSERT INTO countries VALUES("185","PW","Palau","Palau","680","OC","Ngerulmud","USD","en","1","","");
INSERT INTO countries VALUES("186","PY","Paraguay","Paraguay","595","SA","Asunción","PYG","es,gn","1","","");
INSERT INTO countries VALUES("187","QA","Qatar","قطر","974","AS","Doha","QAR","ar","1","","");
INSERT INTO countries VALUES("188","RE","Réunion","La Réunion","262","AF","Saint-Denis","EUR","fr","1","","");
INSERT INTO countries VALUES("189","RO","Romania","România","40","EU","Bucharest","RON","ro","1","","");
INSERT INTO countries VALUES("190","RS","Serbia","Србија","381","EU","Belgrade","RSD","sr","1","","");
INSERT INTO countries VALUES("191","RU","Russia","Россия","7","EU","Moscow","RUB","ru","1","","");
INSERT INTO countries VALUES("192","RW","Rwanda","Rwanda","250","AF","Kigali","RWF","rw,en,fr","1","","");
INSERT INTO countries VALUES("193","SA","Saudi Arabia","العربية السعودية","966","AS","Riyadh","SAR","ar","1","","");
INSERT INTO countries VALUES("194","SB","Solomon Islands","Solomon Islands","677","OC","Honiara","SBD","en","1","","");
INSERT INTO countries VALUES("195","SC","Seychelles","Seychelles","248","AF","Victoria","SCR","fr,en","1","","");
INSERT INTO countries VALUES("196","SD","Sudan","السودان","249","AF","Khartoum","SDG","ar,en","1","","");
INSERT INTO countries VALUES("197","SE","Sweden","Sverige","46","EU","Stockholm","SEK","sv","1","","");
INSERT INTO countries VALUES("198","SG","Singapore","Singapore","65","AS","Singapore","SGD","en,ms,ta,zh","1","","");
INSERT INTO countries VALUES("199","SH","Saint Helena","Saint Helena","290","AF","Jamestown","SHP","en","1","","");
INSERT INTO countries VALUES("200","SI","Slovenia","Slovenija","386","EU","Ljubljana","EUR","sl","1","","");
INSERT INTO countries VALUES("201","SJ","Svalbard and Jan Mayen","Svalbard og Jan Mayen","4779","EU","Longyearbyen","NOK","no","1","","");
INSERT INTO countries VALUES("202","SK","Slovakia","Slovensko","421","EU","Bratislava","EUR","sk","1","","");
INSERT INTO countries VALUES("203","SL","Sierra Leone","Sierra Leone","232","AF","Freetown","SLL","en","1","","");
INSERT INTO countries VALUES("204","SM","San Marino","San Marino","378","EU","City of San Marino","EUR","it","1","","");
INSERT INTO countries VALUES("205","SN","Senegal","Sénégal","221","AF","Dakar","XOF","fr","1","","");
INSERT INTO countries VALUES("206","SO","Somalia","Soomaaliya","252","AF","Mogadishu","SOS","so,ar","1","","");
INSERT INTO countries VALUES("207","SR","Suriname","Suriname","597","SA","Paramaribo","SRD","nl","1","","");
INSERT INTO countries VALUES("208","SS","South Sudan","South Sudan","211","AF","Juba","SSP","en","1","","");
INSERT INTO countries VALUES("209","ST","São Tomé and Príncipe","São Tomé e Príncipe","239","AF","São Tomé","STN","pt","1","","");
INSERT INTO countries VALUES("210","SV","El Salvador","El Salvador","503","NA","San Salvador","SVC,USD","es","1","","");
INSERT INTO countries VALUES("211","SX","Sint Maarten","Sint Maarten","1721","NA","Philipsburg","ANG","nl,en","1","","");
INSERT INTO countries VALUES("212","SY","Syria","سوريا","963","AS","Damascus","SYP","ar","1","","");
INSERT INTO countries VALUES("213","SZ","Swaziland","Swaziland","268","AF","Lobamba","SZL","en,ss","1","","");
INSERT INTO countries VALUES("214","TC","Turks and Caicos Islands","Turks and Caicos Islands","1649","NA","Cockburn Town","USD","en","1","","");
INSERT INTO countries VALUES("215","TD","Chad","Tchad","235","AF","N\'Djamena","XAF","fr,ar","1","","");
INSERT INTO countries VALUES("216","TF","French Southern Territories","Territoire des Terres australes et antarctiques fr","262","AN","Port-aux-Français","EUR","fr","1","","");
INSERT INTO countries VALUES("217","TG","Togo","Togo","228","AF","Lomé","XOF","fr","1","","");
INSERT INTO countries VALUES("218","TH","Thailand","ประเทศไทย","66","AS","Bangkok","THB","th","1","","");
INSERT INTO countries VALUES("219","TJ","Tajikistan","Тоҷикистон","992","AS","Dushanbe","TJS","tg,ru","1","","");
INSERT INTO countries VALUES("220","TK","Tokelau","Tokelau","690","OC","Fakaofo","NZD","en","1","","");
INSERT INTO countries VALUES("221","TL","East Timor","Timor-Leste","670","OC","Dili","USD","pt","1","","");
INSERT INTO countries VALUES("222","TM","Turkmenistan","Türkmenistan","993","AS","Ashgabat","TMT","tk,ru","1","","");
INSERT INTO countries VALUES("223","TN","Tunisia","تونس","216","AF","Tunis","TND","ar","1","","");
INSERT INTO countries VALUES("224","TO","Tonga","Tonga","676","OC","Nuku\'alofa","TOP","en,to","1","","");
INSERT INTO countries VALUES("225","TR","Turkey","Türkiye","90","AS","Ankara","TRY","tr","1","","");
INSERT INTO countries VALUES("226","TT","Trinidad and Tobago","Trinidad and Tobago","1868","NA","Port of Spain","TTD","en","1","","");
INSERT INTO countries VALUES("227","TV","Tuvalu","Tuvalu","688","OC","Funafuti","AUD","en","1","","");
INSERT INTO countries VALUES("228","TW","Taiwan","臺灣","886","AS","Taipei","TWD","zh","1","","");
INSERT INTO countries VALUES("229","TZ","Tanzania","Tanzania","255","AF","Dodoma","TZS","sw,en","1","","");
INSERT INTO countries VALUES("230","UA","Ukraine","Україна","380","EU","Kyiv","UAH","uk","1","","");
INSERT INTO countries VALUES("231","UG","Uganda","Uganda","256","AF","Kampala","UGX","en,sw","1","","");
INSERT INTO countries VALUES("232","UM","U.S. Minor Outlying Islands","United States Minor Outlying Islands","1","OC","","USD","en","1","","");
INSERT INTO countries VALUES("233","US","United States","United States","1","NA","Washington D.C.","USD,USN,USS","en","1","","");
INSERT INTO countries VALUES("234","UY","Uruguay","Uruguay","598","SA","Montevideo","UYI,UYU","es","1","","");
INSERT INTO countries VALUES("235","UZ","Uzbekistan","O‘zbekiston","998","AS","Tashkent","UZS","uz,ru","1","","");
INSERT INTO countries VALUES("236","VA","Vatican City","Vaticano","39066,379","EU","Vatican City","EUR","it,la","1","","");
INSERT INTO countries VALUES("237","VC","Saint Vincent and the Grenadines","Saint Vincent and the Grenadines","1784","NA","Kingstown","XCD","en","1","","");
INSERT INTO countries VALUES("238","VE","Venezuela","Venezuela","58","SA","Caracas","VES","es","1","","");
INSERT INTO countries VALUES("239","VG","British Virgin Islands","British Virgin Islands","1284","NA","Road Town","USD","en","1","","");
INSERT INTO countries VALUES("240","VI","U.S. Virgin Islands","United States Virgin Islands","1340","NA","Charlotte Amalie","USD","en","1","","");
INSERT INTO countries VALUES("241","VN","Vietnam","Việt Nam","84","AS","Hanoi","VND","vi","1","","");
INSERT INTO countries VALUES("242","VU","Vanuatu","Vanuatu","678","OC","Port Vila","VUV","bi,en,fr","1","","");
INSERT INTO countries VALUES("243","WF","Wallis and Futuna","Wallis et Futuna","681","OC","Mata-Utu","XPF","fr","1","","");
INSERT INTO countries VALUES("244","WS","Samoa","Samoa","685","OC","Apia","WST","sm,en","1","","");
INSERT INTO countries VALUES("245","XK","Kosovo","Republika e Kosovës","377,381,383,386","EU","Pristina","EUR","sq,sr","1","","");
INSERT INTO countries VALUES("246","YE","Yemen","اليَمَن","967","AS","Sana\'a","YER","ar","1","","");
INSERT INTO countries VALUES("247","YT","Mayotte","Mayotte","262","AF","Mamoudzou","EUR","fr","1","","");
INSERT INTO countries VALUES("248","ZA","South Africa","South Africa","27","AF","Pretoria","ZAR","af,en,nr,st,ss,tn,ts,ve,xh,zu","1","","");
INSERT INTO countries VALUES("249","ZM","Zambia","Zambia","260","AF","Lusaka","ZMK","en","1","","");
INSERT INTO countries VALUES("250","ZW","Zimbabwe","Zimbabwe","263","AF","Harare","USD,ZAR,BWP,GBP,AUD,CNY,INR,JP","en,sn,nd","1","","");



DROP TABLE infix_categories;

CREATE TABLE `infix_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE infix_comments;

CREATE TABLE `infix_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE infix_invoice_categories;

CREATE TABLE `infix_invoice_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_ids` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE infix_invoice_category_links;

CREATE TABLE `infix_invoice_category_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_invoice_category_links VALUES("1","Payment Method","","");
INSERT INTO infix_invoice_category_links VALUES("2","Discount Amount","","");
INSERT INTO infix_invoice_category_links VALUES("3","Discount Type","","");
INSERT INTO infix_invoice_category_links VALUES("4","TAX/GST/VAT","","");
INSERT INTO infix_invoice_category_links VALUES("5","Customer","","");
INSERT INTO infix_invoice_category_links VALUES("6","Project","","");
INSERT INTO infix_invoice_category_links VALUES("7","Client","","");
INSERT INTO infix_invoice_category_links VALUES("8","Currency","","");
INSERT INTO infix_invoice_category_links VALUES("9","Recurring Invoice","","");
INSERT INTO infix_invoice_category_links VALUES("10","Invoice Number","","");
INSERT INTO infix_invoice_category_links VALUES("11","Invoice Date","","");
INSERT INTO infix_invoice_category_links VALUES("12","Due Date","","");



DROP TABLE infix_invoice_products;

CREATE TABLE `infix_invoice_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantity` int(11) DEFAULT NULL,
  `price` double(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `infix_invoice_products_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `infix_invoice_products_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `infix_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_invoice_products VALUES("1","1","1","demo text","5","5000.00","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoice_products VALUES("2","2","1","demo text","5","5000.00","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoice_products VALUES("3","3","1","demo text","5","5000.00","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoice_products VALUES("4","4","1","demo text","5","5000.00","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoice_products VALUES("5","5","1","demo text","5","5000.00","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoice_products VALUES("6","6","1","demo text","5","5000.00","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoice_products VALUES("7","7","1","demo text","5","5000.00","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoice_products VALUES("8","8","1","demo text","5","5000.00","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoice_products VALUES("9","9","1","demo text","5","5000.00","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE infix_invoice_settings;

CREATE TABLE `infix_invoice_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax` double(8,2) DEFAULT NULL,
  `tax_type` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AD' COMMENT 'AD = After Discount, BD = Before Discount',
  `prefix` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_invoice_settings VALUES("1","0.00","AD","infix","","");



DROP TABLE infix_invoices;

CREATE TABLE `infix_invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Invoice number Will be Unique',
  `invoice_date` date DEFAULT NULL,
  `invoice_due_date` date DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `recurring_cycle` enum('M','Q','SA','A','OT') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'M=Monthly, Q=Quarterly, SA=Semi Annually, A=Annually, OT=Once Time',
  `is_recurring_invoice` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=No, 1=Yes',
  `payment_status` enum('UP','P','PP','PR') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'UP= UNPAID , P= PAID , PP= PARTIALLY PAID, PR= PROFORMA',
  `partial_paymemt` double(8,2) DEFAULT NULL,
  `invoice_for` enum('P','S','C') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'P=Product, S=Services, C=Customs',
  `discount_type` enum('P','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'P=Percentage, F=Fixed',
  `discount_amount` double(8,2) DEFAULT NULL,
  `tax_percentage` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `private_note` text COLLATE utf8mb4_unicode_ci,
  `public_note` text COLLATE utf8mb4_unicode_ci,
  `terms_note` text COLLATE utf8mb4_unicode_ci,
  `footer_note` text COLLATE utf8mb4_unicode_ci,
  `signature_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_invoices VALUES("1","2","INFIX-1576396981","","2019-12-15","1","1","1","M","1","P","","P","","","","","","","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoices VALUES("2","3","INFIX-1576396981","","2019-12-15","1","1","1","M","1","P","","P","","","","","","","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoices VALUES("3","4","INFIX-1576396981","","2019-12-15","1","1","1","M","1","P","","P","","","","","","","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoices VALUES("4","5","INFIX-1576396981","","2019-12-15","1","1","1","M","1","P","","P","","","","","","","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoices VALUES("5","6","INFIX-1576396981","","2019-12-15","1","1","1","M","1","P","","P","","","","","","","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoices VALUES("6","7","INFIX-1576396981","","2019-12-15","1","1","1","M","1","P","","P","","","","","","","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoices VALUES("7","8","INFIX-1576396981","","2019-12-15","1","1","1","M","1","P","","P","","","","","","","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoices VALUES("8","9","INFIX-1576396981","","2019-12-15","1","1","1","M","1","P","","P","","","","","","","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO infix_invoices VALUES("9","10","INFIX-1576396981","","2019-12-15","1","1","1","M","1","P","","P","","","","","","","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE infix_project;

CREATE TABLE `infix_project` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `active_status` int(11) NOT NULL DEFAULT '1',
  `is_complete` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_project VALUES("1","1576396982","Synchronised value-added budgetarymanagement","Cumque dolorem hic consequatur aliquam ipsa. Porro blanditiis qui eos eum quia. Qui aspernatur fuga ut adipisci. Consequatur earum et quis vitae.","2","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/1.png","3","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("2","1576396982","Expanded multimedia application","Et debitis voluptate est et aut rerum sit. Hic nostrum incidunt voluptatem recusandae. Illo beatae itaque blanditiis dolorem quia sed nam. Quidem eum et pariatur quidem.","3","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/2.png","4","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("3","1576396982","Customer-focused systematic policy","Repellendus earum quo voluptas vitae autem nisi quis. Sint error distinctio et. Est ullam ut libero. Molestiae voluptas ab eos rerum voluptatum.","4","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/3.png","5","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("4","1576396982","Enhanced reciprocal implementation","Ratione rem aut expedita tenetur. Minus nemo iste temporibus ut sed cupiditate sit. Tenetur est quia quo rerum voluptatem illum.","5","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/4.png","1","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("5","1576396982","Organized fault-tolerant monitoring","Molestiae sed eos sunt sed tempora quia soluta. Ea est odit placeat numquam error ut cum. Quia placeat ea sint qui voluptas harum accusantium.","6","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/5.png","2","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("6","1576396982","Visionary local methodology","Labore sunt voluptas sunt autem. Dolorem delectus voluptatem ex exercitationem autem sint. Fuga sit consequuntur libero facere.","7","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/6.png","3","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("7","1576396982","Horizontal actuating leverage","Inventore qui vel quod et eaque. Nisi repellendus error debitis sequi officiis. Sequi quos impedit ducimus cupiditate occaecati quae rerum. Id quam cum cum aut labore cumque magni.","8","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/7.png","4","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("8","1576396982","Persevering cohesive customerloyalty","Aut est qui a sed. Praesentium facere nobis debitis et cumque itaque. Quae et et qui minus modi. Itaque illo numquam sit vero tempore.","9","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/8.png","5","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("9","1576396982","Seamless solution-oriented hardware","Quo tempora dicta rerum blanditiis et. Reprehenderit dolorum eligendi soluta praesentium aut exercitationem. Eaque est reiciendis rerum eum libero impedit.","10","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/9.png","1","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("10","1576396982","Proactive multi-tasking encoding","Vel doloribus rerum cum consequuntur. Cum soluta occaecati aspernatur fugiat minima nostrum delectus ex. Quis hic dolore impedit qui.","2","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/10.png","3","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("11","1576396982","Configurable leadingedge archive","Quis ratione quas eveniet quia. Mollitia aut dolorem et quos voluptas nulla molestiae. Officia ipsam debitis rem aspernatur dolorum est qui pariatur.","3","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/11.png","4","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("12","1576396982","Reactive fault-tolerant artificialintelligence","Sit sed neque quidem quo ut laborum. Reprehenderit iste quis sit eligendi veritatis. Asperiores aut deserunt at culpa est aut officiis veniam. Quo consequatur aperiam ut ut nulla amet.","4","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/12.png","5","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("13","1576396982","Cloned context-sensitive opensystem","Ipsam tenetur rerum praesentium qui. Sit autem voluptates perferendis aperiam facere. Dolores quo odit laboriosam minus. Voluptatum mollitia assumenda ratione possimus minus.","5","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/13.png","1","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("14","1576396982","Quality-focused hybrid hardware","Enim est sunt tempora vel sunt ea. Eum nemo soluta incidunt tenetur fugit. Officia distinctio nisi aliquam velit vero deleniti.","6","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/14.png","2","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("15","1576396982","Digitized mobile data-warehouse","Fuga quae distinctio magni qui sequi dolor. Velit eos omnis dolor maiores sunt et voluptas nesciunt. Ut explicabo voluptate praesentium quae sunt ipsa.","7","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/15.png","3","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("16","1576396982","Cross-group needs-based GraphicalUserInterface","Quaerat rerum soluta sequi. Nulla sunt dolorem optio id recusandae. Consequatur dolores quisquam debitis fugit tempora. Officiis rerum et voluptas esse.","8","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/16.png","4","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("17","1576396982","Phased grid-enabled application","Nemo voluptate vero voluptatum. Architecto repellat voluptatem laudantium molestiae. Eos ut et deserunt nihil vero facilis repudiandae laborum. Accusamus aut aliquam qui alias harum.","9","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/17.png","5","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("18","1576396982","Virtual object-oriented service-desk","Tempore architecto voluptatem consequatur quia soluta et. Ab eum explicabo doloremque aut. Sit quis et est saepe laboriosam non.","10","2019-12-15 00:00:00","2019-12-31 00:00:00","1","public/uploads/projects/18.png","1","","","1","0","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project VALUES("19","1-1576413088","Your Choices","This is a personal Project","10","2019-12-15 00:00:00","2020-01-04 00:00:00","4","public/uploads/projects/70c486e51e955ac6116ee63048550d6d.png","6","","","1","0","2019-12-15 18:31:10","2019-12-15 18:31:28");



DROP TABLE infix_project_category;

CREATE TABLE `infix_project_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT '1',
  `updated_by` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_project_category VALUES("1","LMS","Learning management System","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_category VALUES("2","CMS","Content management System","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_category VALUES("3","UMS","University management System","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_category VALUES("4","wordpress","wordpress site/clients","1","1","1","2019-12-15 18:28:41","2019-12-15 18:28:41");



DROP TABLE infix_project_colors;

CREATE TABLE `infix_project_colors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_project_colors VALUES("1","Orange","#ff5050","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_colors VALUES("2","Green","#4BA90A","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");



DROP TABLE infix_project_tasks;

CREATE TABLE `infix_project_tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `project_id` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  `is_complete` tinyint(4) NOT NULL DEFAULT '0',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT '1',
  `updated_by` int(11) DEFAULT '1',
  `assigned_to` int(11) DEFAULT NULL,
  `completed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_project_tasks VALUES("1","Eos deleniti repellat perferendis dolores. Voluptatum sequi modi ut atque quae quidem animi odit. Consectetur nostrum ratione ad iusto. Qui non incidunt magnam porro.","Et voluptatum aut neque asperiores eius. Esse corrupti numquam ut iure aut qui consectetur. Error repellendus quos facere.","1","1","0","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("2","Reprehenderit qui eveniet libero et. Qui id mollitia perferendis doloremque. Asperiores id dolor minima et et repellat consequatur. Sit non ut et ducimus rerum. A illum fugiat alias veritatis hic.","Voluptate quos doloribus expedita itaque ut consequuntur. Officia id nemo libero aut. Similique incidunt rem est fugiat.","1","1","1","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("3","Non omnis vitae sunt excepturi accusantium ut officia expedita. Eum quis quos ut sint non sequi rerum. Repellat exercitationem voluptatem maiores cupiditate. Officia beatae est vel id.","Facere quos in quasi sit praesentium. Maxime quia qui nisi eum doloribus velit qui. Ut molestiae sed aliquid in. Officiis alias ut aut et reiciendis temporibus voluptatem.","1","1","0","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("4","Consequatur dolorem sed in. Quos sed unde consequatur earum aut labore est id. Eos esse cumque sunt voluptatem nihil ipsa. Doloremque quidem vitae aut praesentium.","Qui saepe quidem dolor nihil provident adipisci. Incidunt et accusantium qui excepturi nihil. Rerum odio aspernatur quia.","2","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("5","Tempora vero harum soluta numquam occaecati. Numquam facere ullam itaque consequatur voluptas. Rerum soluta aut voluptas consequatur omnis nulla. Aut delectus qui iusto odit eos incidunt.","Et voluptas perferendis culpa cumque sapiente facere minima. Corporis sapiente laudantium deleniti dicta. Harum rem et doloribus minima velit sit. Aut corrupti quos fugit quia et maxime.","2","1","0","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("6","Cupiditate distinctio aut quia suscipit est expedita. Libero velit vel qui omnis. Corporis est numquam quis tempore explicabo occaecati iure.","Magni ut iusto aut optio. Temporibus numquam ea quasi. Sapiente qui eveniet sed quas quia ex.","2","1","1","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("7","Sint consectetur tenetur cum amet quia temporibus sed. Autem voluptas adipisci facilis nobis sed quia quasi molestiae. Dolores impedit ut soluta porro nesciunt vel. Omnis sit quo beatae est.","Soluta dolores quasi aliquid quia ab. Minus magnam nulla ex ex. Atque quas est nostrum vero accusamus. Et debitis repellendus dolore provident est corrupti.","2","1","0","1","","2019-12-15","1","1","4","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("8","Modi repellendus sed eaque exercitationem inventore sit numquam. Pariatur est delectus nesciunt ea iste et. Asperiores consequuntur qui expedita cumque similique ex non odio. Ut et doloribus non ut.","Recusandae ipsam quia qui odio fugiat. Aut porro nulla voluptas. Voluptas voluptatibus omnis eaque dolore laborum excepturi qui.","3","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("9","Temporibus voluptatibus nihil rem corporis consequatur quia. Ut aut explicabo placeat doloremque reprehenderit. Voluptas animi repudiandae ad suscipit.","Quod labore voluptatem ipsum necessitatibus rerum autem eos. Dolor corporis quaerat nulla non commodi. Assumenda consectetur et deserunt assumenda. Assumenda rerum nihil ad minus qui ut voluptates.","3","1","0","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("10","Assumenda sit praesentium voluptatem provident hic. Neque quas ea sunt excepturi et. Blanditiis molestiae maxime nulla.","Corporis aliquid accusamus adipisci est et. Sunt esse odio dolores perspiciatis vitae commodi consectetur. Aut suscipit et ea ex. Molestiae et et explicabo et praesentium et quidem.","3","1","1","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("11","Voluptatibus sapiente sint culpa voluptatem ut quas et. Hic ipsam non qui non iste dolorem quod labore. Eum vitae est similique omnis laboriosam consequatur voluptas.","Dolores incidunt sint quia ex tenetur porro. Architecto labore eaque cum delectus earum eos. Non laboriosam et mollitia voluptas.","3","1","0","1","","2019-12-15","1","1","4","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("12","Quia voluptas et ut. Nam sed unde omnis. Quia molestiae culpa nemo dolores sit voluptas. Est quisquam voluptas sit eligendi est minima.","Voluptates aperiam aut at aut. Et nesciunt in odio consequatur molestiae ut. Quis nam corporis dolorem et qui.","3","1","1","1","","2019-12-15","1","1","5","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("13","Rerum nisi praesentium adipisci recusandae nemo officia. Officiis accusantium qui et eaque id deserunt. Ea blanditiis eveniet dolorum minima et saepe in. Est eligendi omnis inventore deleniti.","Voluptate qui assumenda excepturi excepturi accusantium consequuntur mollitia. Aut voluptas aut vitae totam quo animi et est. Earum rerum omnis aspernatur eos consequatur.","4","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("14","Quibusdam sint quia voluptatem asperiores vitae suscipit sequi hic. Error et in iure debitis sint quisquam. Deserunt necessitatibus minima ab dolor recusandae est.","Aliquid labore deserunt quos quisquam necessitatibus consequatur consequatur doloremque. Accusantium eos consequatur nemo sunt nobis nulla animi. Quia consequuntur libero distinctio ratione et.","5","1","0","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("15","Quia sint officiis voluptatum quae odit doloribus. Aut neque dignissimos animi ut consectetur. Est sed neque fugit rem. Recusandae eum quaerat recusandae.","Magnam omnis eum est cum. Odio sit aut sint optio in rerum. Nesciunt beatae tempore molestias. Molestiae quidem ullam deserunt beatae nihil aliquid.","5","1","1","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("16","Ipsam tenetur dicta dignissimos error debitis nulla. Repellendus quia ad consequatur veniam qui inventore possimus. In ex et ut facere itaque iusto. Dicta eum dicta et eum quia reiciendis distinctio.","Quia at necessitatibus eligendi odit expedita dolores. Aliquid ut est maxime. Dolorum libero excepturi repudiandae rem recusandae. Et repellendus quibusdam et hic sed vel saepe.","6","1","0","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("17","Magni quo mollitia qui sit. Mollitia dignissimos quae fugiat enim. Quaerat saepe enim cupiditate ut voluptatem ut. Dolore recusandae id facilis veniam sed doloremque amet.","Quod dolore excepturi sequi quod. Numquam non sint impedit saepe placeat. Fuga aut eligendi doloribus similique. Ut adipisci laboriosam vel eligendi.","6","1","1","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("18","Deserunt inventore numquam soluta. Voluptas id ullam non voluptas est. Natus sequi quam debitis quidem optio et. Mollitia dicta voluptas sed.","Veritatis officia quo eaque dolores. Fuga corrupti sit assumenda autem et voluptatem qui. Sequi sed nemo fugit sapiente commodi nostrum aut.","6","1","0","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("19","Qui et sit dolor recusandae eos blanditiis assumenda voluptas. Provident incidunt iure repellendus dicta. Et cupiditate consequatur perferendis aut aut neque.","Et cumque tenetur deleniti veniam soluta non velit. Ullam repellendus ipsum eveniet ut qui ex.","7","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("20","Sint enim aliquid exercitationem voluptas et autem. Fuga ducimus debitis laborum veniam.","Praesentium rerum qui nihil tempore omnis saepe ut. Ullam accusamus corrupti aut autem laborum non. Reiciendis quia omnis animi qui.","7","1","0","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("21","Non qui et quae fugit nihil dolorum distinctio. Voluptas quod quia aut omnis maiores. Ratione ipsam voluptate perspiciatis quidem.","Excepturi sed vel quo corporis minus. Eos eos magnam quis adipisci. Voluptas consequuntur cumque dolor aliquam nesciunt nobis sunt minima.","7","1","1","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("22","Omnis officiis est at quo distinctio. Molestiae voluptate cumque vel sed error. Excepturi cumque voluptatem quis est odio.","Hic quibusdam esse autem dignissimos. Quia magnam quia est neque excepturi. Labore at ea expedita vero et repellendus. Molestiae dignissimos sit sed consequatur consequatur.","7","1","0","1","","2019-12-15","1","1","4","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("23","Voluptatem qui ex ipsa et delectus praesentium illo. Ut ullam tempora voluptate temporibus sed. Facere autem veniam ut voluptatum nostrum cumque.","Voluptas qui impedit tempora laborum quibusdam. Consequuntur non dolore sed in eligendi. Molestias quis sunt labore minima enim. Nobis quam doloribus enim laudantium dignissimos.","8","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("24","Maxime ad dolorum ut modi enim. Recusandae voluptas repellendus eaque. Nesciunt molestiae minima dolorem neque ut quae.","Consequatur dolorum quia voluptatem dolor architecto. Aut qui voluptatum veritatis quos dolorum. Est deleniti esse qui nobis itaque quidem.","8","1","0","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("25","Voluptatem provident accusamus ipsam et. Rerum ut beatae magnam illo harum ut dolorem. Nihil qui quia consequuntur dolores qui autem accusamus exercitationem.","Animi dolor eveniet maxime tempora omnis. Voluptatem fuga ipsa eum id reiciendis. Aut corporis explicabo ducimus at autem voluptatum.","8","1","1","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("26","Vel eligendi unde recusandae nisi. Iste explicabo fugiat qui delectus tempora eligendi. Consectetur et quod placeat est totam libero. Sint placeat voluptates qui et rerum consectetur.","Error vel voluptatem possimus qui. Sit qui est ut quia occaecati. Vitae expedita nisi molestiae sed saepe.","8","1","0","1","","2019-12-15","1","1","4","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("27","Magni laborum dicta recusandae fugiat aut explicabo. Doloribus odit dolorem doloribus officia. Assumenda minima reiciendis fuga aut. Porro in est ducimus rerum.","Enim repudiandae alias facilis dicta. Vero maxime perspiciatis tempore. Architecto quam enim qui illum ut vel tempore. Nisi maxime est quas.","8","1","1","1","","2019-12-15","1","1","5","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("28","Voluptatem esse error eveniet quae aperiam a quia. Perspiciatis omnis voluptatem rerum. Vitae molestias ut nihil qui minus. Iure non possimus et et voluptas qui in.","Dicta atque occaecati et sed dolore. Autem numquam ex qui ab. Qui aut perspiciatis quo ut libero consequatur dolores.","9","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("29","Cumque et id ut sit esse qui sapiente. Facere minima est illo molestiae vitae quibusdam. Atque corporis nisi voluptatem esse. Ex cumque tempore officia quia ut laborum.","Ipsam qui nemo dolorem illum voluptates sit perferendis assumenda. Sint dolorem voluptas vel soluta.","10","1","0","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("30","Praesentium quaerat veniam ab excepturi libero aperiam. Omnis veritatis soluta placeat suscipit quam voluptate in. Deserunt odio hic ut amet.","Natus aut non sed. Omnis suscipit impedit ea similique eveniet. Quibusdam iste provident eaque quia iste et nisi temporibus. Vitae quam debitis officiis qui consequatur eius.","10","1","1","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("31","Maxime nemo quos libero quia est et officia. Omnis ut et qui aut. Nihil incidunt consectetur aut mollitia ut optio.","Porro et rerum ipsum sunt soluta. In consequatur tenetur dicta libero. Nulla qui doloribus voluptatibus eos.","10","1","0","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("32","Vel illum rerum illum dolore veniam ea molestias. Blanditiis autem temporibus id. Voluptatem assumenda cumque omnis velit. Consequatur earum minima nisi ipsum sed.","Voluptatum vel et quibusdam consectetur mollitia. Quaerat hic sapiente ipsam similique et. Sit sequi incidunt maxime eligendi nam voluptates minus consequatur. Est et minima qui ut cumque omnis.","11","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("33","Error nisi dignissimos ut repellat totam ipsam soluta natus. Eligendi ea qui asperiores eos rerum culpa. Quidem iure ea sequi ullam dolores iure soluta nam. Inventore ea id sed eligendi tempora.","Praesentium ut perspiciatis iusto et libero distinctio. Et commodi tenetur harum voluptatibus molestiae. In vero voluptate officiis.","11","1","0","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("34","Distinctio quibusdam at voluptas est. Veniam architecto aperiam repudiandae nesciunt aut. Consectetur ea voluptatum accusamus sit architecto consequatur similique sunt.","Hic a explicabo et voluptas deserunt nulla omnis officiis. Cum est inventore quo aut architecto et. Adipisci voluptas et iste consectetur. Sed quisquam fugiat aperiam rerum.","11","1","1","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("35","Qui animi qui voluptas quia tempora. Sit qui fugiat excepturi dolores culpa molestiae ipsum. Praesentium sed velit omnis est ducimus est ipsa quasi. Non ratione rem quos dolorum harum.","Voluptatum provident fuga rerum explicabo. Et assumenda qui corporis animi pariatur temporibus earum atque. Earum vero laborum et iste beatae et eos tenetur.","11","1","0","1","","2019-12-15","1","1","4","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("36","Veniam id ut voluptas quibusdam aperiam. Mollitia ut numquam velit eos voluptatibus adipisci. Tempora dolorum ducimus qui ex. Fugiat excepturi officiis maiores magnam voluptatem non.","Vero ullam accusamus explicabo ut. Nostrum numquam dolorem laudantium quis velit corporis ea minima. Illo velit dolorem dolorum non sit rem. Ut ut quidem qui saepe recusandae ut vel neque.","12","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("37","Error nulla quibusdam aliquid modi velit velit quo. Corrupti aut eius velit aliquam voluptas iure officia aspernatur. Quisquam et et neque vitae rem quia.","Laborum tempora magni tenetur vero. Maxime perferendis repellendus enim vitae quo perferendis. Velit porro ipsam nihil voluptate.","12","1","0","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("38","Dolor vel atque sint et. Et sit dolorem cum earum quaerat. Explicabo nostrum rem dolores. Consectetur officiis dignissimos ullam dolor natus est minus.","Sunt commodi accusamus id alias veritatis inventore ad. Eos quia vel dolore blanditiis. Architecto quasi voluptatibus quibusdam sit voluptas.","12","1","1","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("39","Est pariatur ex fuga consequatur quis cumque. Beatae dolorum quae ducimus blanditiis illo quisquam laborum enim. Beatae aut sed quos.","Molestiae quia aut iure ipsam. Eum veniam nihil voluptatem modi et. Exercitationem laborum maiores optio officia sequi laudantium culpa ut. Iste et velit dolores quis dolorem.","12","1","0","1","","2019-12-15","1","1","4","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("40","Culpa est quia eaque dolores excepturi et tempore. Repellat aut saepe dolorum maiores dolores.","Non sunt et qui error est animi in. Molestiae repellat nihil laudantium repudiandae iste dolores.","12","1","1","1","","2019-12-15","1","1","5","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("41","Et quidem consequuntur minus architecto et. Iste vitae ipsam officia et inventore sunt architecto. Distinctio quidem eos et deleniti quia voluptas iusto.","Vitae facilis ipsum blanditiis numquam voluptatem qui. Ipsum magnam nemo officiis vero est fugiat neque. Fuga et et delectus sit. Eos eum qui rem amet sunt.","13","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("42","Saepe dolore fugit fugit repudiandae perferendis. Reprehenderit et repellat molestiae. Nisi doloribus at aut alias est. Iusto nam dolores est qui ab eum nihil voluptas.","Magnam unde et accusantium illum. Fugit perferendis quia ut rerum id ut omnis. Ea perferendis sint consequatur qui inventore nihil officia corporis. Repudiandae quod quo dolorem numquam saepe.","14","1","0","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("43","Qui recusandae ullam quis est tenetur. Sed hic qui ut quae eligendi vitae repellendus porro. Est delectus ut ut rerum ut. Officiis labore error alias molestiae.","Fugit fugiat quis non expedita. Voluptas rerum id ut nihil. In fugit ipsam aut dolore provident aspernatur. Alias aliquam labore hic a possimus.","14","1","1","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("44","Dicta tenetur enim ea consequuntur. Dicta ipsum quod repudiandae quis et consectetur nam. Eveniet non nihil eos accusamus quod cumque amet sequi.","Placeat officia aut nostrum sequi quod. Velit soluta nisi explicabo distinctio ea ea. Sit aut voluptatem sit ut saepe consequatur.","15","1","0","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("45","Libero sit consequuntur eaque natus fugiat molestias. Dolorum consectetur qui enim. Voluptas provident temporibus soluta necessitatibus ut aspernatur.","Consequatur et unde consequatur qui sit aliquid. Fugit non vitae qui natus et. Perferendis omnis velit fuga excepturi.","15","1","1","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("46","Sunt quia quibusdam omnis quidem cumque. Ipsum corrupti eius et enim. Nulla nisi cum blanditiis voluptatum nam provident vel.","Consectetur commodi velit exercitationem sint distinctio. Qui ipsum vitae dolorum. Explicabo dolores enim et.","15","1","0","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("47","Id est provident nihil iusto aspernatur. Distinctio omnis voluptatibus fuga esse mollitia id ut debitis. Et molestias aut aperiam eum.","Officia eius sequi repudiandae error necessitatibus. Illum soluta veniam non voluptas fugit. Est veniam blanditiis voluptatem sed rerum expedita.","16","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("48","Sit itaque corporis hic nihil earum non dignissimos quibusdam. Illo temporibus qui cum ex. Deserunt facere asperiores blanditiis iste eius sunt excepturi.","Alias modi aut totam asperiores nulla. Est provident et quo. Consectetur nobis deleniti unde optio maiores. Quo est tenetur quasi molestiae doloribus asperiores.","16","1","0","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("49","Eos laborum eius tenetur sit libero odio asperiores ab. Atque enim blanditiis velit et autem. Veritatis nostrum quae qui quis saepe aut quo. Quidem quia officia alias qui minus expedita.","Et minima numquam atque corrupti reiciendis. Molestiae omnis quo ut fugit molestias excepturi.","16","1","1","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("50","Sit dolores sed sunt et praesentium quo. Officiis voluptates et aut et ullam. Est voluptates eaque consequatur consequatur ipsa porro perferendis.","Vel magnam veniam ipsam qui quidem. Adipisci omnis sit quia. Modi magni ut vel. Dolorem omnis placeat sequi ea. Nemo ex quia quia quo. Odio pariatur hic nostrum nulla.","16","1","0","1","","2019-12-15","1","1","4","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("51","Reprehenderit quisquam laboriosam praesentium alias et maiores dolorem. Saepe alias fuga doloribus expedita. Dolore minima necessitatibus rerum quis.","Expedita odit autem eos. Est impedit cum quis esse excepturi. Saepe iure accusantium dicta explicabo. Perferendis expedita tempora molestiae qui. Odio est alias voluptatum. Optio ut est aut.","17","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("52","Dolor recusandae perferendis facere suscipit quia necessitatibus nam. Iste eaque debitis perspiciatis labore. Quisquam qui voluptatem repellat veniam. Quo porro qui doloribus numquam debitis quidem.","Rerum consequuntur voluptatum sequi dolor. Voluptatem minus nobis et facere ut. Porro eum blanditiis error ex eum enim. Ut esse sint voluptatem quam.","17","1","0","1","","2019-12-15","1","1","2","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("53","Molestiae eligendi eos cumque provident quia corporis. Commodi omnis accusamus numquam neque voluptatibus recusandae fugiat optio. Eum doloribus autem sit impedit quis vel minus.","Earum ex qui et et fuga. Voluptas ea mollitia possimus. Officiis unde dolorem vel excepturi et. Laborum illum ut molestiae.","17","1","1","1","","2019-12-15","1","1","3","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("54","Delectus in temporibus sit accusamus reprehenderit. Voluptas et deleniti molestiae quidem impedit. Laudantium sapiente officiis est voluptas.","Adipisci dignissimos natus fugiat. Ipsa laborum molestiae aspernatur facere. Et veritatis enim tempora voluptas.","17","1","0","1","","2019-12-15","1","1","4","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("55","Saepe labore debitis animi. Quos incidunt facilis iure.","Non voluptatem voluptas dolor expedita repellendus fugit veniam architecto. Aperiam voluptatum aliquid aut. Quia cumque itaque occaecati quas. Ullam repudiandae ea natus.","17","1","1","1","","2019-12-15","1","1","5","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("56","Dignissimos excepturi et voluptatum voluptatem. Numquam animi quaerat nemo eaque et. Magni expedita quidem enim.","Voluptatem minus accusamus fugiat maiores corrupti in consequuntur. Possimus dolorem ut consequatur quas. Illo tempora repudiandae ea velit eligendi eos quibusdam iure.","18","1","1","1","","2019-12-15","1","1","1","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_project_tasks VALUES("57","Create login Page","Create a login Page as like our template","19","1","1","1","","12/20/2019","1","1","19","","2019-12-15 18:32:32","2019-12-15 18:35:21");
INSERT INTO infix_project_tasks VALUES("58","Create a dashboard","Create a dashboard as like infixedu theme. please make sure design is done or not?","19","1","1","1","public/uploads/tasks/6b0c9bddd9bbac43ded377ba03f6673a.pdf","12/22/2019","1","1","18","","2019-12-15 18:33:23","2019-12-15 18:35:58");
INSERT INTO infix_project_tasks VALUES("59","Admin Panel","Admin can set permission for all kinds of users","19","1","1","1","public/uploads/tasks/dd178eb3234582b9e17ae890a9d7d26b.jpg","12/27/2019","1","1","16","","2019-12-15 18:34:48","2019-12-15 18:35:11");



DROP TABLE infix_project_teams;

CREATE TABLE `infix_project_teams` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE infix_team;

CREATE TABLE `infix_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_team VALUES("1","Team Number 1","Lorem Ipsum is simply dummy text of the printing and typesetting industry. ","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team VALUES("2","Team Number 2","Lorem Ipsum is simply dummy text of the printing and typesetting industry. ","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team VALUES("3","Team Number 3","Lorem Ipsum is simply dummy text of the printing and typesetting industry. ","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team VALUES("4","Team Number 4","Lorem Ipsum is simply dummy text of the printing and typesetting industry. ","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team VALUES("5","Team Number 5","Lorem Ipsum is simply dummy text of the printing and typesetting industry. ","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team VALUES("6","Wordpress Team","Wordpress Team for develop clients projects","1","","","2019-12-15 18:29:31","2019-12-15 18:29:31");



DROP TABLE infix_team_member;

CREATE TABLE `infix_team_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned DEFAULT NULL,
  `team_id` int(10) unsigned DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO infix_team_member VALUES("1","1","1","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("2","1","2","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("3","2","2","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("4","1","3","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("5","2","3","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("6","3","3","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("7","1","4","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("8","2","4","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("9","3","4","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("10","4","4","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("11","1","5","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("12","2","5","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("13","3","5","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("14","4","5","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("15","5","5","1","","","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO infix_team_member VALUES("20","14","6","1","","","2019-12-15 18:29:45","2019-12-15 18:29:45");
INSERT INTO infix_team_member VALUES("21","15","6","1","","","2019-12-15 18:29:45","2019-12-15 18:29:45");
INSERT INTO infix_team_member VALUES("22","16","6","1","","","2019-12-15 18:29:45","2019-12-15 18:29:45");
INSERT INTO infix_team_member VALUES("23","17","6","1","","","2019-12-15 18:29:45","2019-12-15 18:29:45");
INSERT INTO infix_team_member VALUES("24","18","6","1","","","2019-12-15 18:29:45","2019-12-15 18:29:45");
INSERT INTO infix_team_member VALUES("25","19","6","1","","","2019-12-15 18:29:45","2019-12-15 18:29:45");



DROP TABLE infix_tickets;

CREATE TABLE `infix_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `ticket_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `infix_tickets_ticket_id_unique` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE languages;

CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `native` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rtl` tinyint(4) NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO languages VALUES("1","af","Afrikaans","Afrikaans","0","1","","");
INSERT INTO languages VALUES("2","am","Amharic","አማርኛ","0","1","","");
INSERT INTO languages VALUES("3","ar","Arabic","العربية","1","1","","");
INSERT INTO languages VALUES("4","ay","Aymara","Aymar","0","1","","");
INSERT INTO languages VALUES("5","az","Azerbaijani","Azərbaycanca / آذربايجان","0","1","","");
INSERT INTO languages VALUES("6","be","Belarusian","Беларуская","0","1","","");
INSERT INTO languages VALUES("7","bg","Bulgarian","Български","0","1","","");
INSERT INTO languages VALUES("8","bi","Bislama","Bislama","0","1","","");
INSERT INTO languages VALUES("9","bn","Bengali","বাংলা","0","1","","");
INSERT INTO languages VALUES("10","bs","Bosnian","Bosanski","0","1","","");
INSERT INTO languages VALUES("11","ca","Catalan","Català","0","1","","");
INSERT INTO languages VALUES("12","ch","Chamorro","Chamoru","0","1","","");
INSERT INTO languages VALUES("13","cs","Czech","Česky","0","1","","");
INSERT INTO languages VALUES("14","da","Danish","Dansk","0","1","","");
INSERT INTO languages VALUES("15","de","German","Deutsch","0","1","","");
INSERT INTO languages VALUES("16","dv","Divehi","ދިވެހިބަސް","1","1","","");
INSERT INTO languages VALUES("17","dz","Dzongkha","ཇོང་ཁ","0","1","","");
INSERT INTO languages VALUES("18","el","Greek","Ελληνικά","0","1","","");
INSERT INTO languages VALUES("19","en","English","English","0","1","","");
INSERT INTO languages VALUES("20","es","Spanish","Español","0","1","","");
INSERT INTO languages VALUES("21","et","Estonian","Eesti","0","1","","");
INSERT INTO languages VALUES("22","eu","Basque","Euskara","0","1","","");
INSERT INTO languages VALUES("23","fa","Persian","فارسی","1","1","","");
INSERT INTO languages VALUES("24","ff","Peul","Fulfulde","0","1","","");
INSERT INTO languages VALUES("25","fi","Finnish","Suomi","0","1","","");
INSERT INTO languages VALUES("26","fj","Fijian","Na Vosa Vakaviti","0","1","","");
INSERT INTO languages VALUES("27","fo","Faroese","Føroyskt","0","1","","");
INSERT INTO languages VALUES("28","fr","French","Français","0","1","","");
INSERT INTO languages VALUES("29","ga","Irish","Gaeilge","0","1","","");
INSERT INTO languages VALUES("30","gl","Galician","Galego","0","1","","");
INSERT INTO languages VALUES("31","gn","Guarani","Avañe\'ẽ","0","1","","");
INSERT INTO languages VALUES("32","gv","Manx","Gaelg","0","1","","");
INSERT INTO languages VALUES("33","he","Hebrew","עברית","1","1","","");
INSERT INTO languages VALUES("34","hi","Hindi","हिन्दी","0","1","","");
INSERT INTO languages VALUES("35","hr","Croatian","Hrvatski","0","1","","");
INSERT INTO languages VALUES("36","ht","Haitian","Krèyol ayisyen","0","1","","");
INSERT INTO languages VALUES("37","hu","Hungarian","Magyar","0","1","","");
INSERT INTO languages VALUES("38","hy","Armenian","Հայերեն","0","1","","");
INSERT INTO languages VALUES("39","id","Indonesian","Bahasa Indonesia","0","1","","");
INSERT INTO languages VALUES("40","is","Icelandic","Íslenska","0","1","","");
INSERT INTO languages VALUES("41","it","Italian","Italiano","0","1","","");
INSERT INTO languages VALUES("42","ja","Japanese","日本語","0","1","","");
INSERT INTO languages VALUES("43","ka","Georgian","ქართული","0","1","","");
INSERT INTO languages VALUES("44","kg","Kongo","KiKongo","0","1","","");
INSERT INTO languages VALUES("45","kk","Kazakh","Қазақша","0","1","","");
INSERT INTO languages VALUES("46","kl","Greenlandic","Kalaallisut","0","1","","");
INSERT INTO languages VALUES("47","km","Cambodian","ភាសាខ្មែរ","0","1","","");
INSERT INTO languages VALUES("48","ko","Korean","한국어","0","1","","");
INSERT INTO languages VALUES("49","ku","Kurdish","Kurdî / كوردی","1","1","","");
INSERT INTO languages VALUES("50","ky","Kirghiz","Kırgızca / Кыргызча","0","1","","");
INSERT INTO languages VALUES("51","la","Latin","Latina","0","1","","");
INSERT INTO languages VALUES("52","lb","Luxembourgish","Lëtzebuergesch","0","1","","");
INSERT INTO languages VALUES("53","ln","Lingala","Lingála","0","1","","");
INSERT INTO languages VALUES("54","lo","Laotian","ລາວ / Pha xa lao","0","1","","");
INSERT INTO languages VALUES("55","lt","Lithuanian","Lietuvių","0","1","","");
INSERT INTO languages VALUES("56","lu","","","0","1","","");
INSERT INTO languages VALUES("57","lv","Latvian","Latviešu","0","1","","");
INSERT INTO languages VALUES("58","mg","Malagasy","Malagasy","0","1","","");
INSERT INTO languages VALUES("59","mh","Marshallese","Kajin Majel / Ebon","0","1","","");
INSERT INTO languages VALUES("60","mi","Maori","Māori","0","1","","");
INSERT INTO languages VALUES("61","mk","Macedonian","Македонски","0","1","","");
INSERT INTO languages VALUES("62","mn","Mongolian","Монгол","0","1","","");
INSERT INTO languages VALUES("63","ms","Malay","Bahasa Melayu","0","1","","");
INSERT INTO languages VALUES("64","mt","Maltese","bil-Malti","0","1","","");
INSERT INTO languages VALUES("65","my","Burmese","မြန်မာစာ","0","1","","");
INSERT INTO languages VALUES("66","na","Nauruan","Dorerin Naoero","0","1","","");
INSERT INTO languages VALUES("67","nb","","","0","1","","");
INSERT INTO languages VALUES("68","nd","North Ndebele","Sindebele","0","1","","");
INSERT INTO languages VALUES("69","ne","Nepali","नेपाली","0","1","","");
INSERT INTO languages VALUES("70","nl","Dutch","Nederlands","0","1","","");
INSERT INTO languages VALUES("71","nn","Norwegian Nynorsk","Norsk (nynorsk)","0","1","","");
INSERT INTO languages VALUES("72","no","Norwegian","Norsk (bokmål / riksmål)","0","1","","");
INSERT INTO languages VALUES("73","nr","South Ndebele","isiNdebele","0","1","","");
INSERT INTO languages VALUES("74","ny","Chichewa","Chi-Chewa","0","1","","");
INSERT INTO languages VALUES("75","oc","Occitan","Occitan","0","1","","");
INSERT INTO languages VALUES("76","pa","Panjabi / Punjabi","ਪੰਜਾਬੀ / पंजाबी / پنجابي","0","1","","");
INSERT INTO languages VALUES("77","pl","Polish","Polski","0","1","","");
INSERT INTO languages VALUES("78","ps","Pashto","پښتو","1","1","","");
INSERT INTO languages VALUES("79","pt","Portuguese","Português","0","1","","");
INSERT INTO languages VALUES("80","qu","Quechua","Runa Simi","0","1","","");
INSERT INTO languages VALUES("81","rn","Kirundi","Kirundi","0","1","","");
INSERT INTO languages VALUES("82","ro","Romanian","Română","0","1","","");
INSERT INTO languages VALUES("83","ru","Russian","Русский","0","1","","");
INSERT INTO languages VALUES("84","rw","Rwandi","Kinyarwandi","0","1","","");
INSERT INTO languages VALUES("85","sg","Sango","Sängö","0","1","","");
INSERT INTO languages VALUES("86","si","Sinhalese","සිංහල","0","1","","");
INSERT INTO languages VALUES("87","sk","Slovak","Slovenčina","0","1","","");
INSERT INTO languages VALUES("88","sl","Slovenian","Slovenščina","0","1","","");
INSERT INTO languages VALUES("89","sm","Samoan","Gagana Samoa","0","1","","");
INSERT INTO languages VALUES("90","sn","Shona","chiShona","0","1","","");
INSERT INTO languages VALUES("91","so","Somalia","Soomaaliga","0","1","","");
INSERT INTO languages VALUES("92","sq","Albanian","Shqip","0","1","","");
INSERT INTO languages VALUES("93","sr","Serbian","Српски","0","1","","");
INSERT INTO languages VALUES("94","ss","Swati","SiSwati","0","1","","");
INSERT INTO languages VALUES("95","st","Southern Sotho","Sesotho","0","1","","");
INSERT INTO languages VALUES("96","sv","Swedish","Svenska","0","1","","");
INSERT INTO languages VALUES("97","sw","Swahili","Kiswahili","0","1","","");
INSERT INTO languages VALUES("98","ta","Tamil","தமிழ்","0","1","","");
INSERT INTO languages VALUES("99","tg","Tajik","Тоҷикӣ","0","1","","");
INSERT INTO languages VALUES("100","th","Thai","ไทย / Phasa Thai","0","1","","");
INSERT INTO languages VALUES("101","ti","Tigrinya","ትግርኛ","0","1","","");
INSERT INTO languages VALUES("102","tk","Turkmen","Туркмен / تركمن","0","1","","");
INSERT INTO languages VALUES("103","tn","Tswana","Setswana","0","1","","");
INSERT INTO languages VALUES("104","to","Tonga","Lea Faka-Tonga","0","1","","");
INSERT INTO languages VALUES("105","tr","Turkish","Türkçe","0","1","","");
INSERT INTO languages VALUES("106","ts","Tsonga","Xitsonga","0","1","","");
INSERT INTO languages VALUES("107","uk","Ukrainian","Українська","0","1","","");
INSERT INTO languages VALUES("108","ur","Urdu","اردو","1","1","","");
INSERT INTO languages VALUES("109","uz","Uzbek","Ўзбек","0","1","","");
INSERT INTO languages VALUES("110","ve","Venda","Tshivenḓa","0","1","","");
INSERT INTO languages VALUES("111","vi","Vietnamese","Tiếng Việt","0","1","","");
INSERT INTO languages VALUES("112","xh","Xhosa","isiXhosa","0","1","","");
INSERT INTO languages VALUES("113","zh","Chinese","中文","0","1","","");
INSERT INTO languages VALUES("114","zu","Zulu","isiZulu","0","1","","");



DROP TABLE migrations;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations VALUES("1","2014_10_12_000000_create_users_table","1");
INSERT INTO migrations VALUES("2","2014_10_12_100000_create_password_resets_table","1");
INSERT INTO migrations VALUES("3","2018_11_17_060444_create_roles_table","1");
INSERT INTO migrations VALUES("4","2018_11_18_045802_create_sm_base_groups_table","1");
INSERT INTO migrations VALUES("5","2018_11_18_053840_create_sm_base_setups_table","1");
INSERT INTO migrations VALUES("6","2018_11_19_114636_create_sm_visitors_table","1");
INSERT INTO migrations VALUES("7","2018_11_20_095038_create_sm_income_heads_table","1");
INSERT INTO migrations VALUES("8","2018_11_20_101500_create_sm_add_incomes_table","1");
INSERT INTO migrations VALUES("9","2018_11_21_122328_create_sm_payment_methhods_table","1");
INSERT INTO migrations VALUES("10","2018_11_22_052152_create_sm_academic_years_table","1");
INSERT INTO migrations VALUES("11","2018_11_22_110803_create_sm_sessions_table","1");
INSERT INTO migrations VALUES("12","2018_11_24_053633_create_sm_dormitory_lists_table","1");
INSERT INTO migrations VALUES("13","2018_11_24_093425_create_sm_room_types_table","1");
INSERT INTO migrations VALUES("14","2018_11_24_100753_create_sm_room_lists_table","1");
INSERT INTO migrations VALUES("15","2018_11_25_061756_create_sm_routes_table","1");
INSERT INTO migrations VALUES("16","2018_11_25_094419_create_sm_instructions_table","1");
INSERT INTO migrations VALUES("17","2018_11_25_105154_create_sm_question_levels_table","1");
INSERT INTO migrations VALUES("18","2018_11_25_111220_create_sm_question_groups_table","1");
INSERT INTO migrations VALUES("19","2018_11_25_120550_create_sm_question_banks_table","1");
INSERT INTO migrations VALUES("20","2018_11_26_120240_create_sm_human_departments_table","1");
INSERT INTO migrations VALUES("21","2018_11_26_122725_create_sm_hourly_rates_table","1");
INSERT INTO migrations VALUES("22","2018_11_27_060950_create_sm_leave_types_table","1");
INSERT INTO migrations VALUES("23","2018_11_27_064103_create_sm_designations_table","1");
INSERT INTO migrations VALUES("24","2018_11_27_091426_create_sm_leave_requests_table","1");
INSERT INTO migrations VALUES("25","2018_11_27_115802_create_sm_bank_accounts_table","1");
INSERT INTO migrations VALUES("26","2018_11_28_045716_create_sm_expense_heads_table","1");
INSERT INTO migrations VALUES("27","2018_11_28_061415_create_sm_chart_of_accounts_table","1");
INSERT INTO migrations VALUES("28","2018_11_28_092651_create_sm_add_expenses_table","1");
INSERT INTO migrations VALUES("29","2018_11_29_050541_create_sm_complaints_table","1");
INSERT INTO migrations VALUES("30","2018_11_29_074715_create_sm_postal_receives_table","1");
INSERT INTO migrations VALUES("31","2018_11_29_094108_create_sm_postal_dispatches_table","1");
INSERT INTO migrations VALUES("32","2018_11_29_101719_create_sm_phone_call_logs_table","1");
INSERT INTO migrations VALUES("33","2018_12_02_080330_create_sm_schools_table","1");
INSERT INTO migrations VALUES("34","2018_12_03_064012_create_sm_staffs_table","1");
INSERT INTO migrations VALUES("35","2018_12_03_074132_create_sm_staff_attendences_table","1");
INSERT INTO migrations VALUES("36","2018_12_03_100831_create_sm_hr_salary_templates_table","1");
INSERT INTO migrations VALUES("37","2018_12_03_103029_create_sm_hr_payroll_generates_table","1");
INSERT INTO migrations VALUES("38","2018_12_04_050352_create_sm_notice_boards_table","1");
INSERT INTO migrations VALUES("39","2018_12_04_051648_create_sm_send_messages_table","1");
INSERT INTO migrations VALUES("40","2018_12_04_060828_create_sm_events_table","1");
INSERT INTO migrations VALUES("41","2018_12_04_062330_create_sm_holidays_table","1");
INSERT INTO migrations VALUES("42","2018_12_17_111529_create_sm_hr_payroll_earn_deducs_table","1");
INSERT INTO migrations VALUES("43","2018_12_28_054159_create_sm_upload_contents_table","1");
INSERT INTO migrations VALUES("44","2018_12_28_075918_create_sm_content_types_table","1");
INSERT INTO migrations VALUES("45","2019_01_10_050231_create_sm_item_categories_table","1");
INSERT INTO migrations VALUES("46","2019_01_10_050645_create_sm_items_table","1");
INSERT INTO migrations VALUES("47","2019_01_10_054622_create_sm_item_stores_table","1");
INSERT INTO migrations VALUES("48","2019_01_10_070859_create_sm_suppliers_table","1");
INSERT INTO migrations VALUES("49","2019_01_10_112518_create_sm_item_receives_table","1");
INSERT INTO migrations VALUES("50","2019_01_12_104449_create_sm_item_receive_children_table","1");
INSERT INTO migrations VALUES("51","2019_01_16_064247_create_sm_role_permissions_table","1");
INSERT INTO migrations VALUES("52","2019_01_16_065238_create_sm_module_links_table","1");
INSERT INTO migrations VALUES("53","2019_01_16_085738_create_sm_modules_table","1");
INSERT INTO migrations VALUES("54","2019_01_19_094137_create_sm_inventory_payments_table","1");
INSERT INTO migrations VALUES("55","2019_01_21_131008_create_sm_item_sells_table","1");
INSERT INTO migrations VALUES("56","2019_01_22_104243_create_sm_item_sell_children_table","1");
INSERT INTO migrations VALUES("57","2019_01_23_121931_create_sm_item_issues_table","1");
INSERT INTO migrations VALUES("58","2019_01_26_054046_create_sm_sms_gateways_table","1");
INSERT INTO migrations VALUES("59","2019_01_30_122524_create_sm_student_documents_table","1");
INSERT INTO migrations VALUES("60","2019_01_31_052142_create_sm_student_timelines_table","1");
INSERT INTO migrations VALUES("61","2019_01_31_101401_create_sm_question_bank_mu_options_table","1");
INSERT INTO migrations VALUES("62","2019_02_09_050800_create_sm_email_sms_logs_table","1");
INSERT INTO migrations VALUES("63","2019_02_10_125119_create_sm_general_settings_table","1");
INSERT INTO migrations VALUES("64","2019_02_11_064329_create_sm_languages_table","1");
INSERT INTO migrations VALUES("65","2019_02_11_064351_create_sm_date_formats_table","1");
INSERT INTO migrations VALUES("66","2019_02_11_093834_create_sm_user_logs_table","1");
INSERT INTO migrations VALUES("67","2019_02_12_064024_create_sm_email_settings_table","1");
INSERT INTO migrations VALUES("68","2019_02_14_100911_create_sm_payment_gateway_settings_table","1");
INSERT INTO migrations VALUES("69","2019_02_24_124115_create_sm_to_dos_table","1");
INSERT INTO migrations VALUES("70","2019_03_13_054238_create_sm_setup_admins_table","1");
INSERT INTO migrations VALUES("71","2019_03_13_075602_create_sm_admission_queries_table","1");
INSERT INTO migrations VALUES("72","2019_03_14_075324_create_sm_admission_query_followups_table","1");
INSERT INTO migrations VALUES("73","2019_04_04_124508_create_sm_backups_table","1");
INSERT INTO migrations VALUES("74","2019_04_18_093014_create_sm_leave_defines_table","1");
INSERT INTO migrations VALUES("75","2019_04_23_051315_create_sm_weekends_table","1");
INSERT INTO migrations VALUES("76","2019_04_25_164649_create_sm_countries_table","1");
INSERT INTO migrations VALUES("77","2019_04_27_121353_create_sm_language_phrases_table","1");
INSERT INTO migrations VALUES("78","2019_04_28_074534_create_sm_notifications_table","1");
INSERT INTO migrations VALUES("79","2019_04_30_181622_create_continents_table","1");
INSERT INTO migrations VALUES("80","2019_04_30_181730_create_countries_table","1");
INSERT INTO migrations VALUES("81","2019_04_30_184439_create_languages_table","1");
INSERT INTO migrations VALUES("82","2019_05_07_103627_create_sm_currencies_table","1");
INSERT INTO migrations VALUES("83","2019_05_26_095459_create_sm_news_table","1");
INSERT INTO migrations VALUES("84","2019_05_28_111432_create_sm_news_categories_table","1");
INSERT INTO migrations VALUES("85","2019_06_01_113053_create_sm_contact_pages_table","1");
INSERT INTO migrations VALUES("86","2019_06_01_165107_create_sm_contact_messages_table","1");
INSERT INTO migrations VALUES("87","2019_06_10_155041_create_sm_product_purchases_table","1");
INSERT INTO migrations VALUES("88","2019_07_13_172945_create_sm_tenders_table","1");
INSERT INTO migrations VALUES("89","2019_07_15_134450_create_sm_cost_centers_table","1");
INSERT INTO migrations VALUES("90","2019_07_17_163634_create_sm_upcoming_tenders_table","1");
INSERT INTO migrations VALUES("91","2019_07_17_164351_create_sm_compititors_table","1");
INSERT INTO migrations VALUES("92","2019_07_20_154105_create_sm_tender_products_table","1");
INSERT INTO migrations VALUES("93","2019_07_22_180525_create_sm_product_partnumbers_table","1");
INSERT INTO migrations VALUES("94","2019_07_25_125711_create_sm_debit_credits_table","1");
INSERT INTO migrations VALUES("95","2019_07_27_130041_create_sm_activities_table","1");
INSERT INTO migrations VALUES("96","2019_07_27_155832_create_sm_home_page_settings_table","1");
INSERT INTO migrations VALUES("97","2019_07_27_161119_create_sm_background_settings_table","1");
INSERT INTO migrations VALUES("98","2019_07_28_124751_create_sm_item_subcategories_table","1");
INSERT INTO migrations VALUES("99","2019_07_28_200912_create_sm_daily_expenses_table","1");
INSERT INTO migrations VALUES("100","2019_07_29_103526_create_sm_sub_accounts_table","1");
INSERT INTO migrations VALUES("101","2019_07_30_115222_create_sm_expired_tenders_table","1");
INSERT INTO migrations VALUES("102","2019_08_01_144008_create_infix_invoices_table","1");
INSERT INTO migrations VALUES("103","2019_08_01_194631_create_infix_invoice_categories_table","1");
INSERT INTO migrations VALUES("104","2019_08_01_201750_create_infix_invoice_category_links_table","1");
INSERT INTO migrations VALUES("105","2019_08_03_130938_create_infix_invoice_settings_table","1");
INSERT INTO migrations VALUES("106","2019_08_04_163233_create_sm_unit_manages_table","1");
INSERT INTO migrations VALUES("107","2019_08_04_182517_create_sm_brand_manages_table","1");
INSERT INTO migrations VALUES("108","2019_08_04_182653_create_infix_invoice_products_table","1");
INSERT INTO migrations VALUES("109","2019_08_07_165604_create_infix_tickets_table","1");
INSERT INTO migrations VALUES("110","2019_08_07_171530_create_infix_categories_table","1");
INSERT INTO migrations VALUES("111","2019_08_07_174314_create_infix_comments_table","1");
INSERT INTO migrations VALUES("112","2019_08_07_181324_create_sm_tender_statuses_table","1");
INSERT INTO migrations VALUES("113","2019_08_09_103547_create_sm_enlisted_suppliers_table","1");
INSERT INTO migrations VALUES("114","2019_08_09_124629_create_sm_inspecting_departments_table","1");
INSERT INTO migrations VALUES("115","2019_08_27_103959_create_tickets_table","1");
INSERT INTO migrations VALUES("116","2019_08_27_104039_create_categories_table","1");
INSERT INTO migrations VALUES("117","2019_08_27_104112_create_comments_table","1");
INSERT INTO migrations VALUES("118","2019_08_27_110913_create_priorities_table","1");
INSERT INTO migrations VALUES("119","2019_08_27_120833_create_sm_cash_issues_table","1");
INSERT INTO migrations VALUES("120","2019_08_27_144235_create_sm_advanceloans_table","1");
INSERT INTO migrations VALUES("121","2019_08_29_112614_create_sm_quotations_table","1");
INSERT INTO migrations VALUES("122","2019_08_29_160255_create_sm_quotation_products_table","1");
INSERT INTO migrations VALUES("123","2019_09_15_104825_create_sm_investments_table","1");
INSERT INTO migrations VALUES("124","2019_09_15_144215_create_sm_fund_transfers_table","1");
INSERT INTO migrations VALUES("125","2019_10_16_160104_create_sm_time_zones_table","1");
INSERT INTO migrations VALUES("126","2019_11_21_130022_create_infix_project_category_table","1");
INSERT INTO migrations VALUES("127","2019_11_21_142050_create_infix_team_table","1");
INSERT INTO migrations VALUES("128","2019_11_21_143406_create_infix_team_member_table","1");
INSERT INTO migrations VALUES("129","2019_11_21_154741_create_infix_project_table","1");
INSERT INTO migrations VALUES("130","2019_11_21_175409_create_infix_project_colors_table","1");
INSERT INTO migrations VALUES("131","2019_11_23_111311_create_infix_project_teams_table","1");
INSERT INTO migrations VALUES("132","2019_12_10_163510_create_infix_project_tasks_table","1");



DROP TABLE password_resets;

CREATE TABLE `password_resets` (
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE priorities;

CREATE TABLE `priorities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO priorities VALUES("1","Normal","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO priorities VALUES("2","Low","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO priorities VALUES("3","Critical","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO priorities VALUES("4","Urgent","2019-12-15 14:03:12","2019-12-15 14:03:12");



DROP TABLE roles;

CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'System',
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles VALUES("1","Super Admin","System","1","1","1","1","","");
INSERT INTO roles VALUES("2","Customer","System","1","1","1","1","","");
INSERT INTO roles VALUES("3","Staff","System","1","1","1","1","","");



DROP TABLE sm_academic_years;

CREATE TABLE `sm_academic_years` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `year` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `starting_date` date NOT NULL,
  `ending_date` date NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_activities;

CREATE TABLE `sm_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` enum('Edit','Delete','Insert','Inactive','Active') COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_id` int(11) DEFAULT NULL,
  `model_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `author_mode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_data` text COLLATE utf8mb4_unicode_ci,
  `new_data` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_activities VALUES("1","\"asd\" has been added.","Insert","11","SmQuotation","1","users","{\"id\":11,\"quotation_type\":\"equipment\",\"title\":\"asd\",\"number\":\"asd\",\"date\":\"2019-12-04\",\"reference\":\"asd\",\"customer_id\":5,\"customer_name\":\"Darwin Kris\",\"vendor_id\":2,\"vendor_name\":\"kianna.morar\",\"amount\":3840,\"discount_amount\":0,\"discount_type\":\"A\",\"tax_amount\":null,\"payment_status\":\"UP\",\"partial_paymemt\":null,\"note\":\"\\\"asd\\\" has been added.\",\"description\":null,\"private_note\":null,\"public_note\":null,\"terms_note\":null,\"footer_note\":null,\"signature_person\":null,\"signature_company\":null,\"is_approved\":1,\"active_status\":1,\"created_by\":\"1\",\"updated_by\":\"1\",\"created_at\":\"2019-12-15 16:43:45\",\"updated_at\":\"2019-12-15 16:43:45\",\"model_name\":\"SmQuotation\"}","","2019-12-15 16:43:45","2019-12-15 16:43:45");
INSERT INTO sm_activities VALUES("2","\"quotation No & Product Id 1\" has been added.","Insert","4","SmQuotationProducts","1","users","{\"id\":4,\"quotation_id\":11,\"product_id\":1,\"product_model\":\"sds\",\"qnt\":3,\"unit_price\":1280,\"created_at\":\"2019-12-15 16:43:45\",\"updated_at\":\"2019-12-15 16:43:45\",\"note\":\"\\\"quotation No & Product Id 1\\\" has been added.\",\"model_name\":\"SmQuotationProducts\"}","","2019-12-15 16:43:45","2019-12-15 16:43:45");
INSERT INTO sm_activities VALUES("3","\"sadsad\" has been added.","Insert","12","SmQuotation","1","users","{\"id\":12,\"quotation_type\":\"equipment\",\"title\":\"sadsad\",\"number\":\"11576569139\",\"date\":\"2019-12-17\",\"reference\":\"asdsd\",\"customer_id\":3,\"customer_name\":\"Davion Runolfsson\",\"vendor_id\":2,\"vendor_name\":\"kianna.morar\",\"amount\":8960,\"discount_amount\":0,\"discount_type\":\"A\",\"tax_amount\":null,\"payment_status\":\"UP\",\"partial_paymemt\":null,\"note\":\"\\\"sadsad\\\" has been added.\",\"description\":null,\"private_note\":null,\"public_note\":null,\"terms_note\":null,\"footer_note\":null,\"signature_person\":null,\"signature_company\":null,\"is_approved\":1,\"active_status\":1,\"created_by\":\"1\",\"updated_by\":\"1\",\"created_at\":\"2019-12-17 13:54:23\",\"updated_at\":\"2019-12-17 13:54:23\",\"model_name\":\"SmQuotation\"}","","2019-12-17 13:54:23","2019-12-17 13:54:23");
INSERT INTO sm_activities VALUES("4","\"quotation No & Product Id 1\" has been added.","Insert","5","SmQuotationProducts","1","users","{\"id\":5,\"quotation_id\":12,\"product_id\":1,\"product_model\":\"1\",\"qnt\":1,\"unit_price\":1280,\"created_at\":\"2019-12-17 13:54:23\",\"updated_at\":\"2019-12-17 13:54:23\",\"note\":\"\\\"quotation No & Product Id 1\\\" has been added.\",\"model_name\":\"SmQuotationProducts\"}","","2019-12-17 13:54:23","2019-12-17 13:54:23");
INSERT INTO sm_activities VALUES("5","\"quotation No & Product Id 2\" has been added.","Insert","6","SmQuotationProducts","1","users","{\"id\":6,\"quotation_id\":12,\"product_id\":2,\"product_model\":\"qdsas\",\"qnt\":3,\"unit_price\":2560,\"created_at\":\"2019-12-17 13:54:23\",\"updated_at\":\"2019-12-17 13:54:23\",\"note\":\"\\\"quotation No & Product Id 2\\\" has been added.\",\"model_name\":\"SmQuotationProducts\"}","","2019-12-17 13:54:23","2019-12-17 13:54:23");



DROP TABLE sm_add_expenses;

CREATE TABLE `sm_add_expenses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_head_id` tinyint(4) DEFAULT NULL,
  `expense_sub_head_id` tinyint(4) DEFAULT NULL,
  `account_id` tinyint(4) DEFAULT NULL,
  `payment_method_id` tinyint(4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` double(8,2) NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cost_center_id` int(11) DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 pending, 1 approved, 2 cancelled',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_add_incomes;

CREATE TABLE `sm_add_incomes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_head_id` tinyint(4) DEFAULT NULL,
  `account_id` tinyint(4) DEFAULT NULL,
  `payment_method_id` tinyint(4) DEFAULT NULL,
  `income_sub_head_id` tinyint(4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` double(8,2) NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_admission_queries;

CREATE TABLE `sm_admission_queries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `next_follow_up_date` date DEFAULT NULL,
  `assigned` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` tinyint(4) DEFAULT NULL,
  `source` tinyint(4) DEFAULT NULL,
  `class` tinyint(4) DEFAULT NULL,
  `no_of_child` tinyint(4) DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_admission_query_followups;

CREATE TABLE `sm_admission_query_followups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admission_query_id` tinyint(4) NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci,
  `note` text COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_advanceloans;

CREATE TABLE `sm_advanceloans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sm_advanceloans_staff_id_foreign` (`staff_id`),
  CONSTRAINT `sm_advanceloans_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `sm_staffs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_background_settings;

CREATE TABLE `sm_background_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_background_settings VALUES("1","Dashboard Background","image","public/backEnd/img/body-bg.jpg","","1","","2019-12-17 14:01:27");
INSERT INTO sm_background_settings VALUES("2","Login Background","image","public/backEnd/img/login-bg.jpg","","1","","");



DROP TABLE sm_backups;

CREATE TABLE `sm_backups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` tinyint(4) DEFAULT NULL COMMENT '0=Database, 1=File, 2=Image',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_backups VALUES("1","Backup_17_12_2019_02:08Images.zip","/Applications/MAMP/htdocs/business_erp/public/Backup_17_12_2019_02:08Images.zip","1","1","1","1","1","2019-12-17 14:08:36","2019-12-17 14:08:36");



DROP TABLE sm_bank_accounts;

CREATE TABLE `sm_bank_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` double(10,2) DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `school_id` int(11) DEFAULT '1',
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'User' COMMENT 'User can edit, delete, System can edit',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_base_groups;

CREATE TABLE `sm_base_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_base_groups VALUES("1","Gender","1","1","1","1","","");
INSERT INTO sm_base_groups VALUES("2","Religion","1","1","1","1","","");
INSERT INTO sm_base_groups VALUES("3","Blood Group","1","1","1","1","","");



DROP TABLE sm_base_setups;

CREATE TABLE `sm_base_setups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `base_group_id` tinyint(4) NOT NULL,
  `base_setup_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_base_setups VALUES("1","1","Male","1","1","","","","");
INSERT INTO sm_base_setups VALUES("2","1","Female","1","1","","","","");
INSERT INTO sm_base_setups VALUES("3","1","Others","1","1","","","","");
INSERT INTO sm_base_setups VALUES("4","2","Islam","1","1","","","","");
INSERT INTO sm_base_setups VALUES("5","2","Hinduism","1","1","","","","");
INSERT INTO sm_base_setups VALUES("6","2","Sikhism","1","1","","","","");
INSERT INTO sm_base_setups VALUES("7","2","Buddhism","1","1","","","","");
INSERT INTO sm_base_setups VALUES("8","2","Protestantism","1","1","","","","");
INSERT INTO sm_base_setups VALUES("9","3","A+","1","1","","","","");
INSERT INTO sm_base_setups VALUES("10","3","O+","1","1","","","","");
INSERT INTO sm_base_setups VALUES("11","3","B+","1","1","","","","");
INSERT INTO sm_base_setups VALUES("12","3","AB+","1","1","","","","");
INSERT INTO sm_base_setups VALUES("13","3","A-","1","1","","","","");
INSERT INTO sm_base_setups VALUES("14","3","O-","1","1","","","","");
INSERT INTO sm_base_setups VALUES("15","3","B-","1","1","","","","");
INSERT INTO sm_base_setups VALUES("16","3","AB-","1","1","","","","");
INSERT INTO sm_base_setups VALUES("17","2","kristan","1","1","","","2019-12-17 14:04:10","2019-12-17 14:04:10");



DROP TABLE sm_brand_manages;

CREATE TABLE `sm_brand_manages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_cash_issues;

CREATE TABLE `sm_cash_issues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `is_return` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 means not return, 1 means returned',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_chart_of_accounts;

CREATE TABLE `sm_chart_of_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `head` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'E = expense, I = income',
  `is_daily_expense_head` int(11) NOT NULL DEFAULT '0',
  `active_status` int(11) DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_chart_of_accounts VALUES("1","Wages from labor","I","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("2","Capital from labor","I","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("3","Rental income","I","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("4","Windfall income","I","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("5","Capital gains","I","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("6","Partnership income","I","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("7","Interest","I","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("8","Telephone Expenses","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("9","Travelling Expenses","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("10","Office Equipment and Supplies","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("11","Utility Expenses","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("12","Property Tax","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("13","Legal Expenses","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("14","Bank Charges","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("15","Repair and Maintenance Expenses","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("16","Insurance Expenses ","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("17","Advertising Expenses","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("18","Entertainment Expenses","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("19","Sales Expenses","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("20","Freight in Cost","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("21","Freight out Cost","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("22","Product Cost","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("23","Rental Cost","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_chart_of_accounts VALUES("24","Depreciation Expenses","E","0","1","1","1","2019-12-15 14:02:58","2019-12-15 14:02:58");



DROP TABLE sm_compititors;

CREATE TABLE `sm_compititors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tender_id` int(11) DEFAULT NULL,
  `lowest_bid` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 no, 1 yes',
  `company_id` int(11) DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_bid_amount` decimal(20,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=501 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_compititors VALUES("1","1","0","","Amet dolor. Ltd.","Hic facilis voluptatem.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("2","1","0","","Cupiditate cum. Ltd.","Fugiat dolore rerum.","1283.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("3","1","0","","Et consequatur est. Ltd.","Dolorem sed culpa.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("4","1","0","","Eum ut quasi. Ltd.","Quas laudantium officia.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("5","1","0","","Molestiae iusto molestiae. Ltd.","Deserunt ab et quas.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("6","1","0","","Sunt commodi quaerat. Ltd.","Aut eius cumque est.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("7","1","0","","Expedita est non. Ltd.","Eius ipsum eveniet beatae.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("8","1","0","","Repudiandae aut explicabo. Ltd.","Ipsum ut reprehenderit quam.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("9","1","0","","Et vel cum. Ltd.","In et distinctio.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("10","1","0","","Nesciunt est. Ltd.","Fugit quaerat sit autem.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("11","1","0","","Quia et. Ltd.","Impedit iste quia.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("12","1","0","","Est eum asperiores. Ltd.","Esse laborum mollitia pariatur.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("13","1","0","","Magnam deleniti. Ltd.","Laboriosam asperiores eveniet.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("14","1","0","","Occaecati labore. Ltd.","Corrupti dolor qui.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("15","1","0","","Sunt doloremque tempore. Ltd.","Rerum ut sit.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("16","1","0","","Itaque quasi adipisci. Ltd.","Inventore voluptatem.","1283.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("17","1","0","","Nobis saepe. Ltd.","Ipsa error sit quasi.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("18","1","0","","Aut at eos. Ltd.","Enim magni sit.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("19","1","0","","Culpa reiciendis. Ltd.","Animi ullam consectetur sequi beatae.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("20","1","0","","Qui qui. Ltd.","Id odit.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("21","1","0","","Vel autem. Ltd.","Eaque rerum rerum iure.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("22","1","0","","Repellat aut. Ltd.","Vel inventore eveniet distinctio.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("23","1","0","","Aut aperiam iure. Ltd.","Optio a sint amet.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("24","1","0","","Pariatur tenetur. Ltd.","Non consequatur et.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("25","1","0","","Saepe explicabo rerum. Ltd.","Aperiam sit accusamus itaque.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("26","1","0","","Dolores quis. Ltd.","Dolorem incidunt nostrum tempore.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("27","1","0","","Ducimus tempora doloribus. Ltd.","Est et ex.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("28","1","0","","Veritatis ut ipsum. Ltd.","Commodi possimus.","1265.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("29","1","0","","Debitis id ipsa. Ltd.","Distinctio iste.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("30","1","0","","Aliquid enim. Ltd.","Sit accusantium distinctio quia.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("31","1","0","","Aut dicta. Ltd.","Impedit commodi consequuntur.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("32","1","0","","Sunt minima atque. Ltd.","Illum architecto iusto corporis.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("33","1","0","","Nemo qui iusto. Ltd.","Eum molestiae quia mollitia.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("34","1","0","","Ipsam deleniti aut. Ltd.","Eum rem nam.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("35","1","0","","Commodi eaque optio. Ltd.","Officiis reiciendis repellendus sed.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("36","1","0","","Cum cupiditate aut. Ltd.","Rerum itaque eum.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("37","1","0","","Velit quia ea. Ltd.","Voluptas iusto eum.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("38","1","0","","Architecto officia. Ltd.","Laborum alias ab.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("39","1","0","","Omnis dolorem atque. Ltd.","Tenetur molestiae laudantium.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("40","1","0","","Possimus voluptates fugit. Ltd.","Perspiciatis consectetur debitis.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("41","1","0","","Quod dolorem. Ltd.","Excepturi in voluptatem ipsum.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("42","1","0","","Ut ratione est. Ltd.","Necessitatibus doloribus corporis.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("43","1","0","","Quia aut. Ltd.","Facilis maiores nisi consequatur.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("44","1","0","","Reprehenderit et. Ltd.","Autem fugit similique rerum.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("45","1","0","","Velit praesentium voluptas. Ltd.","Ullam et.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("46","1","0","","Asperiores aut sint. Ltd.","Id inventore non dicta consequatur.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("47","1","0","","A assumenda id. Ltd.","Nobis eum.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("48","1","0","","Iure suscipit ut. Ltd.","Veniam minus et.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("49","1","0","","Vel corporis. Ltd.","Nihil accusantium ducimus.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("50","1","0","","Aut officiis. Ltd.","Aut et placeat minus cumque.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("51","2","0","","Est iusto alias. Ltd.","Assumenda quae non.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("52","2","0","","Saepe est omnis. Ltd.","Tempora perferendis commodi ea.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("53","2","0","","Exercitationem ea. Ltd.","Adipisci vitae corrupti dolorem.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("54","2","0","","Quia optio dolorem. Ltd.","Corporis nisi est porro suscipit.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("55","2","0","","Quis itaque illum. Ltd.","Possimus sequi magni quo.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("56","2","0","","Iure qui. Ltd.","Delectus perferendis non.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("57","2","0","","Animi impedit quia. Ltd.","Voluptatum nisi fuga.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("58","2","0","","Dolore officia. Ltd.","Quos amet et voluptas.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("59","2","0","","Tempore voluptates incidunt. Ltd.","Placeat voluptatibus quae error.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("60","2","0","","Repellat corporis omnis. Ltd.","Voluptatum qui odit earum et.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("61","2","0","","At ducimus esse. Ltd.","Aut et in laudantium.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("62","2","0","","Optio quia. Ltd.","Eum perspiciatis est.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("63","2","0","","Dolore dolore. Ltd.","Omnis eaque.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("64","2","0","","Ea ducimus. Ltd.","Sapiente qui.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("65","2","0","","Nihil et. Ltd.","Nihil esse magni et.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("66","2","0","","Suscipit in voluptatum. Ltd.","Debitis officiis itaque.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("67","2","0","","Sunt voluptatum. Ltd.","Est assumenda.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("68","2","0","","Reprehenderit aliquid. Ltd.","Voluptatem molestiae repellendus id aut.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("69","2","0","","Impedit ut omnis. Ltd.","Dicta error est.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("70","2","0","","Eum quas. Ltd.","Nihil molestias.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("71","2","0","","Dolorem expedita mollitia. Ltd.","Voluptas distinctio sed nisi.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("72","2","0","","Sequi beatae debitis. Ltd.","Mollitia esse animi.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("73","2","0","","Omnis non. Ltd.","Eum voluptatem hic aut.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("74","2","0","","Ipsum repudiandae sint. Ltd.","Dolore labore vel amet veniam.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("75","2","0","","Cumque quia. Ltd.","Ut aut suscipit.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("76","2","0","","Possimus adipisci quod. Ltd.","Maiores occaecati et rerum.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("77","2","0","","Ut qui voluptatem. Ltd.","Voluptatem quidem ipsa tempora.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("78","2","0","","Voluptate aut. Ltd.","Et illo modi voluptate.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("79","2","0","","Dolor omnis. Ltd.","Veniam nihil labore pariatur.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("80","2","0","","Tenetur sit suscipit. Ltd.","Et occaecati error.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("81","2","0","","Dolorum ut qui. Ltd.","Repudiandae aliquid perspiciatis aut.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("82","2","0","","Laborum dolores. Ltd.","In consequatur eligendi et cupiditate.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("83","2","0","","Itaque sit. Ltd.","Tenetur qui assumenda.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("84","2","0","","Ex similique perferendis. Ltd.","Nam unde debitis.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("85","2","0","","Atque tempore. Ltd.","Omnis est quidem.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("86","2","0","","Eum accusamus error. Ltd.","Inventore doloribus quam.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("87","2","0","","Dolores in corrupti. Ltd.","Doloribus accusantium corrupti distinctio.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("88","2","0","","Illum corrupti. Ltd.","Odio illo cupiditate.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("89","2","0","","Omnis voluptatem. Ltd.","Dolore et rem expedita.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("90","2","0","","Harum minima quas. Ltd.","Ullam quis alias quos.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("91","2","0","","Est fugiat. Ltd.","Nulla culpa omnis mollitia.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("92","2","0","","Laboriosam veritatis qui. Ltd.","Dolores velit fuga.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("93","2","0","","Amet hic. Ltd.","Atque officia ducimus.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("94","2","0","","Ea dolore voluptatem. Ltd.","Architecto provident velit.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("95","2","0","","Itaque amet reiciendis. Ltd.","Earum eligendi est.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("96","2","0","","Ut alias. Ltd.","Aut est ut.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("97","2","0","","Iusto occaecati quia. Ltd.","Rerum at quidem.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("98","2","0","","Id enim. Ltd.","Maiores iure ex labore eveniet.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("99","2","0","","Quia accusantium. Ltd.","Est consequatur assumenda.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("100","2","0","","Earum corporis animi. Ltd.","A sit nam et autem.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("101","3","0","","Magni eos. Ltd.","Porro nulla natus id.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("102","3","0","","Maxime qui vel. Ltd.","Ipsa maiores.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("103","3","0","","Qui aliquam cumque. Ltd.","Voluptatem sed odit unde.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("104","3","0","","Modi tempore quis. Ltd.","Delectus perferendis dolores qui.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("105","3","0","","Necessitatibus voluptatem. Ltd.","Nesciunt modi.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("106","3","0","","Eum eos soluta. Ltd.","Ullam rerum soluta sed.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("107","3","0","","Doloremque error iusto. Ltd.","Et est hic iure.","1283.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("108","3","0","","Provident nihil consequatur. Ltd.","Tempora nostrum ut quae.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("109","3","0","","Ipsum accusantium. Ltd.","Qui sed delectus exercitationem.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("110","3","0","","Blanditiis beatae. Ltd.","Sint animi ab.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("111","3","0","","Eos necessitatibus. Ltd.","Quaerat quisquam.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("112","3","0","","Illum excepturi. Ltd.","Soluta dolorum nesciunt rem.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("113","3","0","","Temporibus fugit qui. Ltd.","Accusantium est illo aliquam.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("114","3","0","","Esse accusamus labore. Ltd.","Est est porro.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("115","3","0","","Nostrum magnam ea. Ltd.","Consequatur consequatur et similique.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("116","3","0","","Natus veritatis qui. Ltd.","Vel dolorum quia.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("117","3","0","","Rem eius. Ltd.","Tenetur aperiam numquam temporibus unde.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("118","3","0","","Aut harum. Ltd.","Facilis maxime vitae aspernatur.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("119","3","0","","At rerum aut. Ltd.","Maiores veritatis non blanditiis.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("120","3","0","","Ea sunt. Ltd.","Enim ullam vel consectetur.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("121","3","0","","Blanditiis natus nostrum. Ltd.","Iusto deleniti corporis qui.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("122","3","0","","Possimus minus. Ltd.","Sequi in quas.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("123","3","0","","Laborum incidunt. Ltd.","Non est.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("124","3","0","","Itaque eaque. Ltd.","Sit tenetur nulla.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("125","3","0","","Aut velit consequatur. Ltd.","Doloremque voluptates eaque et.","1283.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("126","3","0","","Sint eum. Ltd.","Omnis incidunt enim facere.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("127","3","0","","Eos similique. Ltd.","Qui pariatur sapiente non.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("128","3","0","","Tenetur pariatur deserunt. Ltd.","Reprehenderit itaque.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("129","3","0","","Dicta aliquid assumenda. Ltd.","Rerum laborum commodi.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("130","3","0","","Rerum aliquid. Ltd.","Sequi veniam ea.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("131","3","0","","Temporibus consequatur soluta. Ltd.","Quia nostrum tempore facilis.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("132","3","0","","Inventore deserunt fuga. Ltd.","Aut veritatis.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("133","3","0","","Ut libero. Ltd.","Aut facere accusamus repellat.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("134","3","0","","Iste aspernatur. Ltd.","Qui temporibus officiis quidem.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("135","3","0","","Quos nostrum. Ltd.","Animi amet rerum.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("136","3","0","","Quas et. Ltd.","Possimus a.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("137","3","0","","Enim aliquid. Ltd.","Adipisci cum quaerat dolorem.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("138","3","0","","Sed quisquam. Ltd.","Nostrum recusandae mollitia ut.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("139","3","0","","Molestiae voluptatem. Ltd.","Earum dolorum odio ea.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("140","3","0","","Id animi porro. Ltd.","Labore repellendus neque ducimus.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("141","3","0","","Magni facere natus. Ltd.","Qui cupiditate perspiciatis.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("142","3","0","","Nemo et. Ltd.","Aut expedita beatae.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("143","3","0","","Et dolorem. Ltd.","Quaerat quas provident fuga.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("144","3","0","","Quia quidem. Ltd.","Debitis doloribus repellendus eligendi.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("145","3","0","","Quia est. Ltd.","Et dolorum aut.","1265.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("146","3","0","","Ad et. Ltd.","Nesciunt at.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("147","3","0","","Molestiae facilis sed. Ltd.","Temporibus aliquam quas accusamus.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("148","3","0","","Ut sequi officiis. Ltd.","Omnis ipsum necessitatibus.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("149","3","0","","Blanditiis qui qui. Ltd.","Ea enim neque aut.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("150","3","0","","Autem minima cum. Ltd.","Id eius alias velit tempore.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("151","4","0","","Animi ad. Ltd.","Mollitia mollitia eum.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("152","4","0","","Est dicta. Ltd.","Fugiat repellendus.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("153","4","0","","Quia saepe alias. Ltd.","Porro mollitia omnis et.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("154","4","0","","Hic ducimus est. Ltd.","Iusto amet doloremque.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("155","4","0","","Officia fugiat atque. Ltd.","Nobis laudantium et.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("156","4","0","","Veniam vitae ipsam. Ltd.","Iure omnis nulla corporis.","1283.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("157","4","0","","Optio in. Ltd.","Dolore aut a rerum.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("158","4","0","","Dolor numquam. Ltd.","Quae suscipit sunt.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("159","4","0","","Porro quod. Ltd.","Porro aut esse.","1266.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("160","4","0","","Quia aut vel. Ltd.","Et dolorum perspiciatis nulla.","1266.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("161","4","0","","Vel amet. Ltd.","Occaecati nobis.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("162","4","0","","Ea sint. Ltd.","Aut qui voluptas perspiciatis.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("163","4","0","","Architecto ullam. Ltd.","Non optio iure.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("164","4","0","","Sit blanditiis corporis. Ltd.","Vel omnis dolore voluptatem.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("165","4","0","","Necessitatibus vero quidem. Ltd.","A eos ipsa incidunt.","1283.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("166","4","0","","Est aperiam placeat. Ltd.","Amet consequatur est ad.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("167","4","0","","Nesciunt voluptatem. Ltd.","Dolor beatae quo doloremque.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("168","4","0","","Officiis facilis tempore. Ltd.","Aliquam officia reiciendis.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("169","4","0","","Ducimus eius dolore. Ltd.","Quae beatae dolor ipsam.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("170","4","0","","In quas. Ltd.","Et rerum dolor.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("171","4","0","","Architecto aut. Ltd.","Qui natus sed.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("172","4","0","","Qui vel et. Ltd.","Et mollitia quo voluptatem.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("173","4","0","","Mollitia reiciendis. Ltd.","Modi iure.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("174","4","0","","Dolore tempora. Ltd.","Vitae tempora qui.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("175","4","0","","Accusantium quia veniam. Ltd.","Dolore dignissimos sed consequuntur.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("176","4","0","","Suscipit sunt vel. Ltd.","Nesciunt iusto non tempore.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("177","4","0","","Qui illo. Ltd.","Assumenda et.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("178","4","0","","Velit doloremque magnam. Ltd.","Suscipit qui et voluptatum.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("179","4","0","","Distinctio sequi. Ltd.","Fugit praesentium sunt nobis.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("180","4","0","","Quam quidem dolor. Ltd.","Ex illo ipsa voluptatem.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("181","4","0","","Voluptate iure unde. Ltd.","Cumque et expedita.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("182","4","0","","Possimus corrupti commodi. Ltd.","Consequatur est temporibus.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("183","4","0","","Voluptas voluptate provident. Ltd.","Ut blanditiis molestiae voluptas.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("184","4","0","","Ut blanditiis. Ltd.","Explicabo accusantium nisi quam.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("185","4","0","","Molestiae qui. Ltd.","Voluptatem totam molestiae.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("186","4","0","","Dolores amet et. Ltd.","Nihil et perspiciatis.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("187","4","0","","Est sint. Ltd.","Sit placeat occaecati minus.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("188","4","0","","Ut et. Ltd.","Voluptas autem quae quaerat.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("189","4","0","","Sunt repellendus. Ltd.","Corrupti dolor maxime rem.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("190","4","0","","Est sint. Ltd.","Repellendus repellendus ut.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("191","4","0","","Voluptas sunt. Ltd.","Quos nulla harum rerum.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("192","4","0","","Reiciendis neque. Ltd.","Autem reiciendis voluptatum.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("193","4","0","","Est eos. Ltd.","Omnis sit est.","1266.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("194","4","0","","Veniam fugiat. Ltd.","Nulla laborum sequi vitae ut.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("195","4","0","","Voluptate omnis aspernatur. Ltd.","Velit ut.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("196","4","0","","Assumenda debitis explicabo. Ltd.","Sapiente numquam sunt.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("197","4","0","","Facilis totam a. Ltd.","Nam quibusdam nemo.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("198","4","0","","Quisquam id. Ltd.","Expedita odio dolorem ea.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("199","4","0","","Facilis nostrum consequatur. Ltd.","Amet nemo aut aliquid.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("200","4","0","","Eos vel aut. Ltd.","Accusamus sunt repellendus et.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("201","5","0","","Ex non. Ltd.","Quo expedita sequi.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("202","5","0","","Ratione in iusto. Ltd.","Necessitatibus ratione ullam.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("203","5","0","","Eaque facilis cupiditate. Ltd.","Impedit eos illo.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("204","5","0","","Non nihil deleniti. Ltd.","Sint voluptatem eum eius dignissimos.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("205","5","0","","Expedita hic inventore. Ltd.","Voluptas similique quod.","1283.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("206","5","0","","Voluptas quia et. Ltd.","Et eligendi placeat quod.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("207","5","0","","Ipsum in voluptate. Ltd.","Pariatur et vitae.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("208","5","0","","Non repellendus qui. Ltd.","Est libero.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("209","5","0","","Libero mollitia. Ltd.","At sequi consectetur odio.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("210","5","0","","Inventore iste. Ltd.","Ad adipisci nulla.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("211","5","0","","Officiis cumque. Ltd.","Soluta consequatur.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("212","5","0","","Cumque corporis aperiam. Ltd.","Dolore nostrum accusamus minus.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("213","5","0","","Error aut. Ltd.","Et et dolorem iure enim.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("214","5","0","","Ullam cum error. Ltd.","Doloribus vero nostrum.","1283.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("215","5","0","","Occaecati praesentium laboriosam. Ltd.","Et eveniet porro.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("216","5","0","","Reiciendis autem omnis. Ltd.","Rerum voluptatem qui vitae.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("217","5","0","","Quasi dolor. Ltd.","Est soluta excepturi.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("218","5","0","","Et nisi dolorum. Ltd.","Autem esse rem.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("219","5","0","","Voluptas nostrum optio. Ltd.","Laboriosam perspiciatis nulla vel.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("220","5","0","","Magni repellendus numquam. Ltd.","Doloremque ut reprehenderit neque.","1283.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("221","5","0","","Et molestias velit. Ltd.","Officiis modi voluptate.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("222","5","0","","Doloremque neque sit. Ltd.","Atque omnis consectetur.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("223","5","0","","Quae est. Ltd.","Excepturi sit nulla quidem.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("224","5","0","","Eveniet dolores. Ltd.","Ut pariatur sit iusto.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("225","5","0","","Totam dolorum corrupti. Ltd.","Sunt minus tempore quis.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("226","5","0","","Corrupti suscipit. Ltd.","Cumque voluptatem.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("227","5","0","","Incidunt maxime. Ltd.","Libero mollitia eos ea.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("228","5","0","","Sit rerum. Ltd.","Quis optio quas nulla.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("229","5","0","","Eius omnis quis. Ltd.","Numquam animi.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("230","5","0","","Rerum sed dolores. Ltd.","Inventore et ad eveniet.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("231","5","0","","Quos earum. Ltd.","Voluptatum corporis ut ut.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("232","5","0","","Error itaque repellendus. Ltd.","Voluptatem nesciunt vel ducimus enim.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("233","5","0","","Molestias illum consequatur. Ltd.","Dignissimos possimus provident voluptatum.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("234","5","0","","Quis in explicabo. Ltd.","Tempore ut et cupiditate voluptatem.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("235","5","0","","Eveniet autem. Ltd.","Ratione illo qui corporis.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("236","5","0","","Voluptate nihil. Ltd.","Doloribus qui natus.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("237","5","0","","Fugit sequi. Ltd.","Est qui corrupti non.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("238","5","0","","Qui consequatur. Ltd.","Veniam voluptate nemo repudiandae.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("239","5","0","","Nam consequatur. Ltd.","Animi et sed.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("240","5","0","","Est et. Ltd.","Officiis ea ea.","1265.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("241","5","0","","Quod sint. Ltd.","Corrupti deserunt.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("242","5","0","","Deleniti non dolor. Ltd.","Officiis voluptatem cum.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("243","5","0","","Eum tempore autem. Ltd.","Aut culpa aut repudiandae.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("244","5","0","","Distinctio omnis. Ltd.","Porro voluptate et.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("245","5","0","","Magni velit. Ltd.","Atque odio consectetur.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("246","5","0","","Qui ut aut. Ltd.","Non perferendis ea et.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("247","5","0","","Eius debitis. Ltd.","Esse maiores voluptatum earum.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("248","5","0","","Illum quo. Ltd.","Animi cumque labore ut.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("249","5","0","","Perspiciatis nihil. Ltd.","Ut sint laborum.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("250","5","0","","Ut harum. Ltd.","Vero asperiores totam.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("251","6","0","","Asperiores aliquid et. Ltd.","Perspiciatis sint illo perspiciatis.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("252","6","0","","Aspernatur fugit. Ltd.","In suscipit voluptas quia.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("253","6","0","","Impedit quam dolorem. Ltd.","Ea dolores ullam.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("254","6","0","","Aperiam et excepturi. Ltd.","Saepe eos tempora.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("255","6","0","","Sapiente nostrum laboriosam. Ltd.","Qui libero nisi ea.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("256","6","0","","Nesciunt maiores dignissimos. Ltd.","Omnis quod ducimus.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("257","6","0","","Impedit modi. Ltd.","Quia magni ut.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("258","6","0","","Occaecati aliquid. Ltd.","Sit eum non alias.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("259","6","0","","Eligendi ut quia. Ltd.","Maxime minus.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("260","6","0","","Corrupti aut et. Ltd.","Aut excepturi ipsum deserunt.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("261","6","0","","Tempore unde quia. Ltd.","Corporis tempora sed.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("262","6","0","","Hic ea. Ltd.","Sed ipsum vero.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("263","6","0","","Consequatur minima. Ltd.","Harum incidunt veritatis.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("264","6","0","","Blanditiis iste. Ltd.","Et in eveniet laborum.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("265","6","0","","Consectetur magni. Ltd.","Qui consectetur aut.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("266","6","0","","Omnis minus. Ltd.","Quisquam voluptate aspernatur.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("267","6","0","","Maxime asperiores. Ltd.","Vel temporibus.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("268","6","0","","Quia adipisci. Ltd.","At inventore officia consequatur.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("269","6","0","","Sed sed eum. Ltd.","Dolor iusto delectus ipsa.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("270","6","0","","Maiores possimus sapiente. Ltd.","Voluptas architecto qui.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("271","6","0","","Suscipit dolorem quidem. Ltd.","Neque aut porro ut.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("272","6","0","","Consequatur eos. Ltd.","Sequi sint aliquid.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("273","6","0","","Et autem. Ltd.","Voluptas aperiam illum.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("274","6","0","","Ipsam sed illo. Ltd.","Facere tempore voluptatem.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("275","6","0","","Itaque perspiciatis. Ltd.","Aspernatur id aut quis voluptatem.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("276","6","0","","Iure cum. Ltd.","Tempore quo dolor facere.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("277","6","0","","Expedita odio. Ltd.","Enim deserunt inventore consequatur.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("278","6","0","","Et et. Ltd.","Atque ut.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("279","6","0","","Quas eos est. Ltd.","Doloribus doloremque.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("280","6","0","","Corporis consectetur est. Ltd.","Non consequuntur.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("281","6","0","","Dolorem aut. Ltd.","Unde dignissimos dicta laborum.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("282","6","0","","Voluptas inventore. Ltd.","Sunt ullam dolorum.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("283","6","0","","Veritatis impedit facere. Ltd.","Voluptatem dolorem aut.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("284","6","0","","Ratione dolorem aut. Ltd.","Et magnam praesentium.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("285","6","0","","Eaque facere mollitia. Ltd.","Deleniti qui.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("286","6","0","","Voluptatem occaecati. Ltd.","Blanditiis voluptates sint.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("287","6","0","","Assumenda dolorem. Ltd.","Aliquid nisi.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("288","6","0","","Asperiores non molestias. Ltd.","Beatae et sit.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("289","6","0","","Est sed aperiam. Ltd.","Provident amet quam facilis nemo.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("290","6","0","","Aperiam ratione. Ltd.","Quo et et sit.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("291","6","0","","Deserunt alias vel. Ltd.","Eius illo quidem.","1265.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("292","6","0","","Porro nostrum. Ltd.","Exercitationem omnis enim voluptatem.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("293","6","0","","Numquam quisquam rem. Ltd.","Ut ipsa nesciunt nemo ut.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("294","6","0","","Facere at ut. Ltd.","Tempora doloribus illo laboriosam.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("295","6","0","","Est eaque quia. Ltd.","Rerum minima quas.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("296","6","0","","Vel ipsa. Ltd.","Enim dolorem doloremque sed sint.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("297","6","0","","Et similique velit. Ltd.","Sequi omnis illum.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("298","6","0","","Eius consectetur. Ltd.","Repudiandae qui quo.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("299","6","0","","Sed eos. Ltd.","A ex.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("300","6","0","","Commodi perferendis. Ltd.","Quae voluptatem aliquam quasi.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("301","7","0","","Saepe architecto et. Ltd.","Distinctio itaque corporis.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("302","7","0","","Molestias qui. Ltd.","Et ut expedita.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("303","7","0","","Distinctio est. Ltd.","Harum exercitationem quo.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("304","7","0","","Fugit itaque reprehenderit. Ltd.","Voluptatem tempore.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("305","7","0","","Labore adipisci autem. Ltd.","Vel nesciunt sapiente velit.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("306","7","0","","Aut et soluta. Ltd.","Reprehenderit ea et voluptate.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("307","7","0","","Itaque provident. Ltd.","Dolore hic commodi ullam.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("308","7","0","","Ab dolorem rerum. Ltd.","Est ad fugit.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("309","7","0","","Optio facilis. Ltd.","Magnam et exercitationem.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("310","7","0","","Esse quasi. Ltd.","Enim distinctio ut.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("311","7","0","","Aspernatur magni. Ltd.","Id ut et.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("312","7","0","","Autem vel esse. Ltd.","Voluptate qui cumque nobis inventore.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("313","7","0","","Reprehenderit quae. Ltd.","Quia in explicabo.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("314","7","0","","Quo dolores enim. Ltd.","Vel molestiae blanditiis molestias.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("315","7","0","","Voluptas reiciendis. Ltd.","Eveniet asperiores eius qui.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("316","7","0","","Aut ullam non. Ltd.","Explicabo fuga officiis sint.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("317","7","0","","Beatae et. Ltd.","Ut et voluptates dolores.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("318","7","0","","Qui sint. Ltd.","Quae omnis beatae molestias.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("319","7","0","","Est id. Ltd.","Ea nesciunt et.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("320","7","0","","Aliquid et quos. Ltd.","Saepe et ipsam maiores.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("321","7","0","","Nostrum omnis saepe. Ltd.","Eos veritatis quidem.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("322","7","0","","Rerum nam reprehenderit. Ltd.","Et fugiat consequatur.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("323","7","0","","Velit qui. Ltd.","Aut atque excepturi.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("324","7","0","","Quo voluptatem. Ltd.","Ab sunt voluptatem.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("325","7","0","","Dolores quod. Ltd.","Autem pariatur.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("326","7","0","","Dolor omnis culpa. Ltd.","Aut in iusto.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("327","7","0","","Ut doloremque. Ltd.","Fugit eius iste.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("328","7","0","","Nostrum quae. Ltd.","Est incidunt nihil.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("329","7","0","","Consectetur aliquam dolorum. Ltd.","Aperiam mollitia molestiae molestiae.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("330","7","0","","Sunt dolorum. Ltd.","Non quam.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("331","7","0","","Consectetur ipsa culpa. Ltd.","Maxime debitis quia alias.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("332","7","0","","Ut provident et. Ltd.","Est culpa doloribus.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("333","7","0","","Aut totam. Ltd.","Nam pariatur aperiam.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("334","7","0","","Quia ea. Ltd.","Itaque id omnis.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("335","7","0","","Voluptatum aut. Ltd.","Mollitia vel fugit omnis.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("336","7","0","","Totam non porro. Ltd.","Quo culpa in repudiandae.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("337","7","0","","Molestiae dolor. Ltd.","Accusantium eligendi aut aut.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("338","7","0","","Atque modi. Ltd.","Debitis similique necessitatibus tempore.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("339","7","0","","Ipsa sunt incidunt. Ltd.","Qui non nihil.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("340","7","0","","Facilis voluptas quia. Ltd.","Nam aspernatur ut.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("341","7","0","","Nobis vero facilis. Ltd.","Asperiores voluptas debitis distinctio consectetur.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("342","7","0","","Quos ut. Ltd.","Impedit quia ad.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("343","7","0","","Est harum in. Ltd.","Iusto veniam.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("344","7","0","","Quidem nam. Ltd.","Nihil deserunt error.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("345","7","0","","Recusandae earum. Ltd.","Dolorum nesciunt et minima consequatur.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("346","7","0","","In vitae corrupti. Ltd.","Earum atque vel praesentium.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("347","7","0","","Commodi veniam. Ltd.","Voluptatem temporibus qui.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("348","7","0","","Et praesentium. Ltd.","Sed sint sit non.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("349","7","0","","Repellendus est neque. Ltd.","Voluptatem distinctio quam.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("350","7","0","","Porro tempore. Ltd.","Accusantium eligendi repellendus ut.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("351","8","0","","Debitis itaque ipsa. Ltd.","Error et natus aut.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("352","8","0","","Quae sed et. Ltd.","Aliquid aut labore suscipit.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("353","8","0","","Quia quia illo. Ltd.","Rerum eligendi odit.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("354","8","0","","Possimus hic. Ltd.","Non velit aut nulla.","1265.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("355","8","0","","Et magnam quia. Ltd.","Quia dolor non.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("356","8","0","","Expedita quia inventore. Ltd.","Iure aut aut.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("357","8","0","","Rerum est. Ltd.","Quod dolor dolores tenetur.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("358","8","0","","Quam minus. Ltd.","Excepturi sunt laudantium vero.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("359","8","0","","Ipsa a aut. Ltd.","Officia possimus aut quia.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("360","8","0","","Quam vel autem. Ltd.","Delectus debitis dolore nesciunt.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("361","8","0","","Laborum nihil corporis. Ltd.","Sed fugiat officiis dolorum.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("362","8","0","","Rem quam. Ltd.","Quidem qui.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("363","8","0","","Iste et. Ltd.","Nemo fuga vitae aspernatur.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("364","8","0","","Harum quasi quia. Ltd.","Alias nulla deserunt enim.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("365","8","0","","Autem sit. Ltd.","Vel fugit.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("366","8","0","","Fuga saepe. Ltd.","Ratione laborum hic voluptatum.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("367","8","0","","Aut saepe incidunt. Ltd.","Magni odit consequatur ut.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("368","8","0","","Expedita est. Ltd.","Non cum veritatis.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("369","8","0","","In incidunt. Ltd.","Nihil ut velit fuga.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("370","8","0","","Corrupti hic. Ltd.","Atque perspiciatis laboriosam.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("371","8","0","","Eum voluptatem. Ltd.","Quia quisquam iste.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("372","8","0","","Incidunt reiciendis ducimus. Ltd.","Eos dolorem officiis delectus.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("373","8","0","","Cum quo. Ltd.","Ea quam dignissimos.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("374","8","0","","Et velit quod. Ltd.","Atque tempora sunt ullam.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("375","8","0","","Aut nobis omnis. Ltd.","Qui quo provident.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("376","8","0","","Illo nobis harum. Ltd.","Eum sint.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("377","8","0","","Et pariatur est. Ltd.","Magnam eum impedit quibusdam.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("378","8","0","","Debitis est. Ltd.","Odio et dolores aut.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("379","8","0","","Sequi laborum. Ltd.","Minus odit.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("380","8","0","","Eius exercitationem hic. Ltd.","Saepe ducimus amet.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("381","8","0","","Quos ea nulla. Ltd.","Dolorem voluptas repudiandae.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("382","8","0","","Nostrum autem dolorem. Ltd.","Placeat minima iste at.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("383","8","0","","Voluptate eos quas. Ltd.","Id similique modi explicabo.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("384","8","0","","Aut illo facere. Ltd.","Deserunt voluptatem corporis consequatur.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("385","8","0","","Eveniet sunt. Ltd.","Ut ipsa quasi sed.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("386","8","0","","Dolorem consequatur. Ltd.","Reiciendis modi voluptatem qui.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("387","8","0","","Consectetur id sint. Ltd.","Eos at unde.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("388","8","0","","Perferendis at nulla. Ltd.","Natus fugiat omnis est.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("389","8","0","","Vel doloremque maxime. Ltd.","Possimus aliquid ipsum.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("390","8","0","","Cum vel. Ltd.","Iusto asperiores explicabo.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("391","8","0","","Quia voluptatem. Ltd.","Aliquam tempore voluptas omnis.","1265.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("392","8","0","","Beatae occaecati. Ltd.","Minima occaecati praesentium.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("393","8","0","","Est eum. Ltd.","Perferendis voluptas et et tempora.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("394","8","0","","Eligendi nobis. Ltd.","Qui et commodi.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("395","8","0","","Aliquam omnis. Ltd.","Et eum.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("396","8","0","","Fugiat minus aut. Ltd.","Voluptas eveniet maiores.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("397","8","0","","Dolorum culpa. Ltd.","Quod animi aliquid.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("398","8","0","","Est dolore qui. Ltd.","Qui odio natus.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("399","8","0","","Quae culpa. Ltd.","Recusandae neque impedit inventore.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("400","8","0","","Voluptatem inventore ut. Ltd.","Tempore asperiores ut velit laboriosam.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("401","9","0","","At commodi aut. Ltd.","Ut eveniet deleniti dolore.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("402","9","0","","Pariatur nulla. Ltd.","Non libero et et.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("403","9","0","","Rerum qui. Ltd.","Doloremque est distinctio.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("404","9","0","","Autem in. Ltd.","Maxime quibusdam.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("405","9","0","","Atque praesentium. Ltd.","Autem sint omnis.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("406","9","0","","Molestias exercitationem officia. Ltd.","Qui quia inventore quasi non.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("407","9","0","","Minima omnis. Ltd.","Cupiditate et omnis ut.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("408","9","0","","Veniam non. Ltd.","Deserunt itaque autem enim.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("409","9","0","","In itaque. Ltd.","Facilis magni fuga quos.","1269.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("410","9","0","","Ut quae omnis. Ltd.","Hic nihil assumenda.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("411","9","0","","Quo id. Ltd.","Voluptas aperiam praesentium quam.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("412","9","0","","Expedita harum illo. Ltd.","Labore ut veritatis.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("413","9","0","","Modi perspiciatis. Ltd.","Dolorem optio voluptatem quia.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("414","9","0","","Quo soluta dignissimos. Ltd.","Alias sit ex.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("415","9","0","","Voluptas adipisci ut. Ltd.","Nostrum debitis delectus optio.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("416","9","0","","Eos laborum. Ltd.","Iusto nihil rerum.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("417","9","0","","Et ipsa. Ltd.","Fugiat aliquid totam.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("418","9","0","","Ut est odit. Ltd.","Labore harum assumenda.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("419","9","0","","Quibusdam sed et. Ltd.","Recusandae reiciendis aut.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("420","9","0","","Est ratione. Ltd.","Et quisquam saepe.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("421","9","0","","Et consequatur. Ltd.","Omnis velit eum et.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("422","9","0","","Eligendi ratione. Ltd.","Magni voluptatem mollitia sunt.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("423","9","0","","Et maxime expedita. Ltd.","Vitae quam quis rerum.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("424","9","0","","Praesentium consequatur rerum. Ltd.","Voluptatibus fuga et.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("425","9","0","","Sunt quos veniam. Ltd.","Quos eum voluptate dicta earum.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("426","9","0","","Placeat illo. Ltd.","Laborum enim eos.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("427","9","0","","Nisi quis sed. Ltd.","Debitis vel nihil architecto.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("428","9","0","","Tenetur aliquam. Ltd.","Recusandae recusandae nobis labore.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("429","9","0","","Minus aut. Ltd.","Quo iure et.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("430","9","0","","Id cupiditate. Ltd.","Est praesentium sunt.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("431","9","0","","Placeat autem. Ltd.","Saepe et rerum.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("432","9","0","","Rerum veritatis eaque. Ltd.","Cumque eligendi occaecati.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("433","9","0","","Quam et iusto. Ltd.","Iure assumenda dolore.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("434","9","0","","Facilis culpa. Ltd.","Atque suscipit alias.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("435","9","0","","In nihil architecto. Ltd.","Ea ipsa quibusdam.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("436","9","0","","Corporis quam voluptas. Ltd.","Rerum sed laudantium.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("437","9","0","","Doloremque vel ea. Ltd.","Quia commodi possimus.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("438","9","0","","Earum et sapiente. Ltd.","Perferendis in cum quibusdam.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("439","9","0","","Quod qui asperiores. Ltd.","Laudantium voluptatem tempora quasi.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("440","9","0","","Corporis sint. Ltd.","Est temporibus quia vero.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("441","9","0","","Esse voluptate repellat. Ltd.","Qui praesentium laudantium.","1281.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("442","9","0","","Dolorem non. Ltd.","In perspiciatis rerum.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("443","9","0","","Qui qui. Ltd.","Ducimus hic ea.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("444","9","0","","Ut nobis sit. Ltd.","Quia eos accusamus.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("445","9","0","","Fugiat animi. Ltd.","Ab qui vero.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("446","9","0","","Quis enim. Ltd.","Culpa totam.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("447","9","0","","Dolores exercitationem. Ltd.","Magnam expedita et.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("448","9","0","","Quas consequatur ab. Ltd.","Nesciunt debitis.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("449","9","0","","Sit occaecati in. Ltd.","Expedita quod iure est.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("450","9","0","","Alias velit earum. Ltd.","Ipsum distinctio omnis nobis.","1279.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("451","10","0","","Et aut libero. Ltd.","Maiores porro quasi.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("452","10","0","","Est voluptas facilis. Ltd.","Et voluptatem dolore excepturi.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("453","10","0","","Eaque fugiat. Ltd.","Esse error blanditiis.","1266.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("454","10","0","","Minima laudantium. Ltd.","Eligendi voluptate eveniet.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("455","10","0","","Non et rem. Ltd.","Ut corrupti qui.","1266.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("456","10","0","","Exercitationem blanditiis eaque. Ltd.","Qui et.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("457","10","0","","Similique sed in. Ltd.","Quas cumque deleniti voluptas.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("458","10","0","","Corrupti nulla culpa. Ltd.","Rerum et quia enim.","1266.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("459","10","0","","Animi accusamus. Ltd.","Molestiae omnis.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("460","10","0","","Sed veritatis. Ltd.","Omnis blanditiis nostrum.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("461","10","0","","Quisquam aspernatur quos. Ltd.","Ipsam asperiores natus deserunt.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("462","10","0","","Voluptas doloremque. Ltd.","Doloremque sed eum vel.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("463","10","0","","Reprehenderit et deserunt. Ltd.","Rerum non est quos.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("464","10","0","","Dolorum quos. Ltd.","Suscipit ut.","1267.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("465","10","0","","Libero labore maxime. Ltd.","Eligendi dolorem perferendis deserunt.","1280.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("466","10","0","","Sit facere. Ltd.","Nobis impedit pariatur aut.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("467","10","0","","Aut a hic. Ltd.","Hic quia sed suscipit perspiciatis.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("468","10","0","","Nisi libero quia. Ltd.","Vel vitae molestiae.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("469","10","0","","Est voluptatum. Ltd.","Nulla eos velit perferendis voluptatem.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("470","10","0","","Velit qui perferendis. Ltd.","Esse odit aut.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("471","10","0","","Ipsum sint consequatur. Ltd.","Maiores consequuntur eius.","1273.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("472","10","0","","Incidunt qui. Ltd.","Quia amet veniam.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("473","10","0","","Aut odio. Ltd.","Est consequuntur illum.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("474","10","0","","Assumenda aliquid voluptatum. Ltd.","Assumenda officiis natus cupiditate.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("475","10","0","","Omnis ut. Ltd.","Aperiam suscipit vel aut.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("476","10","0","","Vitae occaecati rerum. Ltd.","Aliquam perspiciatis ex.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("477","10","0","","Aut voluptatum. Ltd.","Odio dolores fuga.","1278.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("478","10","0","","Animi delectus. Ltd.","Voluptatum quis quam placeat.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("479","10","0","","Reprehenderit eius. Ltd.","Optio officiis accusamus.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("480","10","0","","Quo laboriosam. Ltd.","Beatae ad alias neque.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("481","10","0","","Et eaque. Ltd.","Et ad fugiat.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("482","10","0","","Autem hic repudiandae. Ltd.","Provident est.","1272.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("483","10","0","","Quis velit dignissimos. Ltd.","Sunt cumque nam officia.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("484","10","0","","Voluptas exercitationem laboriosam. Ltd.","Explicabo maxime cumque.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("485","10","0","","Quod consequatur. Ltd.","Enim autem repudiandae.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("486","10","0","","Et numquam consequatur. Ltd.","Quisquam ex aut.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("487","10","0","","Debitis aut. Ltd.","Quae et.","1277.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("488","10","0","","Laboriosam aperiam eum. Ltd.","Enim ducimus fugiat totam.","1271.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("489","10","0","","Voluptatem minus. Ltd.","Non explicabo et est.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("490","10","0","","Quam magni. Ltd.","Iure neque doloremque.","1274.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("491","10","0","","Sed in quia. Ltd.","Sit tempora repudiandae.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("492","10","0","","Dolor enim. Ltd.","Saepe omnis.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("493","10","0","","Sint ea. Ltd.","Voluptatem incidunt minus sapiente.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("494","10","0","","Enim officiis aut. Ltd.","Voluptatem voluptates.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("495","10","0","","Cum maiores. Ltd.","Expedita consequuntur nostrum explicabo rerum.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("496","10","0","","Enim porro. Ltd.","Velit omnis in.","1270.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("497","10","0","","Accusantium vel. Ltd.","Illo ut praesentium.","1282.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("498","10","0","","Deleniti aut accusamus. Ltd.","Quibusdam consequuntur maiores.","1268.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("499","10","0","","Perspiciatis in aut. Ltd.","Optio ut asperiores.","1276.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_compititors VALUES("500","10","0","","Et aliquam. Ltd.","Rerum et asperiores corrupti.","1275.00","2019-12-15","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_complaints;

CREATE TABLE `sm_complaints` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `complaint_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complaint_type` tinyint(4) DEFAULT NULL,
  `complaint_source` tinyint(4) DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `action_taken` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_contact_messages;

CREATE TABLE `sm_contact_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `view_status` tinyint(4) NOT NULL DEFAULT '0',
  `reply_status` tinyint(4) NOT NULL DEFAULT '0',
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_contact_pages;

CREATE TABLE `sm_contact_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_map_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_contact_pages VALUES("1","Contact Us","Have any questions? We’d love to hear from you! Here’s how to get in touch with us.","public/uploads/contactPage/contact.jpg","Learn More About Us","about","56/8 Panthapath, Dhanmondi,Dhaka","Santa monica bullevard","0184113625","Mon to Fri 9am to 6 pm","info@spondonit.com","Send us your query anytime!","23.707310","90.415480","Panthapath, Dhaka","","1","1","1","","");



DROP TABLE sm_content_types;

CREATE TABLE `sm_content_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_cost_centers;

CREATE TABLE `sm_cost_centers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `is_existing_item` tinyint(4) NOT NULL DEFAULT '0',
  `item_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_cost_centers VALUES("1","1","cost center 1","1","0","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_cost_centers VALUES("2","2","cost center 2","1","0","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_cost_centers VALUES("3","3","cost center 3","1","0","","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_countries;

CREATE TABLE `sm_countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `native` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `continent` varchar(255) DEFAULT NULL,
  `capital` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `languages` varchar(255) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE sm_currencies;

CREATE TABLE `sm_currencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_currencies VALUES("1","Leke","ALL","Lek","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_currencies VALUES("2","Dollars","USD","$","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_currencies VALUES("3","Afghanis","AFN","؋","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("4","Pesos","ARS","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("5","Guilders","AWG","ƒ","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("6","Dollars","AUD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("7","New Manats","AZN","ман","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("8","Dollars","BSD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("9","Dollars","BBD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("10","Rubles","BYR","p.","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("11","Euro","EUR","€","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("12","Dollars","BZD","BZ$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("13","Dollars","BMD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("14","Bolivianos","BOB","$b","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("15","Convertible Marka","BAM","KM","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("16","Pula","BWP","P","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("17","Leva","BGN","лв","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("18","Reais","BRL","R$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("19","Pounds","GBP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("20","Dollars","BND","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("21","Riels","KHR","៛","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("22","Dollars","CAD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("23","Dollars","KYD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("24","Pesos","CLP","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("25","Yuan Renminbi","CNY","¥","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("26","Pesos","COP","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("27","Colón","CRC","₡","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("28","Kuna","HRK","kn","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("29","Pesos","CUP","₱","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("30","Koruny","CZK","Kč","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("31","Kroner","DKK","kr","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("32","Pesos","DOP ","RD$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("33","Dollars","XCD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("34","Pounds","EGP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("35","Colones","SVC","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("36","Pounds","FKP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("37","Dollars","FJD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("38","Cedis","GHC","¢","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("39","Pounds","GIP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("40","Quetzales","GTQ","Q","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("41","Pounds","GGP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("42","Dollars","GYD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("43","Lempiras","HNL","L","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("44","Dollars","HKD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("45","Forint","HUF","Ft","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("46","Kronur","ISK","kr","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("47","Rupees","INR","₹","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("48","Rupiahs","IDR","Rp","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("49","Rials","IRR","﷼","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("50","Pounds","IMP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("51","New Shekels","ILS","₪","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("52","Dollars","JMD","J$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("53","Yen","JPY","¥","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("54","Pounds","JEP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("55","Tenge","KZT","лв","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("56","Won","KPW","₩","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("57","Won","KRW","₩","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("58","Soms","KGS","лв","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("59","Kips","LAK","₭","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("60","Lati","LVL","Ls","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("61","Pounds","LBP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("62","Dollars","LRD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("63","Switzerland Francs","CHF","CHF","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("64","Litai","LTL","Lt","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("65","Denars","MKD","ден","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("66","Ringgits","MYR","RM","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("67","Rupees","MUR","₨","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("68","Pesos","MXN","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("69","Tugriks","MNT","₮","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("70","Meticais","MZN","MT","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("71","Dollars","NAD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("72","Rupees","NPR","₨","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("73","Guilders","ANG","ƒ","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("74","Dollars","NZD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("75","Cordobas","NIO","C$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("76","Nairas","NGN","₦","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("77","Krone","NOK","kr","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("78","Rials","OMR","﷼","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("79","Rupees","PKR","₨","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("80","Balboa","PAB","B/.","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("81","Guarani","PYG","Gs","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("82","Nuevos Soles","PEN","S/.","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("83","Pesos","PHP","Php","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("84","Zlotych","PLN","zł","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("85","Rials","QAR","﷼","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("86","New Lei","RON","lei","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("87","Rubles","RUB","руб","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("88","Pounds","SHP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("89","Riyals","SAR","﷼","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("90","Dinars","RSD","Дин.","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("91","Rupees","SCR","₨","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("92","Dollars","SGD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("93","Dollars","SBD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("94","Shillings","SOS","S","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("95","Rand","ZAR","R","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("96","Rupees","LKR","₨","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("97","Kronor","SEK","kr","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("98","Dollars","SRD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("99","Pounds","SYP","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("100","New Dollars","TWD","NT$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("101","Baht","THB","฿","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("102","Dollars","TTD","TT$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("103","Lira","TRY","TL","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("104","Liras","TRL","£","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("105","Dollars","TVD","$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("106","Hryvnia","UAH","₴","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("107","Pesos","UYU","$U","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("108","Sums","UZS","лв","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("109","Bolivares Fuertes","VEF","Bs","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("110","Dong","VND","₫","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("111","Rials","YER","﷼","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("112","Taka","BDT","৳","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_currencies VALUES("113","Zimbabwe Dollars","ZWD","Z$","1","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_daily_expenses;

CREATE TABLE `sm_daily_expenses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `head_id` int(11) DEFAULT NULL,
  `sub_head_id` int(11) DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_approved` int(11) NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `active_status` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_date_formats;

CREATE TABLE `sm_date_formats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `format` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `normal_view` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT '1',
  `updated_by` int(11) DEFAULT '1',
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_date_formats VALUES("1","jS M, Y","7th May, 2019","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("2","MM/DD/YY","02/17/2009","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("3","DD/MM/YY","17/02/2009","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("4","Month D, Yr","February 17, 2009","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("5","DDMonYY","17Feb2009","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("6","YYMonDD","2009Feb17","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("7","D Month, Yr","17 February, 2009","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("8","F j, Y, g:i a","May 7, 2019, 6:20 pm","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("9","m.d.y","02.05.19","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("10","j, n, Y","5, 2, 2019","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("11","Ymd","20190205","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("12","h-i-s, j-m-y, it is w Day","06-20-25, 5-02-10, 2028 2025 5 Fripm10","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("13","it is the jS day","it is the 5th day","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("14","D M j G:i:s T Y","Fri Feb 5 18:20:25 PST 2010","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_date_formats VALUES("15","H:m:s m is month","18:02:25 m is month","1","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");



DROP TABLE sm_debit_credits;

CREATE TABLE `sm_debit_credits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `voucher_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type` enum('D','C') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'd debit, c credit',
  `note` text COLLATE utf8mb4_unicode_ci,
  `customer` text COLLATE utf8mb4_unicode_ci,
  `receiver` text COLLATE utf8mb4_unicode_ci,
  `company_or_address` text COLLATE utf8mb4_unicode_ci,
  `amount` double DEFAULT NULL,
  `authorised_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accountant_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_designations;

CREATE TABLE `sm_designations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_designations VALUES("1","Accounts Manager","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_designations VALUES("2","Recruitment Manager","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_designations VALUES("3","Technology Manager","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_designations VALUES("4","Store Manager","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_designations VALUES("5","Departmental Managers","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_designations VALUES("6","General Managers","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_designations VALUES("7","Chief Information Officer (CIO)","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_designations VALUES("8","Chief Technology Officer (CTO)","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");



DROP TABLE sm_dormitory_lists;

CREATE TABLE `sm_dormitory_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dormitory_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'B=Boys, G=Girls',
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intake` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_dormitory_lists VALUES("1","Sir Isaac Newton Hostel","B","25/13, Sukrabad Rd, Tallahbag, Dhaka 1215","120","Hostels provide lower-priced, sociable accommodation where guests can rent a bed, usually a bunk bed, in a dormitory and share a bathroom, lounge and sometimes a kitchen.","1","1","1","1","","");
INSERT INTO sm_dormitory_lists VALUES("2","Louis Pasteur Hostel","B","25/13, Sukrabad Rd, Tallahbag, Dhaka 1215","120","Hostels provide lower-priced, sociable accommodation where guests can rent a bed, usually a bunk bed, in a dormitory and share a bathroom, lounge and sometimes a kitchen.","1","1","1","1","","");
INSERT INTO sm_dormitory_lists VALUES("3","Galileo Hostel","B","25/13, Sukrabad Rd, Tallahbag, Dhaka 1215","120","Hostels provide lower-priced, sociable accommodation where guests can rent a bed, usually a bunk bed, in a dormitory and share a bathroom, lounge and sometimes a kitchen.","1","1","1","1","","");
INSERT INTO sm_dormitory_lists VALUES("4","Marie Curie Hostel","B","25/13, Sukrabad Rd, Tallahbag, Dhaka 1215","120","Hostels provide lower-priced, sociable accommodation where guests can rent a bed, usually a bunk bed, in a dormitory and share a bathroom, lounge and sometimes a kitchen.","1","1","1","1","","");
INSERT INTO sm_dormitory_lists VALUES("5","Albert Einstein Hostel","B","25/13, Sukrabad Rd, Tallahbag, Dhaka 1215","120","Hostels provide lower-priced, sociable accommodation where guests can rent a bed, usually a bunk bed, in a dormitory and share a bathroom, lounge and sometimes a kitchen.","1","1","1","1","","");
INSERT INTO sm_dormitory_lists VALUES("6","Charles Darwin Hostel","B","25/13, Sukrabad Rd, Tallahbag, Dhaka 1215","120","Hostels provide lower-priced, sociable accommodation where guests can rent a bed, usually a bunk bed, in a dormitory and share a bathroom, lounge and sometimes a kitchen.","1","1","1","1","","");
INSERT INTO sm_dormitory_lists VALUES("7","Nikola Tesla Hostel","B","25/13, Sukrabad Rd, Tallahbag, Dhaka 1215","120","Hostels provide lower-priced, sociable accommodation where guests can rent a bed, usually a bunk bed, in a dormitory and share a bathroom, lounge and sometimes a kitchen.","1","1","1","1","","");



DROP TABLE sm_email_settings;

CREATE TABLE `sm_email_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_engine_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_server` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_port` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_security` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_email_settings VALUES("1","smtp","demo_name","demo@email.com","spn5@spondonit.com","Dhaka@5577","smtp.mailtrap.io","2525","","1","","","1","","");



DROP TABLE sm_email_sms_logs;

CREATE TABLE `sm_email_sms_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `send_date` date DEFAULT NULL,
  `send_through` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `send_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_enlisted_suppliers;

CREATE TABLE `sm_enlisted_suppliers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cotact_person_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_quantity` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_enlisted_suppliers VALUES("1","company name 1","address 1","Contact Person Name 1","0197823649231","lang.may@sipes.com","","","Lorem Ipsum is simply dummy text of the printing and typesetting industry.","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_enlisted_suppliers VALUES("2","company name 2","address 2","Contact Person Name 2","0197823649232","djohns@hotmail.com","","","Lorem Ipsum is simply dummy text of the printing and typesetting industry.","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_enlisted_suppliers VALUES("3","company name 3","address 3","Contact Person Name 3","0197823649233","monserrat57@oreilly.biz","","","Lorem Ipsum is simply dummy text of the printing and typesetting industry.","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_enlisted_suppliers VALUES("4","company name 4","address 4","Contact Person Name 4","0197823649234","qschulist@gmail.com","","","Lorem Ipsum is simply dummy text of the printing and typesetting industry.","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_enlisted_suppliers VALUES("5","company name 5","address 5","Contact Person Name 5","0197823649235","hoppe.alize@ritchie.com","","","Lorem Ipsum is simply dummy text of the printing and typesetting industry.","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_events;

CREATE TABLE `sm_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_des` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `uplad_image_file` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_expense_heads;

CREATE TABLE `sm_expense_heads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_expense_heads VALUES("1","magnam","However, at the name is the court, \'Bring me grow larger, sir, if I shall tell you--all I don\'t.","54906976","1","1","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_expense_heads VALUES("2","sed","And mentioned before, never! And so far down in Wonderland, though she did that?--It was at the.","92056930","1","2","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_expense_heads VALUES("3","velit","King, rubbing his teacup in THAT well say in their faces, so please your evidence,\' said to it.","71788654","1","3","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_expense_heads VALUES("4","odio","The question is, I suppose Dinah\'ll be clearer than his claws, And she tipped over the whole party.","11324553","1","4","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_expense_heads VALUES("5","non","Alice quietly said, and whispered \'She\'s in a somersault in the act of that for a whiting. Now I.","36203420","1","5","","2019-12-15 14:03:12","2019-12-15 14:03:12");



DROP TABLE sm_expired_tenders;

CREATE TABLE `sm_expired_tenders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `upcoming_tender_id` int(11) DEFAULT NULL,
  `tender_result` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_fund_transfers;

CREATE TABLE `sm_fund_transfers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_account_id` int(11) DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_general_settings;

CREATE TABLE `sm_general_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` int(11) DEFAULT '1',
  `language_id` int(11) DEFAULT '1',
  `date_format_id` int(11) DEFAULT '1',
  `time_zone_id` int(11) DEFAULT '1',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'USD',
  `currency_symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '$',
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `system_version` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1.0',
  `active_status` int(11) DEFAULT '1',
  `currency_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'USD',
  `language_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'en',
  `session_year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '2020',
  `system_purchase_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `system_activated_date` date DEFAULT NULL,
  `envato_user` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `envato_item_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `system_domain` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copyright_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_general_settings VALUES("1","Infix Business ERP","A Trusted Global Business Source of INFIX","1","Intesa Sanpaolo Spa. 1 William St New York, NY 10004","+880170360870","admin@infixbusinesserp.com","","1","1","51","USD","$","public/uploads/settings/c83cf9850156c6c76dfff8f7c8b43ec6.png","public/uploads/settings/4afae96bdee0d50a0cacd63512100394.png","1.0","1","USD","en","2020","","","","","","Copyright &copy; 2019 All rights reserved | This template by Codethemes","","2019-12-17 14:00:11");



DROP TABLE sm_holidays;

CREATE TABLE `sm_holidays` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `holiday_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `upload_image_file` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_holidays VALUES("1","Summer Vacation","","2019-05-02","2019-05-08","","1","","","1","","");
INSERT INTO sm_holidays VALUES("2","Public Holiday","","2019-05-10","2019-05-11","","1","","","1","","");
INSERT INTO sm_holidays VALUES("3","winter vacation","winter vacation will remain 3 days","2019-12-27","2019-12-29","","1","1","1","1","2019-12-17 14:04:59","2019-12-17 14:05:19");



DROP TABLE sm_home_page_settings;

CREATE TABLE `sm_home_page_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `long_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `link_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_home_page_settings VALUES("1","THE ULTIMATE BUSINESS ERP","INFIX","Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","Learn More About Us","http://infixedu.com/about","public/backEnd/img/client/home-banner1.jpg","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_hourly_rates;

CREATE TABLE `sm_hourly_rates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grade` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_hr_payroll_earn_deducs;

CREATE TABLE `sm_hr_payroll_earn_deducs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payroll_generate_id` int(10) unsigned NOT NULL,
  `type_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `earn_dedc_type` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e for earnings and d for deductions',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_hr_payroll_generates;

CREATE TABLE `sm_hr_payroll_generates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `basic_salary` int(11) DEFAULT NULL,
  `total_earning` int(11) DEFAULT NULL,
  `total_deduction` int(11) DEFAULT NULL,
  `gross_salary` int(11) DEFAULT NULL,
  `tax` int(11) DEFAULT NULL,
  `net_salary` int(11) DEFAULT NULL,
  `payroll_month` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payroll_year` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payroll_status` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'NG for not generated, G for generated, P for paid',
  `payment_mode` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `expense_head_id` int(11) DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cheque_deposite_date` date DEFAULT NULL,
  `cheque_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_hr_payroll_generates VALUES("1","1","3100","5100","400","4100","100","12000","March","2012","G","1","","","","","","","","","Alice!\' she was lying down to the question of its paws in her choice, and said the name like the.","1","1","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_hr_payroll_generates VALUES("2","2","3101","5101","401","4101","101","12003","February","2011","G","1","","","","","","","","","March Hare. Visit either question, and she said Alice, were beautifully printed on the Duchess.","1","2","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_hr_payroll_generates VALUES("3","3","3102","5102","402","4102","102","12006","May","1970","G","1","","","","","","","","","CHAPTER III. A MILE HIGH TO BE TRUE--\" that\'s not do let the procession came rattling teacups as.","1","3","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_hr_payroll_generates VALUES("4","4","3103","5103","403","4103","103","12009","February","1979","G","1","","","","","","","","","Allow me for showing off being rather crossly: \'of course you think you coward!\' and this grand.","1","4","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_hr_payroll_generates VALUES("5","5","3104","5104","404","4104","104","12012","December","1982","G","1","","","","","","","","","I got burnt, and I\'ve got up into her knowledge. \'Just about children who only wish I tell you!\'.","1","5","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_hr_payroll_generates VALUES("6","6","3105","5105","405","4105","105","12015","May","2005","G","1","","","","","","","","","HAVE my dear paws! Oh my tail when she walked off, panting, and a hard against each hand. \'And.","1","6","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_hr_payroll_generates VALUES("7","7","3106","5106","406","4106","106","12018","May","2013","G","1","","","","","","","","","Alice, very sadly down yet, please do!\' said do. Alice began to be clearer than a good deal: this.","1","7","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_hr_payroll_generates VALUES("8","8","3107","5107","407","4107","107","12021","September","1974","G","1","","","","","","","","","Hatter. \'I must be asleep instantly, and Pepper For this way:-- \"Up above a March Hare. \'I cut off.","1","8","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_hr_payroll_generates VALUES("9","9","3108","5108","408","4108","108","12024","June","2000","G","1","","","","","","","","","Alice as well enough; don\'t even looking over all the subject. \'Go on your Majesty,\' he was too.","1","9","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_hr_payroll_generates VALUES("10","10","3109","5109","409","4109","109","12027","June","1971","G","1","","","","","","","","","Alice, \'a great surprise. \'What! Never heard one hand, and she went timidly up the last turned to.","1","10","","1","2019-12-15 14:03:12","2019-12-15 14:03:12");



DROP TABLE sm_hr_salary_templates;

CREATE TABLE `sm_hr_salary_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salary_grades` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salary_basic` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overtime_rate` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_rent` int(11) DEFAULT NULL,
  `provident_fund` int(11) DEFAULT NULL,
  `gross_salary` int(11) DEFAULT NULL,
  `total_deduction` int(11) DEFAULT NULL,
  `net_salary` int(11) DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_human_departments;

CREATE TABLE `sm_human_departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_human_departments VALUES("1","Production","1","1","","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_human_departments VALUES("2","Purchasing","1","1","","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_human_departments VALUES("3","Merchandising","1","1","","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_human_departments VALUES("4","Research and Development","1","1","","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_human_departments VALUES("5","Marketing","1","1","","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_human_departments VALUES("6","Customer Service","1","1","","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_human_departments VALUES("7","Accountants","1","1","","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_human_departments VALUES("8","Human Resource Management","1","1","","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_human_departments VALUES("9","Accounting and Finance","1","1","","","2019-12-15 14:03:12","2019-12-15 14:03:12");



DROP TABLE sm_income_heads;

CREATE TABLE `sm_income_heads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_income_heads VALUES("1","quaerat","Hatter: and Alice waited a subject! Our family always pepper that assembled on the Queen shouted the best thing before, \'and the room at Alice was immediately met in a daisy-chain would deny it.","4","1","1","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_income_heads VALUES("2","corrupti","Pennyworth only took the door led the last March--just before HE taught us dry would make me larger, I was certainly too much, so close to wink of her age knew the most important air, mixed flavour.","3","1","2","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_income_heads VALUES("3","adipisci","Canary called lessons,\' the Queen. \'Sentence first--verdict afterwards.\' \'Stuff and off, or two were beautifully printed on for a right to you? Tell her flamingo was delighted to the pool of the.","5","1","3","","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO sm_income_heads VALUES("4","hic","Mercia and she soon as I don\'t even room at last, and other guinea-pig cheered, and a little sharp kick, and she would cost them her life. The Mouse did not so I\'ll try and making such a great.","5","1","4","","2019-12-15 14:03:12","2019-12-15 14:03:12");



DROP TABLE sm_inspecting_departments;

CREATE TABLE `sm_inspecting_departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 = no, 1= yes',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_inspecting_departments VALUES("1","Maxime eos sit sint.","Heather Harris","77346916","rtrantow@yahoo.com","Quis et qui.","1","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_inspecting_departments VALUES("2","Quia fugiat dolor et.","Ms. Natalie Ernser II","40476008","august51@shanahan.com","Perferendis est illo.","1","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_inspecting_departments VALUES("3","Quas ipsa sed omnis aliquam sunt.","Miss Madalyn Koepp","64266604","cecilia.funk@gmail.com","Rerum mollitia voluptatibus.","1","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_inspecting_departments VALUES("4","Numquam possimus magni est velit.","Prof. Anais Jaskolski PhD","88161036","veum.blaise@gmail.com","Qui ut quos.","1","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_inspecting_departments VALUES("5","Nostrum asperiores voluptas.","Mr. Hayden Kohler","93204222","johanna38@gleichner.com","Iusto veniam porro ut.","1","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_instructions;

CREATE TABLE `sm_instructions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_inventory_payments;

CREATE TABLE `sm_inventory_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_receive_sell_id` int(10) unsigned DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `reference_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'R for receive S for sell',
  `payment_method` int(10) unsigned DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` tinyint(4) DEFAULT NULL,
  `updated_by` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_investments;

CREATE TABLE `sm_investments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_id` int(11) NOT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_item_categories;

CREATE TABLE `sm_item_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_item_categories VALUES("1","Raw Materials Inventory","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_categories VALUES("2","Transit Inventory","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_categories VALUES("3","Buffer Inventory","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_categories VALUES("4","Application Inventory","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_categories VALUES("5","Enterprice Inventory","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_categories VALUES("6","Others Inventory","2019-12-15 14:02:59","2019-12-15 14:02:59");



DROP TABLE sm_item_issues;

CREATE TABLE `sm_item_issues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned DEFAULT NULL,
  `issue_to` int(10) unsigned DEFAULT NULL,
  `issue_by` int(10) unsigned DEFAULT NULL,
  `item_category_id` int(10) unsigned DEFAULT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `quantity` int(10) unsigned DEFAULT NULL,
  `issue_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_item_receive_children;

CREATE TABLE `sm_item_receive_children` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_receive_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `unit_price` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `sub_total` int(11) DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_item_receives;

CREATE TABLE `sm_item_receives` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `product_id` int(11) DEFAULT NULL,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receive_date` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grand_total` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_quantity` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_due` int(11) DEFAULT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `part_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_part_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `denomination` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qnt` int(11) DEFAULT NULL,
  `unit_price` double(10,2) DEFAULT NULL,
  `total_paid` double(10,2) DEFAULT NULL,
  `sale_price` double(10,2) DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_item_receives VALUES("1","1","1","2019-12-15","description","1","1","2019-12-15","","","","","","1","","(kg)","101","1276.00","128876.00","1280.00","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_receives VALUES("2","1","1","2019-12-15","description","2","2","2019-12-15","","","","","","2","","(kg)","102","2552.00","260304.00","2560.00","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_receives VALUES("3","1","1","2019-12-15","description","3","3","2019-12-15","","","","","","3","","(kg)","103","3828.00","394284.00","3840.00","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_receives VALUES("4","1","1","2019-12-15","description","4","4","2019-12-15","","","","","","4","","(kg)","104","5104.00","530816.00","5120.00","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_receives VALUES("5","1","1","2019-12-15","description","5","5","2019-12-15","","","","","","5","","(kg)","105","6380.00","669900.00","6400.00","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");



DROP TABLE sm_item_sell_children;

CREATE TABLE `sm_item_sell_children` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_sell_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `sell_price` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `sub_total` int(11) DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_item_sells;

CREATE TABLE `sm_item_sells` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `student_staff_id` int(11) DEFAULT NULL,
  `sell_date` date DEFAULT NULL,
  `reference_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grand_total` int(11) DEFAULT NULL,
  `total_quantity` int(11) DEFAULT NULL,
  `total_paid` int(11) DEFAULT NULL,
  `total_due` int(11) DEFAULT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_item_stores;

CREATE TABLE `sm_item_stores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_item_stores VALUES("1","Store 1","100","Store 1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_stores VALUES("2","Store 2","200","Store 2","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_stores VALUES("3","Store 3","300","Store 3","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_stores VALUES("4","Store 4","400","Store 4","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_item_stores VALUES("5","Store 5","500","Store 5","1","2019-12-15 14:02:59","2019-12-15 14:02:59");



DROP TABLE sm_item_subcategories;

CREATE TABLE `sm_item_subcategories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `sub_category_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_item_subcategories VALUES("1","1","Sub Category 1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_item_subcategories VALUES("2","2","Sub Category 2","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_item_subcategories VALUES("3","3","Sub Category 3","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_item_subcategories VALUES("4","4","Sub Category 4","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_item_subcategories VALUES("5","5","Sub Category 5","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_items;

CREATE TABLE `sm_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_name` int(11) DEFAULT NULL,
  `subcategory_name` int(11) DEFAULT NULL,
  `total_in_stock` double(8,2) DEFAULT '1.00',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_items VALUES("1","Item name 1","1","1","23.00","","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_items VALUES("2","Item name 2","2","2","46.00","","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_items VALUES("3","Item name 3","3","3","69.00","","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_items VALUES("4","Item name 4","4","4","92.00","","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_items VALUES("5","Item name 5","5","5","115.00","","1","2019-12-15 14:02:59","2019-12-15 14:02:59");



DROP TABLE sm_language_phrases;

CREATE TABLE `sm_language_phrases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modules` varchar(255) DEFAULT NULL,
  `default_phrases` varchar(500) DEFAULT NULL,
  `en` varchar(500) DEFAULT NULL,
  `es` varchar(500) DEFAULT NULL,
  `fr` varchar(500) DEFAULT NULL,
  `bn` varchar(500) DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1049 DEFAULT CHARSET=utf8;

INSERT INTO sm_language_phrases VALUES("1","0","dashboard","Dashboard","Tablero","Tableau de bord","ড্যাশবোর্ড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("2","0","welcome","Welcome","Bienvenido","Bienvenue","স্বাগত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("3","0","student","Student","Estudiante","Étudiant","ছাত্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("4","0","total","Total","Total","Total","মোট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("5","0","teachers","Teachers","Maestros","Enseignants","শিক্ষক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("6","0","parents","Parents","Los padres","Parents","মাতাপিতা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("7","0","staffs","Staffs","Personal","Le personnel","কর্মীরা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("8","0","income_and_expenses_for","Income and Expenses for","Ingresos y gastos para","Revenus et dépenses pour","আয় এবং ব্যয় জন্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("9","0","total_income","Total Income","Ingresos totales","Revenu total","মোট আয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("10","0","total_expenses","Total Expenses","Gastos totales","Dépenses totales","মোট খরচ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("11","0","total_profit","Total Profit","Beneficio total","Bénéfice total","সমস্ত লাভ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("12","0","total_revenue","Total Revenue","Los ingresos totales","Revenu total","মোট রাজস্ব","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("13","0","title","Title","Título","Titre","খেতাব","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("14","0","message","Message","Mensaje","Message","বার্তা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("15","0","actions","Actions","Comportamiento","actes","ক্রিয়াকলাপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("16","0","calendar","Calendar","Calendario","Calendrier","পাঁজি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("17","0","view","View","Ver","Vue","দৃশ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("18","0","to_do_list","To Do List","Lista de quehaceres","Liste de choses à faire","তালিকা তৈরি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("19","0","add","Add","Añadir","Ajouter","যোগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("20","0","edit","Edit","Editar","modifier","সম্পাদন করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("21","0","no_do_lists_assigned_yet","No Do Lists Assigned Yet","No hay listas asignadas aún","Aucune liste assignée pour linstant","না এখনো তালিকাভুক্ত করা তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("22","1","admin_section","Reception Section","Reception de Administración","Section Reception","রিসেপশন বিভাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("23","1","admission_query","Admission Query","Consulta de Admisión","Requête dadmission","ভর্তি প্রশ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("24","1","select_criteria","Select Criteria","Seleccione los criterios","Sélectionner des critères","মাপদণ্ড নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("25","1","date_from","Date From","Fecha de","Dater de","তারিখ হতে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("26","1","date_to","Date To","Fecha para","Date à","তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("27","1","select_source","Select Source","Seleccione Fuente","Sélectionnez la source","উৎস নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("28","1","select_status","Select status","Seleccionar estado","Sélectionnez le statut","অবস্থা নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("29","1","Status","Status","Estado","Statut","অবস্থা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("30","1","active","Active","Activo","actif","সক্রিয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("31","1","inactive","Inactive","Inactivo","Inactif","নিষ্ক্রিয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("32","1","search","Search","Buscar","Chercher","অনুসন্ধান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("33","1","query_list","Query List","Lista de consultas","Liste de requêtes","প্রশ্ন তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("34","1","name","Name","Nombre","prénom","নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("35","1","phone","Phone","Teléfono","Téléphone","ফোন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("36","1","source","Source","Fuente","La source","সূত্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("37","1","email","Email","Email","Email","ইমেইল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("38","1","query_date","Query Date","Fecha de consulta","Date de la requête","প্রশ্ন তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("39","1","last_follow_up_date","last follow up date","última fecha de seguimiento","dernière date de suivi","সর্বশেষ ফলো আপ তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("40","1","next_follow_up_date","next follow up date","siguiente fecha de seguimiento","prochaine date de suivi","পরবর্তী অনুসরণ তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("41","1","select","Select","Seleccionar","Sélectionner","নির্বাচন করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("42","1","add_query","Add Query","Añadir consulta","Ajouter une requête","প্রশ্ন যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("43","1","delete","Delete","Borrar","Effacer","মুছে ফেলা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("44","1","delete_admission_query","Delete Admission Query","Eliminar consulta de admisión","Supprimer la requête dadmission","ভর্তি প্রশ্ন মুছে ফেলুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("45","1","are_you_sure_to_delete","Are you sure to delete this item?","¿Estás seguro de eliminar este elemento?","Êtes-vous sûr de vouloir supprimer cet article?","আপনি এই আইটেম মুছে ফেলার জন্য নিশ্চিত?","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("46","1","cancel","Cancel","Cancelar","Annuler","বাতিল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("47","1","admission_enquiry","Admission Enquiry","Consulta de Admisión","Enquête dadmission","ভর্তি পরীক্ষা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("48","1","address","Address","Dirección","Adresse","ঠিকানা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("49","1","description","Description","Descripción","La description","বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("50","1","date","Date","Fecha","Rendez-vous amoureux","তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("51","1","assigned","Assigned","Asignado","Attribué","বরাদ্দ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("52","1","reference","Reference","Referencia","Référence","উল্লেখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("53","1","number_of_child","Number of child","Numero de niño","Nombre denfant","সন্তানের সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("54","1","save","Save","Salvar","sauvegarder","সংরক্ষণ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("55","1","visitor_book","Visitor Book","Libro de visitas","Livre de visites","ভিজিটর বুক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("56","1","visitor","Visitor","Visitante","Visiteur","পরিদর্শক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("57","1","purpose","Purpose","Propósito","Objectif","উদ্দেশ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("58","1","id","Id","CARNÉ DE IDENTIDAd","Id","আইডি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("59","1","no_of_person","No. of Person","No. de persona","No. de personne","ব্যক্তির সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("60","1","in_time","In Time","A tiempo","À lheure","সময়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("61","1","out_time","Out time","Fuera de tiempo","Temps de sortie","সময় শেষ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("62","1","browse","browse","vistazo","Feuilleter","ব্রাউজ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("63","1","update","Update","Actualizar","Mettre à jour","হালনাগাদ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("64","1","visitor_list","Visitor List","Lista de visitantes","Liste de visiteurs","ভিজিটর তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("65","1","download","Download","Descargar","Télécharger","ডাউনলোড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("66","1","complaint","Complaint","Queja","Plainte","অভিযোগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("67","1","by","By","Por","Par","দ্বারা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("68","1","type","Type","Tipo","Type","আদর্শ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("69","1","taken","Taken","Tomado","Pris","ধরা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("70","1","list","List","Lista","liste","তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("71","1","postal_receive","Postal Receive","Recibir Postal","Réception postale","পোস্টাল গ্রহণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("72","1","from_title","From Title","Del título","De titre","শিরোনাম থেকে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("73","1","no","No.","No.","Non.","না।","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("74","1","note","Note","Nota","Remarque","বিঃদ্রঃ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("75","1","to_title","To Title","Al título","Au titre","শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("76","1","postal_dispatch","Postal Dispatch","Despacho Postal","Envoi postal","ডাক প্রেরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("77","1","phone_call_log","Phone Call Log","Registro de llamadas telefónicas","Journal des appels téléphoniques","ফোন কল লগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("78","1","phone_call","Phone Call","Llamada telefónica","Appel téléphonique","ফোন কল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("79","1","follow_up_date","Follow Up Date","Fecha de seguimiento","Date de suivi","আপ অনুসরণ করুন তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("80","1","call_duration","Call Duration","Duración de la llamada","Durée dappel","কল সময়কাল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("81","1","incoming","Incoming","Entrante","Entrant","ইনকামিং","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("82","1","outgoing","Outgoing","Saliente","Sortant","বিদায়ী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("83","1","call","Call","Llamada","Appel","কল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("84","1","admin_setup","Admin Setup","Configuración de administrador","Configuration de ladministrateur","অ্যাডমিন সেটআপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("85","1","student_certificate","Student Certificate","Certificado de estudiante","Certificat détudiant","ছাত্র সার্টিফিকেট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("86","1","certificate","Certificate","Certificado","Certificat","শংসাপত্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("87","1","header_left_text","Header left text","Encabezado texto a la izquierda","En-tête gauche du texte","শিরোনাম বাম টেক্সট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("88","1","body","Body","Cuerpo","Corps","শরীর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("89","1","footer_left_text","Footer left text","Pie de página texto a la izquierda","Footer left text","পাদচরণ বাম টেক্সট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("90","1","footer_center_text","Footer Center text","Texto del centro de pie de página","Footer Center text","পাদচরণ কেন্দ্র টেক্সট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("91","1","footer_right_text","Footer Right text","Pie derecho texto","Footer Right text","পাদচরণ ডান টেক্সট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("92","1","student_photo","Student Photo","Foto de estudiante","Photo étudiante","ছাত্র ফটো","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("93","1","yes","yes","sí","Oui","হাঁ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("94","1","none","No","No","Non","না","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("95","1","background_image","Background Image","Imagen de fondo","Image de fond","পটভূমি চিত্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("96","1","generate_certificate","Generate Certificate","Generar certificado","Générer un certificat","শংসাপত্র তৈরি করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("97","1","select_section","Select section","Seleccione la sección","Sélectionnez une section","বিভাগ নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("98","1","generate","Generate","Generar","produire","জেনারেট করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("99","1","admission","Admission","Admisión","Admission","স্বীকারোক্তি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("100","1","class_Sec","Class (Sec.)","Clase (Sec.)","Classe (Sec.)","ক্লাস (সেকেন্ড।)","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("101","1","father","Father","Padre","Père","পিতা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("102","1","date_of_birth","Date Of Birth","Fecha de nacimiento","Date de naissance","জন্ম তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("103","1","gender","Gender","Género","Le sexe","লিঙ্গ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("104","1","mobile","Mobile","Móvil","Mobile","মোবাইল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("105","1","student_id_card","Student ID Card","Credencial de estudiante","Carde didentité détudiant","ছাত্র আইডি কার্ড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("106","1","id_card_title","ID Card Title","Título de la tarjeta de identificación","Titre de la carte didentité","আইডি কার্ড শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("107","1","number","Number","Número","Nombre","সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("108","1","mother","Mother","Madre","Mère","মা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("109","1","blood_group","Blood Group","Grupo sanguíneo","Groupe sanguin","রক্তের গ্রুপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("110","1","id_card","ID Card","Tarjeta de identificación","Carte didentité","পরিচয় পত্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("111","1","generate_id_card","Generate ID Card","Generar tarjeta de identificación","Générer une carte didentité","আইডি কার্ড জেনারেট করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("112","1","all","All","Todos","Tout","সব","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("113","1","relation_with_guardian","Relation with Guardian","Relación con Guardian","Relation avec le gardien","গার্ডিয়ান সঙ্গে সম্পর্ক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("114","1","admin","Admin","Administración","Admin","অ্যাডমিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("115","1","follow_up","Follow up","Seguir","Suivre","অনুসরণ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("116","1","follow_up_admission_query","Follow Up Admission Query","Consulta de seguimiento de admisión","Requête dadmission de suivi","ভর্তি পরীক্ষা প্রশ্ন অনুসরণ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("117","1","response","Response","Respuesta","Réponse","প্রতিক্রিয়া","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("118","1","follow_up_list","Follow Up List","Lista de seguimiento","Liste de suivi","তালিকা অনুসরণ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("119","1","query_by","Query By","Consulta por","Requête par","দ্বারা প্রশ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("120","1","delete_follow_up_query","Delete Follow up query","Eliminar consulta de seguimiento","Supprimer la requête de suivi","ফলো আপ প্রশ্ন মুছে ফেলুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("121","1","certificate_body_len","Max Character lenght 500","Longitud máxima de caracteres 500","Longueur maximum 500 caractères","সর্বোচ্চ অক্ষর 500 সামান্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("122","2","student_information","Student Info","Información del estudiante","Info étudiant","ছাত্র তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("123","2","student_admission","Student Admission","Admisión de estudiantes","Admission des étudiants","ছাত্র ভর্তি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("124","2","student_import","Student Import","Estudiante de importación","Import étudiant","ছাত্র আমদানি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("125","2","import","Import","Importar","Importation","আমদানি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("126","2","personal","Personal","Personal","Personnel","ব্যক্তিগত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("127","2","info","Info","Información","Info","তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("128","2","roll","Roll","Rodar","Rouleau","রোল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("129","2","first","First","primero","Premier","প্রথম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("130","2","last","Last","Último","Dernier","গত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("131","2","religion","Religion","Religión","Religion","ধর্ম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("132","2","caste","Caste","Casta","Caste","জাত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("133","2","category","Category","Categoría","Catégorie","বিভাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("134","2","height","Height","Altura","la taille","উচ্চতা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("135","2","Weight","Weight","Peso","Poids","ওজন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("136","2","sibling","Sibling","Hermano","Enfant de mêmes parents","সমরূপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("137","2","information","Information","Información","Information","তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("138","2","guardian","Guardian","guardián","Gardien","অভিভাবক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("139","2","&","&","Y","Et","&","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("140","2","occupation","Occupation","Ocupación","Occupation","পেশা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("141","2","photo","Photo","Foto","Photo","ছবি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("142","2","Other","Others","Otros","Autres","অন্যরা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("143","2","relation_with_guardian","Relation with Guardian","Relación con Guardian","Relation avec le gardien","গার্ডিয়ান সঙ্গে সম্পর্ক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("144","2","current","Current","Corriente","Actuel","বর্তমান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("145","2","permanent","Permanent","Permanente","Permanent","স্থায়ী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("146","2","route_list","Route List","Lista de rutas","Liste des itinéraires","রুট তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("147","2","driver","Driver","Conductor","Chauffeur","চালক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("148","2","room","Room","Habitación","Pièce","ঘর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("149","2","national_iD_number","National ID Number","Numero de identificacion nacional","numéro national didentité","জাতীয় আইডি নম্বর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("150","2","local_Id_Number","Local Id Number","Número de identificación local","Numéro didentification local","স্থানীয় আইডি নম্বর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("151","2","bank","Bank","Banco","Banque","ব্যাংক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("152","2","previous_school_details","Previous School Details","Detalles de la escuela anterior","Détails de lécole précédente","পূর্ববর্তী স্কুল বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("153","2","additional_notes","Additional Notes","Notas adicionales","Notes complémentaires","অতিরিক্ত নোট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("154","2","parents_and_guardian_info","PARENTS & GUARDIAN INFO","INFORMACIÓN PARA LOS PADRES Y TUTORES","INFO PARENTS ET GARDIENS","পিতামাতা এবং গার্ডিয়ান তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("155","2","transport_and_dormitory_info","Transport & Dormitory Info","Información de transporte y dormitorio","Informations sur le transport et le dortoir","পরিবহন ও ডরমিটার তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("156","2","document_info","Document Info","Información del documento","Informations sur le document","নথি তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("157","2","document_01_title","Document 01 Title","Documento 01 Título","Document 01 Titre","ডকুমেন্ট ১ শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("158","2","document_02_title","Document 02 Title","Documento 02 Titulo","Document 02 Titre","ডকুমেন্ট ২ শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("159","2","document_03_title","Document 03 Title","Título del documento 03","Document 03 Titre","ডকুমেন্ট ৩ শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("160","2","document_04_title","Document 04 Title","Documento 04 Título","Document 04 Titre","ডকুমেন্ট ৪ শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("161","2","student_details","Student Details","Detalles del estudiante","Détails de létudiant","ছাত্র বিস্তারিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("162","2","search_by_name","Search By Name","Buscar por nombre","Rechercher par nom","নাম দ্বারা অনুসন্ধান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("163","2","search_by_roll_no","Search By Roll No","Búsqueda por rollo no","Recherche par roulement no","রোল নম্বর দ্বারা অনুসন্ধান করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("164","2","father_name","Fathers Name","Nombre del Padre","Le nom du père","বাবার নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("165","2","student_promote","Student Promote","Estudiante de promoción","Étudiant promouvoir","ছাত্র প্রচার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("166","2","select_current_session","Select Current Session","Seleccionar sesión actual","Sélectionner la session en cours","বর্তমান সেশন নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("167","2","select_current_class","Select current Class","Seleccione la clase actual","Sélectionnez la classe actuelle","বর্তমান ক্লাস নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("168","2","select_current_section","Select Current section","Seleccione la sección actual","Sélectionnez la section actuelle","বর্তমান বিভাগ নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("169","2","promote_student_in_next_session","Promote Student In Next Session","Promover estudiante en la próxima sesión","Promouvoir létudiant à la prochaine session","পরবর্তী সেশনে ছাত্র প্রচার করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("170","2","view_academic_performance","View Academic Performance","Ver rendimiento académico","Voir la performance académique","একাডেমিক পারফরম্যান্স দেখুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("171","2","pass","Pass","Pasar","Passer","পাস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("172","2","fail","Fail","Fallar","Échouer","ব্যর্থ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("173","2","select_promote_session","Select Promote Session","Seleccione Promover Sesión","Sélectionnez la session de promotion","সেশন প্রচার করুন নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("174","2","select_promote_class","Select Promote Class","Seleccione Promover clase","Sélectionnez Promouvoir la classe","ক্লাস প্রচার করুন নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("175","2","the_session_is_required","The session is required","La sesion es obligatoria","La session est obligatoire","অধিবেশন প্রয়োজন হয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("176","2","the_class_is_required","The class is required","La clase es obligatoria","Le cours est obligatoire","ক্লাস প্রয়োজন হয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("177","2","the_section_is_required","The section is required","La sección es obligatoria.","La section est obligatoire","বিভাগ প্রয়োজন হয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("178","2","select_promote_section","Select Promote Section","Seleccione la sección de promoción","Sélectionnez la section de promotion","নির্বাচন বিভাগ নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("179","2","promote","Promote","Promover","Promouvoir","উন্নীত করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("180","2","student_attendance","Student Attendance","Asistencia de estudiantes","Assiduité des étudiants","ছাত্র উপস্থিতি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("181","2","select_class","Select Class","Seleccionar clase","Sélectionnez une classe","ক্লাস নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("182","2","attendance","Attendance","Asistencia","Présence","উপস্থিতি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("183","2","attendance_already_submitted_as_holiday","Attendance Already Submitted As Holiday. You Can Edit Record","Asistencia ya enviada como festivo. Puede editar el registro","Présence déjà soumise à titre de vacances. Vous pouvez modifier lenregistrement","উপস্থিতি ইতিমধ্যে ছুটির দিন হিসাবে জমা দেওয়া।আপনি রেকর্ড সম্পাদনা করতে পারেন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("184","2","attendance_already_submitted","Attendance Already Submitted You Can Edit Record","La asistencia ya enviada Puede editar el registro","Présence déjà soumise Vous pouvez modifier la fiche","উপস্থিতি ইতিমধ্যে জমা আপনি রেকর্ড সম্পাদনা করতে পারেন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("185","2","mark_holiday","Mark Holiday","Mark Holiday","Mark Holiday","ছুটির দিন চিহ্নিত করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("186","2","present","Present","Presente","Présent","বর্তমান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("187","2","late","Late","Tarde","En retard","বিলম্বে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("188","2","absent","Absent","Ausente","Absent","অনুপস্থিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("189","2","half_day","Half Day","Medio día","Demi-journée","অর্ধেক দিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("190","2","add_note_here","Add Note Here","Añadir nota aquí","Ajouter une note ici","এখানে নোট যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("191","2","error","Error","Error","Erreur","এরর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("192","2","student_attendance_report","Student Attendance Report","Informe de asistencia del estudiante","Rapport de présence des étudiants","ছাত্র উপস্থিতি রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("193","2","january","January","enero","janvier","জানুয়ারী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("194","2","february","February","febrero","février","ফেব্রুয়ারি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("195","2","march","March","marzo","Mars","মার্চ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("196","2","april","April","abril","avril","এপ্রিল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("197","2","may","May","Mayo","Peut","মে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("198","2","june","June","junio","juin","জুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("199","2","july","July","julio","juillet","জুলাই","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("200","2","august","August","agosto","août","অগাস্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("201","2","september","September","septiembre","septembre","সেপ্টেম্বর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("202","2","october","October","octubre","octobre","অক্টোবর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("203","2","november","November","noviembre","novembre","নভেম্বর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("204","2","december","December","diciembre","décembre","ডিসেম্বর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("205","2","select_month","Select Month","Seleccione mes","Sélectionnez un mois","মাস নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("206","2","select_year","Select Year","Seleccione el año","Sélectionnez lannée","বছর নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("207","2","student_category","Student Category","Categoría de estudiante","Catégorie détudiant","ছাত্র বিভাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("208","2","student_category_list","Student Category List","Lista de categorías de estudiantes","Liste des catégories détudiants","ছাত্র শ্রেণী তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("209","2","student_group","Student Group","Grupo de estudiantes","Groupe détudiants","ছাত্র গ্রুপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("210","2","group","Group","Grupo","Groupe","গ্রুপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("211","2","disabled_student","Disabled Students","Estudiantes discapacitados","Etudiants handicapés","নিষ্ক্রিয় ছাত্রদের","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("212","2","student_list","Student List","Lista de estudiantes","Liste des étudiants","ছাত্র তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("213","2","birth_certificate","Birth Certificate","Certificado de nacimiento","Certificat de naissance","জন্ম সনদ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("214","2","student_edit","Student Edit","Estudiante Editar","Étudiant modifier","ছাত্র সম্পাদনা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("215","2","in","In","En","Dans","মধ্যে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("216","2","kg","KG","KG","KG","কেজি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("217","2","add_parent","Add Parent","Añadir padre","Ajouter un parent","মূল যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("218","2","update_information","Update information","Actualizar información","Mettre à jour les informations","হালনাগাদ তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("219","2","siblings","Siblings","Hermanos","Frères et sœurs","ভাইবোন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("220","2","guardian_name","Guardians Name","Nombre del guardián","Nom du gardien","গার্ডিয়ান এর নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("221","2","guardian_email","Guardians Email","Email del guardián","Email du gardien","গার্ডিয়ান এর ইমেল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("222","2","guardian_phone","Guardians Phone","Teléfono del guardián","Téléphone du gardien","গার্ডিয়ান ফোন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("223","2","guardian_occupation","Guardian Occupation","Ocupación Guardián","Profession de gardien","গার্ডিয়ান পেশা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("224","2","guardian_address","Guardian Address","Dirección del tutor","Adresse du gardien","গার্ডিয়ান ঠিকানা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("225","2","student_address_info","Student Address Info","Información de la dirección del estudiante","Adresse de l\'étudiant","ছাত্র ঠিকানা তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("226","2","current_address","Current Address","Direccion actual","Adresse actuelle","বর্তমান ঠিকানা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("227","2","permanent_address","Permanent Address","dirección permanente","Adresse permanente","স্থায়ী ঠিকানা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("228","2","vehicle_number","Vehicle Number","Número de vehículo","Numéro de véhicule","যানবাহন নম্বর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("229","2","driver_name","Driver Name","Nombre del conductor","Nom du conducteur","ড্রাইভার নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("230","2","bank_name","Bank Name","Nombre del banco","Nom de banque","ব্যাংকের নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("231","2","update_student","update student","actualizar estudiante","mise à jour de létudiant","ছাত্র আপডেট করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("232","2","remove","Remove","retirar","Retirer","অপসারণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("233","2","are_you","Are you sure to remove siblings?","¿Estás seguro de eliminar a los hermanos?","Êtes-vous sûr de vouloir supprimer vos frères et soeurs?","আপনি ভাইবোন অপসারণ নিশ্চিত?","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("234","2","download_sample_file","Download Sample File","Descargar archivo de muestra","Télécharger un exemple de fichier","নমুনা ফাইল ডাউনলোড করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("235","3","teacher","Teacher","Profesor","Prof","শিক্ষক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("236","3","upload_content","Upload Content","Subir contenido","Télécharger du contenu","আপলোড কন্টেন্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("237","3","assignment","Assignment","Asignación","Affectation","নিয়োগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("238","3","content_title","Content Title","Título del contenido","Titre du contenu","বিষয়বস্তু শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("239","3","study_material","Study Material","Material de estudio","Matériel détude","শিক্ষাসামগ্রী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("240","3","syllabus","Syllabus","Silaba","Programme","সিলেবাস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("241","3","other_download","Other Downloads","Otras descargas","Autres téléchargements","অন্যান্য ডাউনলোড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("242","3","available_for","Available for","Disponible para","Disponible pour","সহজলভ্যের জন্যে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("243","3","admin","Admin","Administración","Admin","অ্যাডমিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("244","3","available_for_all_classes","Available for all classes","Disponible para todas las clases.","Disponible pour toutes les classes","সব ক্লাসের জন্য উপলব্ধ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("245","3","action","Action","Acción","action","কর্ম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("246","3","other_downloads_list","Other Downloads List","Lista de otras descargas","Autres téléchargements","অন্যান্য ডাউনলোড তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("247","4","fees_collection","Fees Collection","Colección de tarifas","Collection de frais","ফি সংগ্রহ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("248","4","collect_fees","Collect Fees","Cobrar honorarios","Recueillir les frais","ফি সংগ্রহ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("249","4","search_fees_payment","Search Fees Payment","Pago de tarifas de búsqueda","Recherche des frais de paiement","অনুসন্ধান ফি প্রদান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("250","4","search_fees_due","Search Fees Due","Tarifas de búsqueda vencidas","Frais de recherche dus","অনুসন্ধান ফি কারণে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("251","4","fees_master","Fees Master","Honorarios maestro","Frais Maître","ফি মাস্টার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("252","4","fees_group","Fees Group","Grupo de tarifas","Groupe de frais","ফি গ্রুপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("253","4","fees_type","Fees Type","Tipo de Cuotas","Type de frais","ফি প্রকার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("254","4","fees_discount","Fees Discount","Tarifas de descuento","Remise des frais","ফি ছাড়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("255","4","fees_forward","Fees Carry Forward","Cuotas de llevar adelante","Frais reportés","ফি ফরওয়ার্ড বহন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("256","4","payment","Payment","Pago","Paiement","পারিশ্রমিক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("257","4","payment_ID_Details","Payment ID Details","Detalles de ID de pago","ID de paiement","পেমেন্ট আইডি বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("258","4","mode","Mode","Modo","Mode","মোড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("259","4","amount","Amount","Cantidad","Montant","পরিমাণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("260","4","discount","Discount","Descuento","Remise","ডিসকাউন্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("261","4","fine","Fine","Multa","Bien","জরিমানা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("262","4","fees_due_list","Fees Due List","Lista de cuotas","Frais à payer","ফি কারণে তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("263","4","due_birth","Due Birth","Nacimiento debido","Naissance due","জন্মের কারণে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("264","4","deposit","Deposit","Depositar","Dépôt","আমানত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("265","4","balance","Balance","Equilibrar","Équilibre","ভারসাম্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("266","4","master","Master","Dominar","Maîtriser","মনিব","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("267","4","assign","Assign","Asignar","Attribuer","দায়িত্ব অর্পণ করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("268","4","item","Product","ít","Article","পদ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("269","4","content","content","contenido","contenu","সন্তুষ্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("270","4","fees_code","Fees Code","Código de Cuotas","Code des frais","ফি কোড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("271","4","code","Code","Código","Code","কোড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("272","4","once","Once","Una vez","Une fois que","একদা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("273","4","year","Year","Año","Année","বছর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("274","4","previous_Session_Balance_Fees","Previous Session Balance Fees","Cuotas de balance de la sesión anterior","Frais de solde de la session précédente","পূর্ববর্তী অধিবেশন ব্যালেন্স ফি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("275","4","previous_balance_can_only_update_now.","Previous balance already forwarded, you can only update now.","El saldo anterior ya reenviado, solo se puede actualizar ahora.","Le solde précédent ayant déjà été transféré, vous ne pouvez mettre à jour que maintenant.","পূর্ববর্তী ভারসাম্য ইতিমধ্যে ফরোয়ার্ড করা হয়েছে, আপনি এখন শুধুমাত্র আপডেট করতে পারেন।","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("276","5","accounts","Accounts","Cuentas","Comptes","অ্যাকাউন্টস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("277","5","profit","Profit","Lucro","Profit","মুনাফা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("278","5","income","Income","Ingresos","le revenu","আয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("279","5","expense","Expense","Gastos","Frais","ব্যয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("280","5","chart_of_account","Chart Of Account","Plan de cuentas","Charte dutilisation","অ্যাকাউন্ট চার্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("281","5","payment_method","Payment Method","Método de pago","Mode de paiement","মূল্যপরিশোধ পদ্ধতি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("282","5","bank_account","Bank Account","Cuenta bancaria","Compte bancaire","ব্যাংক হিসাব","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("283","5","a_c_Head","A/C Head","A / C Head","Tête A / C","এ-সি হেড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("284","5","add_expense","Add Expense","Añadir gastos","Ajouter une dépense","ব্যয় যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("285","5","search_income_expense","Search Income/Expense","Buscar ingresos / gastos","Recherche revenu / dépense","অনুসন্ধান আয়-ব্যয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("286","5","item_Receive","Item Receive","El artículo recibe","Point recevoir","আইটেম প্রাপ্তি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("287","5","income_Head","Income Head","Jefe de ingresos","Chef de revenu","আয় হেড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("288","5","sells","Sells","Vende","Vend","বিক্রি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("289","5","grand_total","Grand Total","Gran total","somme finale","সর্বমোট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("290","5","expense_head","Expense Head","Cabeza de gastos","Chef de dépenses","ব্যয় মাথা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("291","5","purchase","Purchase","Compra","achat","ক্রয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("292","5","from","From","Desde","De","থেকে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("293","5","head","Head","Cabeza","Tête","মাথা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("294","5","method","Method","Método","Méthode","পদ্ধতি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("295","5","account_name","Account Name","Nombre de la cuenta","Nom du compte","হিসাবের নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("296","5","opening_balance","Opening Balance","Saldo de apertura","Solde douverture","ব্যালেন্স খোলা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("297","5","account","Account","Cuenta","Compte","হিসাব","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("298","6","human_resource","Human resource","Recursos humanos","Ressource humaine","মানব সম্পদ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("299","6","staff_directory","Staff Directory","Directorio de Personal","Répertoire personnel","স্টাফ ডিরেক্টরি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("300","6","staff_attendance","Staff Attendance","Asistencia del personal","Présence du personnel","স্টাফ উপস্থিতি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("301","6","staff_attendance_report","Staff Attendance Report","Informe de asistencia del personal","Rapport de présence du personnel","স্টাফ উপস্থিতি রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("302","6","payroll","Payroll","Nómina de sueldos","Paie","বেতনের","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("303","6","payroll_report","Payroll Report","Informe de nómina","Rapport de paie","Payroll রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("304","6","approve_leave_request","Approve Leave Request","Aprobar Solicitud de Licencia","Approuver la demande de congé","ছাড় অনুরোধ অনুমোদন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("305","6","apply_leave","Apply Leave","Aplicar licencia","Appliquer congé","আবেদন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("306","6","leave_type","Leave type","Dejar tipo","Laisser type","টাইপ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("307","6","department","Department","Departamento","département","বিভাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("308","6","designation","Designation","Designacion","La désignation","উপাধি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("309","6","staff_list","Staff List","Lista de personal","Liste du personnel","স্টাফ তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("310","6","add_staff","Add Staff","Añadir personal","Ajouter du personnel","স্টাফ যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("311","6","search_by_staff_id","Search By Staff Id","Búsqueda por identificación del personal","Rechercher par ID de personnel","স্টাফ আইডি দ্বারা অনুসন্ধান করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("312","6","staff","Staff","Personal","Personnel","কর্মী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("313","6","select_role","Select Role","Seleccionar rol","Sélectionnez un rôle","ভূমিকা নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("314","6","generate_payroll","Generate Payroll","Generar Nómina","Générer la paie","Payroll জেনারেট করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("315","6","generated","Generate","Generar","produire","জেনারেট করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("316","6","paid","Paid","Pagado","Payé","পেইড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("317","6","not","Not","No","ne pas","না","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("318","6","proceed_to_pay","Proceed to Pay","Proceda a pagar","Procéder au paiement","বেতন দিতে এগিয়ে যান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("319","6","view_payslip","View Payslip","Ver recibo de sueldo","Voir fiche de paie","Paylip দেখুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("320","6","month","Month","Mes","Mois","মাস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("321","6","payslip","Payslip","Boleta de pago","Fiche de paie","স্লিপে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("322","6","basic_salary","Basic Salary","Salario base","Salaire de base","মূল বেতন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("323","6","earnings","Earnings","Ganancias","Gains","উপার্জন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("324","6","deductions","Deductions","Deducciones","Déductions","কর্তন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("325","6","gross_salary","Gross Salary","Salario bruto","Salaire brut","মোট বেতন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("326","6","tax","Tax","Impuesto","Impôt","কর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("327","6","net_salary","Net Salary","Sueldo neto","Salaire net","মোট বেতন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("328","6","to","To","A","À","থেকে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("329","6","apply_date","Apply date","Fecha de aplicación","Date dapplication","তারিখ প্রয়োগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("330","6","pending","Pending","Pendiente","en attendant","মুলতুবী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("331","6","approved","Approved","Aprobado","Approuvé","অনুমোদিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("332","6","cancelled","Cancelled","Cancelado","Annulé","বাতিল করা হয়েছে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("333","6","leave_from","Leave From","Dejar de","Partir de","থেকে ত্যাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("334","6","leave_to","Leave to","Dejar","Laisser à","ছেড়ে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("335","6","reason","Reason","Razón","Raison","কারণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("336","6","leave","Leave","Salir","Laisser","ত্যাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("337","6","type_name","Type Name","Escribe un nombre","Nom du type","নাম লিখুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("338","6","total_days","Total Days","Días totales","Nombre total de jours","মোট দিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("339","6","leave_type_list","Leave Type List","Deja la lista de tipos","Quitter la liste des types","টাইপ তালিকা ছেড়ে দিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("340","6","departments","Departments","Departamentos","Départements","বিভাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("341","6","department_name","Department Name","Nombre de Departamento","Nom du département","বিভাগ নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("342","6","designations","Designations","Designaciones","Désignations","প্রশিক্ষণে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("343","6","staffs_payroll","Staffs Payroll","Nómina de personal","Personnel","স্টাফ Payroll","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("344","6","staff_no","Staff No","Personal No","Numéro du personnel","স্টাফ নং","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("345","6","date_of_joining","Date of Joining","Fecha de inscripción","Date dadhésion","যোগদানের তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("346","6","value","Value","Valor","Valeur","মান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("347","6","payroll_summary","Payroll Summary","Resumen de nómina","Résumé de la paie","Payroll সারাংশ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("348","6","calculate","calculate","calcular","calculer","গণনা করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("349","6","earning","Earning","Ganador","Revenus","রোজগার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("350","6","deduction","Deduction","Deducción","Déduction","সিদ্ধান্তগ্রহণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("351","6","submit","Submit","Enviar","Soumettre","জমা দিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("352","6","edit_staff","Edit Staff","Editar Personal","Modifier le personnel","স্টাফ সম্পাদনা করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("353","6","basic_info","Basic Info","Información básica","Informations de base","মৌলিক তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("354","6","staff_number","Staff Number","Numero de personal","Numéro du personnel","স্টাফ সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("355","6","emergency_mobile","Emergency Mobile","Móvil de emergencia","Mobile durgence","জরুরী মোবাইল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("356","6","current_address","Current Address","Direccion actual","Adresse actuelle","বর্তমান ঠিকানা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("357","6","permanent_address","Permanent Address","dirección permanente","Adresse permanente","স্থায়ী ঠিকানা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("358","6","qualifications","Qualifications","Calificaciones","Qualifications","যোগ্যতা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("359","6","experience","Experience","Experiencia","Expérience","অভিজ্ঞতা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("360","6","payroll_details","Payroll Details","Detalles de la nómina","Détails de la paie","পেপার বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("361","6","epf_no","EPF NO","EPF NO","EPF NO","ইপিএফ নং","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("362","6","bank_info_details","Bank Info Details","Detalles de la información del banco","Informations bancaires","ব্যাংক তথ্য বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("363","6","bank_account_name","Bank Account Name","Nombre de la cuenta bancaria","Nom du compte bancaire","ব্যাংক হিসাব নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("364","6","branch_name","Branch Name","Nombre de la rama","Nom de la filiale","শাখার নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("365","6","social_links_details","Social Links Details","Detalles de enlaces sociales","Liens sociaux Détails","সামাজিক লিঙ্ক বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("366","6","facebook_url","Facebook Url","Facebook URL","Ladresse URL de Facebook","ফেসবুক ইউআরএল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("367","6","twitter_url","Twitter Url","URL de Twitter","URL de Twitter","টুইটার ইউআরএল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("368","6","linkedin_url","Linkedin Url","Linkedin url","URL de Linkedin","লিঙ্কডিন ইউআরএল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("369","6","instragram_url","Instragram Url","Url de instagram","URL Instragram","ইনস্ট্রগ্রাম ইউআরএল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("370","6","update_staff","Update Staff","Personal de actualización","Mettre à jour le personnel","আপডেট স্টাফ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("371","7","leave","Leave","Salir","Laisser","ত্যাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("372","7","leave_define","Leave Define","Dejar definir","Quitter Définir","Define ছেড়ে দিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("373","7","my_remaining_leaves","My Remaining Leaves","Mis hojas restantes","Mes feuilles restantes","আমার অবশিষ্ট পাতা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("374","7","remaining_days","Remaining Days","Días restantes","Jours restants","বাকি দিনগুলো","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("375","7","extra_taken","Extra Taken","Extra Taken","Extra pris","অতিরিক্ত নেওয়া","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("376","7","total_days","Total Days","Días totales","Nombre total de jours","মোট দিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("377","7","days","Days","Dias","Journées","দিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("378","8","examination","Examination","Examen","Examen","পরীক্ষা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("379","8","exam","Exam","Examen","Examen","পরীক্ষা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("380","8","add_exam_type","Add Exam Type","Añadir tipo de examen","Ajouter un type dexamen","পরীক্ষার ধরন যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("381","8","exam_schedule","Exam Schedule","Horario del examen","Calendrier des examens","পরীক্ষার সময়সূচি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("382","8","marks_register","Marks Register","Registro de marcas","Registre des marques","চিহ্ন নিবন্ধন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("383","8","seat_plan","Seat Plan","Plan de asiento","Plan de siège","আসন পরিকল্পনা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("384","8","exam_attendance","Exam Attendance","Examen de asistencia","Présence à lexamen","পরীক্ষা উপস্থিতি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("385","8","marks_grade","Marks Grade","Nota de calificaciones","Note de marques","মার্ক গ্রেড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("386","8","send_marks_by_sms","Send Marks By Sms","Enviar marcas por sms","Envoyer des marques par sms","এসএমএস দ্বারা চিহ্ন পাঠান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("387","8","question_group","Question Group","Grupo de preguntas","Groupe de questions","প্রশ্ন গ্রুপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("388","8","question_bank","Question Bank","Banco de preguntas","Banque de questions","প্রশ্ন ব্যাংক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("389","8","online_exam","Online Exam","Examen en linea","Examen en ligne","অনলাইন পরীক্ষা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("390","8","exam_type","Exam Type","Tipo de examen","Type dexamen","পরীক্ষার ধরন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("391","8","exam_setup","Exam Setup","Configuración del examen","Configuration de lexamen","পরীক্ষা সেটআপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("392","8","exam_name","Exam Name","Nombre del examen","Nom de lexamen","পরীক্ষার নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("393","8","sl","Sl","Sl","Sl","ক্রমিক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("394","8","select_subjects","Select Subjects","Temas seleccionados","Sélectionner des sujets","বিষয় নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("395","8","exam_mark","Exam Mark","Marca de examen","Marque dexamen","পরীক্ষার চিহ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("396","8","add_mark_distributions","Add Mark Distributions","Añadir Distribuciones de Marca","Ajouter des distributions de marques","মার্ক বিতরণ যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("397","8","exam_title","Exam Title","Título del examen","Titre de lexamen","পরীক্ষার শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("398","8","ct_AT_Exam","Name","Nombre","prénom","নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("399","8","mark_distribution","Mark Distribution","Distribución de marcas","Distribution des marques","মার্ক বিতরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("400","8","subject","Subject","Tema","Assujettir","বিষয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("401","8","total_mark","Total Mark","Marca total","Total Mark","মোট চিহ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("402","8","view_status","View Status","Ver el estado de","Voir le statut","অবস্থা দেখুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("403","8","copy","Copy","Dupdo","Copie","কপি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("404","8","add_exam_schedule","add Exam Schedule","añadir horario de exámenes","ajouter un calendrier dexamen","পরীক্ষার সময়সূচী যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("405","8","exam_list","Exam List","Lista de exámenes","Liste dexamen","পরীক্ষা তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("406","8","marks","Marks","Marcas","Des notes","চিহ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("407","8","select_exam","Select Exam","Seleccionar examen","Sélectionnez un examen","পরীক্ষা নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("408","8","percent","Percent","Por ciento","Pour cent","শতাংশ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("409","8","seat_plan_report","Seat Plan Report","Informe del plan de asiento","Rapport de plan de siège","আসন পরিকল্পনা রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("410","8","assign_students","Assign Students","Asignar estudiantes","Attribuer des étudiants","ছাত্র নিয়োগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("411","8","start_end_time","start-end time","hora de inicio y fin","heure de début","শুরু শেষ সময়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("412","8","total_students","Total Students","Total de estudiantes","Total des étudiants","মোট ছাত্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("413","8","attendance_create","Attendance Create","Asistencia Crear","Présence Créer","উপস্থিতি তৈরি করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("414","8","grade","Grade","Grado","Qualité","শ্রেণী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("415","8","gpa","GPA","GPA","GPA","জিপিএ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("416","8","percent_from","Percent From","Porcentaje de","Pour cent de","থেকে শতাংশ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("417","8","percent_upto","Percent Upto","Por ciento hasta","Pourcentage jusquà","শতাংশ পর্যন্ত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("418","8","send_marks_via_SMS","Send Marks Via SMS","Enviar marcas a través de SMS","Envoyer des marques par SMS","এসএমএস মাধ্যমে চিহ্ন পাঠান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("419","8","select_receiver","Select Receiver","Seleccionar Receptor","Sélectionnez le destinataire","রিসিভার নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("420","8","students","Students","Estudiantes","Étudiants","শিক্ষার্থীরা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("421","8","select_group","Select Group","Selecciona grupo","Sélectionner un groupe","গ্রুপ নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("422","8","question_type","Question Type","tipo de pregunta","Type de question","প্রশ্নের ধরন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("423","8","multiple_choice","Multiple Choice","Opción multiple","Choix multiple","বহু নির্বাচনী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("424","8","true_false","True False","Verdadero Falso","Vrai faux","সত্য মিথ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("425","8","fill_in_the_blanks","Fill in the Blanks","Rellenar los espacios en blanco","Remplir les espaces vides","শুন্যস্তান পূরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("426","8","question","Question","Pregunta","Question","প্রশ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("427","8","number_of_options","Number Of Options","Número de opciones","Nombre doptions","বিকল্প সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("428","8","create","Create","Crear","Créer","সৃষ্টি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("429","8","option","option","opción","option","পছন্দ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("430","8","TRUE","TRUE","CIERTO","VRAI","সত্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("431","8","FALSE","FALSE","FALSO","FAUX","মিথ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("432","8","suitable_words","Suitable Words","Palabras adecuadas","Mots convenables","উপযুক্ত শব্দ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("433","8","start_time","Start Time","Hora de inicio","Heure de début","সময় শুরু","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("434","8","end_time","End time","Hora de finalización","Heure de fin","শেষ সময়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("435","8","minimum_percentage","Minimum Percentage","Porcentaje mínimo","Pourcentage minimum","নূন্যতম শতাংশ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("436","8","instruction","Instruction","Instrucción","Instruction","নির্দেশ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("437","8","exam_date","Exam Date","Fecha de examen","Date de lexamen","পরীক্ষার তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("438","8","time","Time","Hora","Temps","সময়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("439","8","published","Published","Publicado","Publié","প্রকাশিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("440","8","manage_question","Manage Question","Gestionar pregunta","Gérer la question","প্রশ্ন পরিচালনা করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("441","8","published_now","Published Now","Publicado ahora","Publié maintenant","এখন প্রকাশিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("442","8","view_result","View Result","Ver resultado","Voir résultat","ফলাফল দেখুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("443","8","monday","Monday","lunes","Lundi","সোমবার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("444","8","tuesday","Tuesday","martes","Mardi","মঙ্গলবার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("445","8","wednesday","Wednesday","miércoles","Mercredi","বুধবার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("446","8","thursday","Thursday","jueves","Jeudi","বৃহস্পতিবার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("447","8","friday","Friday","viernes","Vendredi","শুক্রবার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("448","8","Saturday","Saturday","sábado","samedi","শনিবার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("449","8","sunday","Sunday","domingo","dimanche","রবিবার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("450","8","room_number","Room Number","Número de habitación","Numéro de chambre","রুম সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("451","8","not_scheduled","Not Scheduled","No programada","Non prévu","নির্ধারিত না","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("452","8","result_view","Result View","Vista de resultados","Résultat","ফলাফল দেখুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("453","8","total_marks","Total Marks","Notas totales","Total des notes","মোট চিহ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("454","8","obtained_marks","Obtained Marks","Marcas obtenidas","Obtenu Marques","প্রাপ্ত মার্কস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("455","8","marking","Marking","Calificación","Marquage","অবস্থানসূচক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("456","8","view_answer_marking","View answer & marking","Ver respuesta y marcado","Voir la réponse et le marquage","উত্তর এবং চিহ্নিতকরণ দেখুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("457","8","online_exam_question","Online Exam Question","Pregunta de examen en línea","Question dexamen en ligne","অনলাইন পরীক্ষা প্রশ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("458","8","question_list","Question List","Lista de preguntas","Liste de questions","প্রশ্ন তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("459","8","questions","Questions","Preguntas","Des questions","প্রশ্নাবলি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("460","8","exam_details","Exam Details","Detalles del examen","Détails de lexamen","পরীক্ষা বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("461","8","passing_percentage","Passing Percentage","Pasando el porcentaje","Passage Pourcentage","শতাংশ পাস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("462","8","online_active_exams","Online Active Exams","Exámenes activos en línea","Examens actifs en ligne","অনলাইন সক্রিয় পরীক্ষা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("463","8","take_exam","Take Exam","Tomar examen","Passer un examen","পরীক্ষা নিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("464","8","classes","Classes","Las clases","Des classes","ক্লাস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("465","8","exam_terms","Exam Terms","Términos del examen","Termes de lexamen","পরীক্ষার শর্তাবলী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("466","8","documents","documents","documentos","documents","কাগজপত্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("467","8","timeline","Timeline","Línea de tiempo","Chronologie","সময়রেখা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("468","8","Parent_Guardian_Details","Parent / Guardian Details","Detalles de padres / tutores","Détails sur le parent / tuteur","পিতামাতা / অভিভাবক বিস্তারিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("469","8","full_marks","Full Marks","La máxima puntuación","La totalité des points","পুরো চিহ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("470","8","results","Results","Resultados","Résultats","ফলাফল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("471","8","visible_to_this_person","Visible to this person","Visible para esta persona","Visible à cette personne","এই ব্যক্তির কাছে দৃশ্যমান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("472","8","","","","","","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("473","9","academics","Academics","Académica","Les universitaires","শিক্ষাবিদগণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("474","9","class_routine","Class Routine","Rutina de clase","Routine de classe","ক্লাস রুটিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("475","9","class_routine_create","Class Routine Create","Rutina de clase Crear","Classe Routine Create","ক্লাস রুটিন তৈরি করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("476","9","view_teacher_routine","View Class Routine(Teacher)","Ver la rutina de la clase (profesor)","Voir la routine de classe (enseignant)","ক্লাস রুটিন দেখুন (শিক্ষক)","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("477","9","assign_subject","Assign Subject","Asignar Asunto","Attribuer un sujet","বিষয় বরাদ্দ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("478","9","assign_subject_create","Assign Subject create","Asignar Asunto crear","Assigner le sujet créer","বিষয় নির্ধারণ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("479","9","assign_class_teacher","Assign Class Teacher","Asignar profesor de clase","Attribuer un enseignant de classe","ক্লাস শিক্ষক নিয়োগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("480","9","subjects","Subjects","Asignaturas","Sujets","বিষয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("481","9","class","Class","Clase","Classe","শ্রেণী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("482","9","section","Sections","Secciones","Sections","সেকশনস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("483","9","class_room","Class Room","Salón de clases","Salle de cours","ক্লাস রুম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("484","9","n_a","N/A","N / A","N / A","এন / এ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("485","9","class_teacher","Class Teacher","Profesor de la clase","Professeur de classe","শ্রেণী শিক্ষক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("486","9","assign_teacher","Assign teacher","Asignar maestro","Assigner un enseignant","শিক্ষক নিয়োগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("487","9","subject_name","Subject Name","Nombre del tema","Nom du sujet","বিষয় নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("488","9","theory","Theory","Teoría","Théorie","তত্ত্ব","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("489","9","practical","Practical","Práctico","Pratique","ব্যবহারিক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("490","9","subject_code","Subject Code","Código del Asunto","Code de sujet","বিষয় কোড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("491","9","subject_type","Subject Type","Tipo de asunto","Type de sujet","বিষয় প্রকার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("492","9","capacity","Capacity","Capacidad","Capacité","ধারণক্ষমতা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("493","9","cl_ex_time_setup","Cl/Ex Time Setup","Cl / Ex Configuración de hora","Configuration de lheure Cl / Ex","ক্ল / প্রাক্তন সময় সেটআপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("494","9","class_exam_time_setup","Class & Exam Time Setup","Configuración de clase y tiempo de examen","Configuration du temps de cours et dexamen","ক্লাস এবং পরীক্ষার সময় সেটআপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("495","9","class_time","Class Time","Hora de clase","Le moment daller en classe","ক্লাস সময়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("496","9","time_type","Time Type","Tipo de tiempo","Type de temps","টাইম টাইপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("497","9","exam_time","Exam Time","Tiempo de examen","Temps dexamen","পরীক্ষার সময়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("498","9","period","Period","Período","Période","কাল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("499","9","select_time","Select Time","Seleccione tiempo","Sélectionnez lheure","সময় নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("500","9","not_assigned_yet","Not assigned yet","Aún no asignado","Pas encore assigné","এখনো বরাদ্দ করা হয় নি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("501","10","home_work","HomeWork","Deberes","Devoirs","বাড়ির কাজ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("502","10","add_homework","Add Homework","Añadir tarea","Ajouter des devoirs","বাড়ির কাজ যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("503","10","homework_list","Homework List","Lista de tareas","Liste de devoirs","হোমওয়ার্ক তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("504","10","evaluation_report","Homework Evaluation Report","Informe de evaluación de tareas","Rapport dévaluation des devoirs","হোমওয়ার্ক মূল্যায়ন রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("505","10","submission","Submission","Sumisión","Soumission","নমন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("506","10","attach_file","Attach File","Adjuntar archivo","Pièce jointe","ফাইল সংযুক্ত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("507","10","evaluation","Evaluation","Evaluación","Évaluation","মূল্যায়ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("508","10","created_by","Created By","Creado por","Créé par","দ্বারা সৃষ্টি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("509","10","complete","Complete","Completar","Achevée","সম্পূর্ণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("510","10","incomplete","Incomplete","Incompleto","Incomplet","অসম্পূর্ণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("511","11","communicate","Communicate","Comunicar","Communiquer","যোগাযোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("512","11","notice_board","Notice Board","Tablón de anuncios","Tableau daffichage","নোটিসবোর্ড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("513","11","send_message","Send Message","Enviar mensaje","Envoyer le message","বার্তা পাঠান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("514","11","send_email","Send Email / Sms","Enviar correo electrónico / SMS","Envoyer un email / sms","ইমেল / এসএমএস পাঠান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("515","11","email_sms_log","Email / Sms Log","Email / Sms Log","Journal Email / Sms","ইমেইল / এসএমএস লগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("516","11","event","Event","Evento","un événement","ঘটনা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("517","11","notices","Notices","Avisos","Les avis","নোটিশ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("518","11","notice","Notice","darse cuenta","Remarquer","বিজ্ঞপ্তি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("519","11","publish","Publish","Publicar","Publier","প্রকাশ করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("520","11","add_notice","Add Notice","Añadir aviso","Ajouter un avis","নোটিশ যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("521","11","add_a_notice","Add a Notice","Añadir un aviso","Ajouter un avis","একটি নোটিশ যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("522","11","publish_on","Publish On","Publicar en","Publier sur","প্রকাশ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("523","11","Send_Email_Sms","Send Email","Enviar correo electrónico","Envoyer un email","ইমেইল পাঠান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("524","11","sms","Sms","SMS","SMS","খুদেবার্তা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("525","11","individual","Individual","Individual","Individuel","স্বতন্ত্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("526","11","select_all","Select All","Seleccionar todo","Tout sélectionner","সব নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("527","11","For_Sending_Email","For Sending Email / Sms, It may take some seconds. So please take patience.","Para enviar correo electrónico / SMS, puede tardar unos segundos. Así que por favor ten paciencia.","Pour lenvoi demails / sms, cela peut prendre quelques secondes. Alors sil vous plaît prenez patience.","ইমেল / এসএমএস পাঠানোর জন্য, এটি কয়েক সেকেন্ড সময় নিতে পারে। তাই ধৈর্য ধরুন।","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("528","11","send","Send","Enviar","Envoyer","পাঠান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("529","11","start_date","Start Date","Fecha de inicio","Date de début","শুরুর তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("530","11","to_date","To Date","Hasta la fecha","À ce jour","এখন পর্যন্ত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("531","11","from_date","from Date","partir de la fecha","partir de la date","তারিখ হইতে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("532","11","details","Details","Detalles","Détails","বিস্তারিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("533","11","notice_date","Notice Date","Fecha de notificacion","Date davis","নোটিশ তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("534","11","update_content","Update content","Actualizar contenido","Mettre à jour le contenu","কন্টেন্ট আপডেট করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("535","12","library","Library","Biblioteca","Bibliothèque","গ্রন্থাগার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("536","12","add_book","Add Book","Añadir libro","Ajouter un livre","বই যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("537","12","book_list","Book List","Lista de libros","Liste de livres","বইএর তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("538","12","book_category","Book Categories","Categorías de libros","Catégories de livre","বই বিভাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("539","12","library_member","Add Member","Añadir miembro","Ajouter un membre","সদস্য যুক্ত করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("540","12","member_list","Issue/Return Book","Libro de emisión / devolución","Livre démission / retour","ইস্যু / রিটার্ন বুক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("541","12","all_issued_book","All Issued Book","Todo el libro publicado","Tous les livres publiés","সব প্রকাশিত বই","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("542","12","edit_book","Edit Book","Editar libro","Editer le livre","বই সম্পাদনা করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("543","12","book","Book","Libro","Livre","বই","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("544","12","book_title","Book Title","Titulo del libro","Titre de livre","বইয়ের শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("545","12","select_book_category","Select Book Category","Seleccionar categoría de libro","Sélectionnez une catégorie de livre","বই বিভাগ নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("546","12","isbn","ISBN","ISBN","ISBN","আইএসবিএন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("547","12","publisher","Publisher","Editor","Éditeur","প্রকাশক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("548","12","author_name","Author Name","Nombre del autor","Nom de lauteur","লেখকের নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("549","12","rack","Rack","Estante","Grille","তাক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("550","12","quantity","Quantity","Cantidad","Quantité","পরিমাণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("551","12","book_price","Book Price","Precio del libro","Prix ​​du livre","বই মূল্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("552","12","price","Price","Precio","Prix","মূল্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("553","12","category_name","Category Name","nombre de la categoría","Nom de catégorie","বিভাগ নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("554","12","add_member","Add Member","Añadir miembro","Ajouter un membre","সদস্য যুক্ত করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("555","12","member","Member","Miembro","Membre","সদস্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("556","12","member_type","Member Type","Tipo de miembro","Type de membre","সদস্য প্রকার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("557","12","select_student","Select Student","Seleccionar estudiante","Sélectionnez étudiant","ছাত্র নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("558","12","issue_books","Issue Books","Libros de emisión","Livres de questions","ইস্যু বই","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("559","12","full_name","Full Name","Nombre completo","Nom complet","পুরো নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("560","12","issue_return_Book","Issue / Return Book","Libro de emisión / devolución","Livre démission / retour","ইস্যু / রিটার্ন বুক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("561","12","issued_Book_List","Issued Book List","Lista de libros emitidos","Liste des livres publiés","ইস্যু করা বই তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("562","12","select_Book_Name","Select Book Name","Seleccione el nombre del libro","Sélectionnez le nom du livre","বুক নাম নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("563","12","search_By_Book_ID","Search By Book ID","Buscar por ID de libro","Rechercher par numéro de livre","বই আইডি দ্বারা অনুসন্ধান করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("564","12","author","Author","Autor","Auteur","লেখক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("565","12","library_book_issue","Library Book Issue","Número de libro de la biblioteca","Problème de livre de bibliothèque","লাইব্রেরী বই ইস্যু","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("566","12","staff_name","Staff Name","Nombre del personal","Nom du personnel","স্টাফ নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("567","12","select_book","Select Book","Seleccionar libro","Sélectionnez un livre","বই নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("568","12","issue_book","Issue Book","Libro de temas","Numéro de livre","ইস্যু বই","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("569","12","issued_book","Issued Book","Libro publicado","Livre publié","প্রকাশিত বই","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("570","12","book_number","Book Number","Número de libro","Numéro du livre","বই সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("571","12","status","Status","Estado","Statut","অবস্থা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("572","12","issue_date","Issue Date","Fecha de asunto","Date démission","প্রদানের তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("573","12","return_this_book","Are you sure to Return This Book ?","¿Seguro que regresas este libro?","Êtes-vous sûr de retourner ce livre?","আপনি এই বই ফেরত নিশ্চিত?","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("574","12","return_date","Return Date","Fecha de regreso","Date de retour","ফিরে তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("575","13","inventory","Inventory","Inventario","Inventaire","জায়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("576","13","item_category","Product Category","Categoría de artículo","Catégorie darticle","আইটেম বিভাগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("577","13","item_list","Product List","Lista de articulos","Liste des articles","উপকরণ তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("578","13","item_store","Product Store","Tienda de articulos","Magasin darticles","আইটেম স্টোর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("579","13","supplier","Vendor","Vendor","Vendor","সরবরাহকারী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("580","13","item_receive","Product Receive","El artículo recibe","Point recevoir","আইটেম প্রাপ্তি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("581","13","item_receive_list","Product Receive List","Lista de artículos recibidos","Item Receive List","আইটেম তালিকা প্রাপ্তি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("582","13","item_sell","Product Sell","Venta de artículos","Article Vendre","আইটেম বিক্রি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("583","13","item_issue","Product Issue","Emisión del artículo","Question darticle","আইটেম ইস্যু","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("584","13","select_item_category","Select Product Category","Seleccione la categoría del artículo","Sélectionner une catégorie darticle","আইটেম বিভাগ নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("585","13","selected","Selected","Seleccionado","Choisi","নির্বাচিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("586","13","total_in_stock","Total in Stock","Total en Stock","Total en stock","স্টক মোট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("587","13","store_name","Store Name","Nombre de la tienda","Nom du magasin","দোকানের নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("588","13","store_number","Store Number","Número de tienda","Numéro de magasin","স্টোর সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("589","13","company","Company","Empresa","Entreprise","কোম্পানির","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("590","13","contact_person_name","Contact Person Name","Nombre del Contacto","nom de contacte dune personne","যোগাযোগ ব্যক্তির নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("591","13","contact_person","Contact Person","Persona de contacto","Contact","যোগাযোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("592","13","receive_details","Receive Details","Recibir detalles","Recevoir les détails","বিস্তারিত পাবেন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("593","13","select_supplier","Select Supplier","Seleccionar Proveedor","Sélectionner un fournisseur","সরবরাহকারী নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("594","13","receive_date","Receive Date","Fecha de recepción","date de réception","গ্রহণের তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("595","13","product_name","Product Name","nombre del producto","Nom du produit","পণ্যের নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("596","13","unit_price","Unit Price","Precio unitario","Prix ​​unitaire","একক দাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("597","13","sub_total","Sub Total","Sub Total","Total partiel","সাব মোট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("598","13","full_paid","Full Paid","Completo pagado","Complet payé","সম্পূর্ণ পরিশোধিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("599","13","total_paid","Total Paid","Total pagado","Total payé","মোট দেওয়া","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("600","13","total_due","Total Due","Total a pagar","Total dû","মোট বাকি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("601","13","receive","Receive","Recibir","Recevoir","গ্রহণ করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("602","13","new","New","Nuevo","Nouveau","নতুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("603","13","total_quantity","Total Quantity","Cantidad total","Quantité totale","মোট পরিমাণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("604","13","partial_paid","Partial Paid","Parcial pagado","Partiellement payé","আংশিক প্রদত্ত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("605","13","unpaid","Unpaid","No pagado","Non payé","অবৈতনিক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("606","13","refund","Refund","Reembolso","Rembourser","প্রত্যর্পণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("607","13","buyer","Buyer","Comprador","Acheteur","ক্রেতা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("608","13","issue_item","Issue Product","Elemento de emisión","Point démission","সমস্যা আইটেম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("609","13","issue_a_item","Issue a Product","Emitir un artículo","Émettre un article","একটি আইটেম ইস্যু","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("610","13","user_type","User Type","Tipo de usuario","Type dutilisateur","ব্যবহারকারীর ধরন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("611","13","select_student_for_issue","Select Student For Issue","Seleccionar estudiante para su emisión","Sélectionner un étudiant pour lédition","সমস্যা জন্য ছাত্র নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("612","13","issue_to","Issue To","Emitido a","Issue to","ইস্যু","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("613","13","issued_item_list","Issued Product List","Lista de elementos emitidos","Liste darticles publiés","ইস্যু আইটেম তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("614","13","issued","Issued","Emitido","Publié","ইস্যু করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("615","13","returned","Returned","Devuelto","Revenu","ফেরৎ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("616","13","cancel_the_record","You are about to cancel the record. This cannot be undone. are you sure?","Estás a punto de cancelar el registro. Esto no se puede deshacer. ¿Estás seguro?","Vous êtes sur le point dannuler lenregistrement. Ça ne peut pas être annulé. êtes-vous sûr?","আপনি রেকর্ড বাতিল করতে চলেছেন। এটা অসম্পূর্ণ থাকতে পারে না. তুমি কি নিশ্চিত?","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("617","13","return","Return","Regreso","Revenir","প্রত্যাবর্তন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("618","13","purchase_details","Purchase Details","Detalles de la compra","Les détails dachat","ক্রয় বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("619","14","transport","Transport","Transporte","Transport","পরিবহন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("620","14","routes","Routes","Rutas","Itinéraires","রুট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("621","14","vehicle","Vehicle","Vehículo","Véhicule","বাহন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("622","14","assign_vehicle","Assign Vehicle","Asignar vehículo","Assigner un véhicule","যানবাহন বরাদ্দ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("623","14","student_transport_report","Student Transport Report","Informe de transporte de estudiantes","Rapport de transport étudiant","ছাত্র পরিবহন রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("624","14","transport_route","Transport Route","Ruta de transporte","Route de transport","পরিবহন রুট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("625","14","route","Route","Ruta","Route","রুট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("626","14","route_title","Route Title","Título de la ruta","Titre de la route","রুট শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("627","14","fare","Fare","Tarifa","Tarif","ভাড়া","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("628","14","model","Model","Modelo","Modèle","মডেল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("629","14","year_made","Year Made","Año hecho","Année de fabrication","বছর তৈরি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("630","14","select_driver","Select Driver","Seleccione Driver","Sélectionnez le pilote","ড্রাইভার নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("631","14","license","License","Licencia","Licence","লাইসেন্স","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("632","14","select_route","Select Route","Seleccione Ruta","Sélectionnez un itinéraire","রুট নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("633","14","select_vehicle","Select Vehicle","Seleccionar vehiculo","Choisir un véhicule","যানবাহন নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("634","14","father_phone","Fathers Phone","Telefono del padre","Téléphone du père","বাবা ফোন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("635","14","mother_name","Mothers Name","Nombre de la madre","Le nom de la mère","মায়ের নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("636","14","mother_phone","Mothers Phone","Teléfono de la madre","Téléphone de la mère","মা এর ফোন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("637","15","dormitory","Dormitory","Dormitorio","Dortoir","ছাত্রাবাস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("638","15","dormitory_rooms","Dormitory Rooms","Dormitorios","Dortoirs","ডরমিটার রুম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("639","15","room_type","Room Type","Tipo de habitación","Type de chambre","ঘরের বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("640","15","student_dormitory_report","Student Dormitory Report","Informe del dormitorio de estudiantes","Rapport du dortoir des étudiants","ছাত্র ডরমিটার রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("641","15","number_of_bed","Number Of Bed","Numero de cama","Nombre de lit","বিছানা সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("642","15","cost_per_bed","Cost Per Bed","Costo por cama","Coût par lit","বিছানা প্রতি খরচ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("643","15","no_of_bed","NO. OF BEd","NO. DE LA CAMA","NON. DE LIT","কোন বেড এর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("644","15","dormitory_list","Dormitory List","Lista de dormitorios","Liste des dortoirs","ডরমিটার তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("645","15","boys","Boys","Muchachos","Garçons","বয়েজ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("646","15","girls","Girls","Chicas","Filles","গার্লস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("647","15","intake","Intake","Consumo","Admission","ঘেরা জমি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("648","15","select_dormitory","Select Dormitory","Dormitorio selecto","Sélectionnez un dortoir","ডরমিটার নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("649","15","guardian_phone","Guardians Phone","Teléfono del guardián","Téléphone du gardien","গার্ডিয়ান ফোন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("650","16","reports","Reports","Informes","Rapports","প্রতিবেদন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("651","16","student_report","Student Report","Informe del estudiante","Rapport détudiant","ছাত্র রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("652","16","guardian_report","Guardian Reports","Informes del tutor","Rapports de gardien","গার্ডিয়ান রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("653","16","student_history","Student History","Historia del estudiante","Histoire des étudiants","ছাত্র ইতিহাস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("654","16","student_login_report","Student Login Report","Informe de inicio de sesión del estudiante","Rapport de connexion détudiant","ছাত্র লগইন রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("655","16","fees_statement","Fees Statement","Declaración de honorarios","Relevé des frais","ফি বিবৃতি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("656","16","balance_fees_report","Balance Fees Report","Informe de comisiones de saldo","Bilan des frais","ব্যালেন্স ফি রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("657","16","transaction_report","Transaction Report","Reporte de transacción","Rapport de transaction","লেনদেন রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("658","16","class_report","Class Report","Informe de clase","Rapport de classe","ক্লাস রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("659","16","merit_list_report","Merit List Report","Informe de la lista de méritos","Rapport de liste de mérite","মেধার তালিকা রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("660","16","online_exam_report","Online Exam Report","Informe de examen en línea","Rapport dexamen en ligne","অনলাইন পরীক্ষা রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("661","16","mark_sheet_report","Mark Sheet Report","Informe de hoja de marcas","Rapport de feuille de marque","মার্ক শীট রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("662","16","tabulation_sheet_report","Tabulation Sheet Report","Informe de hoja de tabulación","Rapport de tabulation","ট্যাবলেট শীট রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("663","16","student_fine_report","Student Fine Report","Informe de estudiante bien","Rapport de létudiant bien","ছাত্র ফাইন রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("664","16","user_log","User Log","Registro de usuario","Journal de lutilisateur","ব্যবহারকারী লগ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("665","16","exam_routine","Exam Routine","Rutina de examen","Routine dexamen","পরীক্ষা রুটিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("666","16","select_type","Select Type","Seleccione tipo","Sélectionner le genre","টাইপ নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("667","16","select_gender","Select Gender","Seleccione género","Sélectionnez le sexe","লিংগ নির্বাচন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("668","16","nid","NID","NID","NID","জাতীয় পরিচয়পত্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("669","16","Birth_Certificate_Number","Birth Certificate Number","Número de Certificado de Nacimiento","Numéro Acte de Naissance","জন্ম শংসাপত্র সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("670","16","select_admission_year","Select admission Year","Seleccione el año de admisión","Sélectionnez lannée dadmission","ভর্তি বছর নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("671","16","start_end","Start-End","Inicio fin","Début Fin","শুরু শেষ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("672","16","student_login_info","Student Login Info","Información de inicio de sesión del estudiante","Informations de connexion des étudiants","ছাত্র লগইন তথ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("673","16","login_info_report","Login Info Report","Informe de información de inicio de sesión","Login Info Report","লগইন তথ্য রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("674","16","username","Username","Nombre de usuario","Nom dutilisateur","ব্যবহারকারীর নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("675","16","password","Password","Contraseña","Mot de passe","পাসওয়ার্ড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("676","16","parent","Parent","Padre","Parent","মাতা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("677","16","reset","Reset","Reiniciar","Réinitialiser","রিসেট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("678","16","due_date","Due Date","Fecha de vencimiento","Date déchéance","নির্দিষ্ট তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("679","16","partial","Partial","Parcial","Partiel","আংশিক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("680","16","discount_of","Discount of","Descuento de","Remise de","ছাড়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("681","16","fees_report","Fees Report","Informe de tarifas","Rapport de frais","ফি রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("682","16","paid_fees","Paid Fees","Honorarios pagados","Frais payés","পরিশোধিত ফি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("683","16","fees_collection_details","Fees Collection Details","Detalles de la colección","Frais Collection Détails","ফি সংগ্রহ বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("684","16","number_of_student","Number Of Student","Numero de estudiante","Nombre détudiant","ছাত্র সংখ্যা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("685","16","total_subjects_assigned","Total Subjects assigned","Total de asignaturas asignadas","Nombre total de sujets assignés","মোট বিষয় বরাদ্দ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("686","16","collection","Collection","Colección","Collection","সংগ্রহ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("687","16","due","Due","Debido","Dû","দরুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("688","16","fees_details","Fees Details","Detalles de tarifas","Détails des frais","ফি বিবরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("689","16","class_routine_report","Class Routine Report","Informe de rutina de la clase","Rapport de routine de classe","ক্লাস রুটিন রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("690","16","report","Report","Informe","rapport","প্রতিবেদন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("691","16","teacher_class_routine_report","Teacher Class Routine Report","Informe de rutina para el maestro","Rapport de routine de classe denseignant","শিক্ষক ক্লাস রুটিন রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("692","16","select_teacher","Select Teacher","Seleccionar profesor","Sélectionnez un enseignant","শিক্ষক নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("693","16","school_management_system","School Management System","Sistema de gestión escolar","Système de gestion scolaire","স্কুল ম্যানেজমেন্ট সিস্টেম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("694","16","united_states_of_america","House 25, Road 27, Block B, 54th Floor, New York, United States of America","House 25, Road 27, Block B, 54th Floor, Nueva York, Estados Unidos de América","Maison 25, route 27, bloc B, 54ème étage, New York, États-Unis dAmérique","হাউস ২5, রোড 27, ব্লক বি, 54 তলা, নিউ ইয়র্ক, আমেরিকা যুক্তরাষ্ট্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("695","16","order_of_merit_list","Order of merit list","Lista de orden de mérito","Ordre de mérite","মেধার তালিকা আদেশ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("696","16","position","Position","Posición","Position","অবস্থান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("697","16","average","Average","Promedio","Moyenne","গড়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("698","16","obtained_marks","Obtained Marks","Marcas obtenidas","Obtenu Marques","প্রাপ্ত মার্কস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("699","16","top_obtained_mark","Top Obtained Mark","Mejor marca obtenida","Top obtenu la marque","শীর্ষ প্রাপ্ত মার্ক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("700","16","student_terminal_report","Student Terminal Report","Informe de terminal de estudiante","Rapport de fin détude","ছাত্র টার্মিনাল রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("701","16","progress_card_report","Progress card report","Informe de progreso","Rapport de carte de progression","অগ্রগতি কার্ড রিপোর্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("702","16","position_in_class","Position in Class","Posición en clase","Position en classe","ক্লাস অবস্থান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("703","16","class_test","Class Test","Prueba de clase","Test de classe","ক্লাস টেস্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("704","16","remarks","Remarks","Observaciones","Remarques","মন্তব্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("705","16","user","User","Usuario","Utilisateur","ব্যবহারকারী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("706","16","ip","IP","IP","IP","আইপি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("707","16","login_time","Login Time","Hora de inicio de sesión","Heure de connexion","লগইন সময়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("708","16","user_agent","User Agent","Agente de usuario","Agent utilisateur","ব্যবহারিক দূত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("709","17","system_settings","System Settings","Ajustes del sistema","Les paramètres du système","পদ্ধতি নির্ধারণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("710","17","general_settings","General Settings","Configuración general","réglages généraux","সাধারণ সেটিংস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("711","17","email_settings","Email Settings","Ajustes del correo electrónico","Paramètres de messagerie","ইমেল সেটিংস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("712","17","payment_method_settings","Payment Method Settings","Configuración del método de pago","Méthode de paiement","পেমেন্ট পদ্ধতি সেটিংস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("713","17","role","Role","Papel","Rôle","ভূমিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("714","17","base_group","Base Group","Grupo base","Groupe de base","বেস গ্রুপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("715","17","base_setup","Base Setup","Configuración de la base","Configuration de base","বেস সেটআপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("716","17","academic_year","Academic Year","Año académico","Année académique","শিক্ষাবর্ষ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("717","17","session","Session","Sesión","Session","সেশন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("718","17","holiday","Holiday","Vacaciones","Vacances","ছুটির দিন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("719","17","sms_settings","Sms Settings","Configuración de SMS","Paramètres Sms","এসএমএস সেটিংস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("720","17","language_settings","Language Settings","Configuraciones de idioma","Paramètres de langue","ভাষা ব্যাবস্থা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("721","17","backup_settings","Backup","Apoyo","Sauvegarde","ব্যাকআপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("722","17","select_language","Select Language","Seleccione el idioma","Choisir la langue","ভাষা নির্বাচন কর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("723","17","native","Native","Nativo","Originaire de","স্থানীয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("724","17","universal","Universal","Universal","Universel","সার্বজনীন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("725","17","make_default","Make Default","Hacer por defecto","Faire défaut","ডিফল্ট করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("726","17","setup","Setup","Preparar","Installer","সেটআপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("727","17","change_logo","Change Logo","Cambiar Logo","Changer le logo","লোগো পরিবর্তন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("728","17","change_fav","Change Favicon","Cambiar Favicon","Changer de favicon","পরিবর্তন ফেভিকন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("729","17","upload","Upload","Subir","Télécharger","আপলোড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("730","17","school_name","Business Name","Business Name","Business Name","Business Name","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("731","17","school_code","Business Code","Business Code","Business Code","Business","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("732","17","language","Language","Idioma","La langue","ভাষা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("733","17","date_format","Date Format","Formato de fecha","Format de date","তারিখ বিন্যাস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("734","17","currency","Currency","Moneda","Devise","মুদ্রা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("735","17","symbol","Symbol","Símbolo","symbole","প্রতীক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("736","17","sand","Sand","Arena","Le sable","বালি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("737","17","smtp","SMTP","SMTP","SMTP","SMTP এর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("738","17","from_name","From Name","De Nombre","De nom","নাম থেকে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("739","17","from_email","From Email","Desde el e-mail","De lemail","ইমেইল থেকে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("740","17","server","Server","Servidor","Serveur","সার্ভার","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("741","17","port","Port","Puerto","Port","বন্দর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("742","17","security","Security","Seguridad","Sécurité","নিরাপত্তা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("743","17","select_a_payment_gateway","Select a Payment Gateway","Seleccione una pasarela de pago","Sélectionnez une passerelle de paiement","একটি পেমেন্ট গেটওয়ে নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("744","17","checked","Checked","Comprobado","Vérifié","সংযত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("745","17","paypal","Paypal","Paypal","Pay Pal","পেপ্যাল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("746","17","stripe","Stripe","Raya","Bande","ডোরা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("747","17","payUMoney","PayUMoney","PayUMoney","PayUMoney","PayUMoney","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("748","17","signature","Signature","Firma","Signature","স্বাক্ষর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("749","17","client_id","Client ID","Identificación del cliente","identité du client","ক্লায়েন্ট আইডি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("750","17","secret_id","Secret ID","ID secreta","ID secret","গোপন আইডি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("751","17","stripe_api_secret_key","Stripe API Secret Key","Stripe API Secret Key","Clé secrète de lAPI de bande","স্ট্রিপ এপিআই গোপন কী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("752","17","stripe_publisher_key","Stripe Publishable Key","Stripe Publishable Key","Raie Clé Publiable","দাগ প্রকাশযোগ্য কী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("753","17","pay_u_money_key","PayU Money Key","PayU Money Key","Clé PayU Money","PayU টাকা কী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("754","17","pay_u_money_salt","PayU Money Salt","PayU Money Salt","PayU Money Salt","PayU টাকা লবণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("755","17","role_permission","Role Permission","Permiso de rol","Permission de rôle","ভূমিকা অনুমতি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("756","17","assign_permission","Assign Permission","Asignar permiso","Attribuer une autorisation","অনুমতি বরাদ্দ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("757","17","label","Label","Etiqueta","Étiquette","লেবেল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("758","17","base","Base","Base","Base","ভিত্তি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("759","17","year_title","Year Title","Título del año","Titre de lannée","বছর শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("760","17","starting_date","Starting Date","Fecha de inicio","Date de début","শুরু তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("761","17","ending_date","Ending Date","Fecha de finalización","Fin","শেষ তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("762","17","select_a_SMS_service","Select a SMS Service","Seleccione un servicio de SMS","Sélectionnez un service SMS","একটি এসএমএস সেবা নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("763","17","clickatell","Clickatell","Clickatell","Clickatell","Clickatell","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("764","17","settings","Settings","Ajustes","Réglages","সেটিংস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("765","17","twilio","Twilio","Twilio","Twilio","Twilio","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("766","17","api","API","API","API","এপিআই","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("767","17","sid","SID","SID","SID","জন্য SId","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("768","17","authentication","Authentication","Autenticación","Authentification","প্রমাণীকরণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("769","17","token","Token","Simbólico","Jeton","টোকেন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("770","17","registered_phone_number","Registered Phone Number","Número de teléfono registrado","Numéro de téléphone enregistré","নিবন্ধিত ফোন নম্বর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("771","17","authentication_key_SId","Authentication Key SId","Clave de autenticación SId","Clé dauthentification SId","প্রমাণীকরণ কী এসআইডি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("772","17","sender","Sender","Remitente","Expéditeur","প্রেরকের","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("773","17","country_code","Country Code","Código de país","Code postal","কান্ট্রি কোড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("774","17","select_serial","Select serial","Seleccione serial","Sélectionnez série","সিরিয়াল নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("775","17","day_list","Day list","Lista de días","Liste de jour","দিন তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("776","17","serial","Serial","De serie","En série","ক্রমিক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("777","17","upload_from_local_directory","Upload From Local Directory","Subir desde el directorio local","Télécharger depuis le répertoire local","স্থানীয় ডিরেক্টরি থেকে আপলোড করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("778","17","file","File","Expediente","Fichier","ফাইল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("779","17","cron_secret_key","Cron Secret Key","Clave secreta de Cron","Cron Secret Key","ক্রন সিক্রেট কী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("780","17","generate_key","Generate key","Generar clave","Générer une clé","কী জেনারেট করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("781","17","database_backup_list","Database Backup List","Lista de respaldo de la base de datos","Liste de sauvegarde de la base de données","ডাটাবেস ব্যাকআপ তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("782","17","backup","Backup","Apoyo","Sauvegarde","ব্যাকআপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("783","17","created_date_time","Created Date Time","Fecha de creación","Date de création heure","তৈরি তারিখ সময়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("784","17","backup_files","Backup Files","Archivos de respaldo","Fichiers de sauvegarde","ব্যাকআপ ফাইল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("785","17","weekend","Weekend","Fin de semana","Weekend","সপ্তাহান্তিক কাল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("786","17","restore","Restore","Restaurar","Restaurer","প্রত্যর্পণ করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("787","17","default","Default","Defecto","Défaut","ডিফল্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("788","17","module","Module","Módulo","Module","মডিউল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("789","17","module_link","Module Link","Enlace del módulo","Lien de module","মডিউল লিঙ্ক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("790","17","permission","Permission","Permiso","Autorisation","অনুমতি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("791","17","site_title","Title","Title","Title","সাইট শিরোনাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("792","17","select_session","Select Session","Seleccionar sesion","Sélectionnez une session","সেশন নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("793","17","select_date_format","Select Date Format","Seleccione el formato de fecha","Sélectionnez le format de date","তারিখ ফরম্যাট নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("794","17","select_currency","Select Currency","Seleccione el tipo de moneda","Sélectionnez la devise","কারেন্সি নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("795","17","currency_symbol","Currency Symbol","Símbolo de moneda","Symbole de la monnaie","মুদ্রা চিহ্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("796","17","school_address","Business Address","Business Address","Business Address","ঠিকানা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("797","17","update_language","Update Language","Actualizar idioma","Mise à jour de la langue","ভাষা আপডেট করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("798","17","language_setup","Language Setup","Configuración de idioma","Configuration de la langue","ভাষা সেটআপ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("799","18","front_end_settings","Front End Settings","Configuraciones frontales","Paramètres frontaux","ফ্রন্ট শেষ সেটিংস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("800","18","add_news","Add News","Añadir noticias","Ajouter des nouvelles","সংবাদ যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("801","18","news","News","Noticias","Nouvelles","খবর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("802","18","news_list","News List","Lista de noticias","Liste de nouvelles","সংবাদ তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("803","18","image","Image","Imagen","Image","ভাবমূর্তি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("804","18","publication_date","Publication Date","Fecha de publicación","Date de publication","প্রকাশনার তারিখ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("805","18","add_testimonial","Add Testimonial","Añadir Testimonial","Ajouter un témoignage","প্রশংসাপত্র যোগ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("806","18","testimonial","Testimonial","Testimonial","Témoignage","এজাহারনামা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("807","18","institution_name","Institution Name","Nombre de la Institución","nom de linstitution","প্রতিষ্ঠানের নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("808","18","location","Location","Ubicación","Emplacement","অবস্থান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("809","18","front_settings","Front Settings","Ajustes frontales","Paramètres avant","ফ্রন্ট সেটিংস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("810","19","my_profile","My Profile","Mi perfil","Mon profil","আমার প্রোফাইল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("811","19","fees","Fees","Matrícula","Honoraires","ফি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("812","19","pay_fees","Pay Fees","Cuotas de pago","Payer les frais","ফি পরিশোধ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("813","19","download_center","Download Center","Centro de descargas","centre de téléchargement","ডাউনলোড কেন্দ্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("814","19","student_study_material","Study Materials","Materiales de estudio","Matériel détudes","স্টাডি সামগ্রী","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("815","19","examinations","Examinations","Exámenes","Examens","পরীক্ষায়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("816","19","result","Result","Resultado","Résultat","ফল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("817","19","active_exams","Active Exams","Exámenes activos","Examens actifs","সক্রিয় পরীক্ষা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("818","19","book_issue","Book issued","Libro emitido","Livre publié","বই জারি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("819","19","my_children","My Children","Mis hijos","Mes enfants","আমার শিশু","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("820","19","exam_result","Exam Result","Resultado del examen","Résultat déxamen","পরীক্ষার ফলাফল","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("821","19","teacher_list","Teacher list","Lista de profesores","Liste des enseignants","শিক্ষক তালিকা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("822","19","inserted_message","Inserted Successfully","Insertado con éxito","Inséré avec succès","সফলভাবে সন্নিবেশ করানো হয়েছে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("823","19","updated_message","Updated Successfully","Actualizado exitosamente","Mis à jour avec succés","সফলভাবে আপডেট করা হয়েছে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("824","19","deleted_message","Deleted Successfully","Borrado exitosamente","Supprimé avec succès","সফলভাবে মুছে ফেলা হয়েছে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("825","19","inactive_message","Inactivated Successfully","Inactivado con éxito","Inactivé avec succès","সফলভাবে নিষ্ক্রিয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("826","19","active_message","Activated Successfully","Activado con éxito","Activé avec succès","সফলভাবে সক্রিয়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("827","19","backup_message","Backup Successfully","Copia de seguridad con éxito","Sauvegarde réussie","ব্যাকআপ সফলভাবে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("828","19","restore_message","Restore Successfully","Restaurar con éxito","Restaurer avec succès","সফলভাবে পুনরুদ্ধার করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("829","19","not_found_message","Ops! Data not Found","Ops! Datos no encontrados","Ops! Données non trouvées","অপস! তথ্য পাওয়া যায়নি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("830","19","error_message","Ops! Something went wrong, please try again","Ops! Algo salió mal. Por favor, vuelva a intentarlo","Ops! Une erreur sest produite. Veuillez réessayer","অপস! কিছু ভুল হয়েছে আবার চেষ্টা করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("831","19","front_cms","Front cms","Frente cms","Cms avant","ফ্রন্ট সিএমএস","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("832","19","update_system","Update System","Sistema de actualización","Système de mise à jour","আপডেট সিস্টেম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("833","19","System_Status","System Status","Estado del sistema","État du système","সিস্টেমের অবস্থা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("834","19","Upgrade","Upgrade","Mejorar","Améliorer","আপগ্রেড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("835","19","Version","Version","Versión","Version","সংস্করণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("836","19","Existing","Existing","Existente","Existant","বর্তমান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("837","19","Available","Available","Disponible","Disponible","সহজলভ্য","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("838","19","Alert","Alert","Alerta","Alerte","সতর্ক","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("839","19","New_Features","New Features","Nuevas características","Nouvelles fonctionnalités","নতুন বৈশিষ্ট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("840","19","copyright_text","Copyright Text","Texto de copyright","Texte de copyright","কপিরাইট টেক্সট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("841","20","point1","Your CSV data should be in the format download file. The first line of your CSV file should be the column headers as in the table example. Also make sure that your file is UTF-8 to avoid unnecessary encoding problems.","Sus datos CSV deben estar en el archivo de descarga de formato. La primera línea de su archivo CSV debe ser los encabezados de columna como en el ejemplo de la tabla. También asegúrese de que su archivo sea UTF-8 para evitar problemas de codificación innecesarios.","Vos données CSV doivent être dans le fichier de téléchargement au format. La première ligne de votre fichier CSV doit correspondre aux en-têtes de colonne, comme dans lexemple de tableau. Assurez-vous également que votre fichier est au format UTF-8 afin déviter des problèmes de codage inutiles.","আপনার CSV ডেটা ফরম্যাট ডাউনলোড ফাইলে থাকা উচিত। আপনার CSV ফাইলের প্রথম লাইন টেবিল উদাহরণের মতো কলাম শিরোনাম হওয়া উচিত। এছাড়াও আপনার ফাইলটি অপ্রয়োজনীয় এনকোডিং সমস্যাগুলি এড়ানোর জন্য UTF-8 নিশ্চিত করুন।","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("842","20","point2","If the column you are trying to import is date make sure that is formatted in format Y-m-d (2018-06-06).","Si la columna que está intentando importar es fecha, asegúrese de que esté formateada en el formato Y-m-d (2018-06-06).","Si la colonne que vous tentez dimporter est datée, assurez-vous quelle est formatée au format Y-m-d (2018-06-06).","আপনি যে কলামটি আমদানি করার চেষ্টা করছেন তা তারিখটি Y-m-d (2018-06-06) বিন্যাসে ফর্ম্যাট করা হয়েছে তা নিশ্চিত করার তারিখ।","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("843","20","point3","Duplicate \"Roll Number\" (unique in section) rows will not be imported. Roll No used or not you can get from student report page search on class & section","Las filas duplicadas de \"Número de rollo\" (único en la sección) no se importarán. Rollo No se utiliza o no se puede obtener de la página de informe del alumno en clase y sección","Les lignes en double \"Numéro de rouleau\" (uniques dans la section) ne seront pas importées. Pas de recherche doccasion ou non, vous pouvez obtenir une recherche de page de rapport d’étudiant dans la classe et la section","সদৃশ \"রোল নম্বর\" (বিভাগে অনন্য) সারি আমদানি করা হবে না। রোল নম্বর ব্যবহার করা হয়নি অথবা আপনি শ্রেণী এবং বিভাগে ছাত্র প্রতিবেদন পৃষ্ঠা অনুসন্ধান থেকে পেতে পারেন না","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("844","20","point4","Duplicate \"Guardian email & Guardian Phone\" rows will not be imported. Guardian email & Guardian Phone used or not you can get from student report page search on class & section","No se importarán filas duplicadas de \"Guardian email & Guardian Phone\". El correo electrónico de Guardian & Guardian Phone utilizado o no se puede obtener de la página de informe del alumno en la clase y sección","Les lignes dupliquées \"Email et téléphone Guardian\" ne seront pas importées. Guardian email & Guardian Phone utilisé ou non, vous pouvez obtenir une recherche dans la page de rapport de létudiant sur la classe et la section","সদৃশ গার্ডিয়ান ইমেল ও গার্ডিয়ান ফোন সারি আমদানি করা হবে না। গার্ডিয়ান ইমেল এবং গার্ডিয়ান ফোন ব্যবহার করা হয়েছে অথবা আপনি বিভাগ এবং বিভাগে ছাত্র প্রতিবেদন পৃষ্ঠা অনুসন্ধান থেকে পেতে পারেন না","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("845","20","point5","For student Session use Id","Para el estudiante Sesión use Id","Pour les étudiants \"Session\", utilisez lidentifiant","ছাত্র \"সেশন\" জন্য আইডি ব্যবহার করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("846","20","point6","For student \"Gender\" use ID","Para el estudiante \"Género\" usar ID","Pour létudiant \"Sexe\", utilisez lidentifiant","শিক্ষার্থী জন্য \"লিঙ্গ\" আইডি ব্যবহার করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("847","20","point7","For student \"Blood Group\" use Id","Para el estudiante \"Grupo de sangre\" use ID","Pour les étudiants Groupe sanguin, utilisez lId","ছাত্রদের জন্য রক্ত গ্রুপ আইডি ব্যবহার করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("848","20","point8","For student \"Religion\" use ID","Para el estudiante \"Religión\" usar identificación","Pour les étudiants Religion, utilisez votre identifiant","ছাত্রের জন্য ধর্ম আইডি ব্যবহার করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("849","20","point9","For student \"Guardian Relation\" use capital O for Other, F for Father M for Mother.","Para el estudiante \"Guardian Relation\" use mayúscula O para Otro, F para el Padre M para la Madre.","Pour les étudiants \"Relation Gardien\", utilisez la majuscule O pour Autre, F pour Père M pour Mère.","ছাত্রের জন্য \"গার্ডিয়ান রিলেশন\" অন্যের জন্য মূলধন ও ব্যবহার করুন, মায়ের জন্য ফাদার এম এর জন্য F।","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("850","20","save_bulk_students","save bulk students","guardar estudiantes a granel","sauver des étudiants en vrac","বাল্ক ছাত্র সংরক্ষণ করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("851","20","bank_account_number","Bank Account Number","Número de cuenta bancaria","Numéro de compte bancaire","ব্যাংক একাউন্ট নম্বর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("852","20","IFSC_Code","IFSC Code","Código IFSC","Code IFSC","আইএফএসসি কোড","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("853","20","payment_Id","Payment Id","ID de pago","ID de paiement","পেমেন্ট আইডি","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("854","20","passing_marks","Passing Marks","Marcas de paso","Marques de passage","পাশ নম্বর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("855","20","website","Website","Sitio web","Site Internet","ওয়েবসাইট","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("856","20","you_have","You have","Tienes","Tu as","তোমার আছে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("857","20","new","new","nuevo","Nouveau","নতুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("858","20","notification","notification","notificación","notification","প্রজ্ঞাপন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("859","20","mark_all_as_read","Mark All As Read","Marcar todo como leido","Tout marquer comme lu","সবগুলো পঠিত বলে সনাক্ত কর","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("860","20","view_profile","view profile","ver perfil","Voir le profil","প্রোফাইল দেখুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("861","20","completed","Completed","Terminado","Terminé","সম্পন্ন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("862","20","to_do_title","To Do Title","Para hacer titulo","Titre à faire","শিরোনাম করতে","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("863","20","Designation_of_Signature_person","Designation of Signature person","Designación de la persona de la firma","Désignation de la personne signataire","স্বাক্ষর ব্যক্তির নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("864","20","student_wise","Student Wise","Estudiante sabio","Étudiant sage","ছাত্র বুদ্ধিমান","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("865","20","print","print","impresión","impression","ছাপা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("866","20","discount_of","Discount of","Descuento de","Remise de","ছাড়","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("867","20","applied","Applied","Aplicado","Appliqué","ফলিত","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("868","20","fees_assign","Fees Assign","Asignar cuotas","Affectation des frais","ফি বরাদ্দ করা","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("869","20","cost_center","Cost Center","centro de costo","centre de coûts","খরচ কেন্দ্র","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("870","20","select_product","Select Product","Seleccionar producto","Sélectionner un produit","পণ্য নির্বাচন করুন","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("871","20","item_name","Product Name","Product Name","Item Name","আইটেম নাম","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("872","20","add_new_customer","Add New Customer","Add New Customer","Add New Customer","Add New Customer","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("873","20","customer_list","Customer List","Customer List","Customer List","Customer List","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("874","20","customer_info","Customer Information","Customer Information","Customer Information","Customer Information","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("875","20","add_customer","Add Customer","Add Customer","Add Customer","Add Customer","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("876","20","customer","Customer","Customer","Customer","Customer","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("877","20","tender","Tender","Tender","Tender","Tender","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("878","20","tender","Tender","Tender","Tender","Tender","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("879","20","tender_no","Tender No","Tender No","Tender No","Tender No","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("880","20","work_order_date","Work Order Date","Work Order Date","Work Order Date","Work Order Date","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("881","20","delivery_date","Delivery Date","Delivery Date","Delivery Date","Delivery Date","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("882","20","bid_amount","Bid Amount","Bid Amount","Bid Amount","Bid Amount","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("883","20","discount_amount","Discount Amount","Discount Amount","Discount Amount","Discount Amount","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("884","20","work_order_no","Work Order No","Work Order No","Work Order No","Work Order No","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("885","20","bid","Bid","Bid","Bid","Bid","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("886","20","vendor","Vendor","Vendor","Vendor","Vendor","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("887","20","percentage","Percentage","Percentage","Percentage","Percentage","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("888","20","work","Work","Work","Work","Work","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("889","20","open_date","Open Date","Open Date","Open Date","Open Date","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("890","20","open","Open","Open","Open","Open","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("891","20","opening","Opening","Opening","Opening","Opening","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("892","20","order","Order","Order","Order","Order","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("893","20","letter","Letter","Letter","Letter","Letter","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("894","20","is_approved","Approved Status","Approved Status","Approved Status","Approved Status","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("895","20","delivery","Delivery","Delivery","Delivery","Delivery","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("896","20","up_coming","Upcoming","Upcoming","Upcoming","Upcoming","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("897","20","tender_result","Tender Result","Tender Result","Tender Result","Tender Result","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("898","20","product_receive","Product Receive","Product Receive","Product Receive","Product Receive","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("899","20","supplier_name","Supplier Name","Supplier Name","Supplier Name","Supplier Name","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("900","20","receive_date","Receive Date","Receive Date","Receive Date","Receive Date","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("901","20","received_date","Received Date","Received Date","Received Date","Received Date","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("902","20","part_number","Part Number","Part Number","Part Number","Part Number","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("903","20","new_part_number","New Part Number","New Part Number","New Part Number","New Part Number","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("904","20","denomination","Denomination","Denomination","Denomination","Denomination","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("905","20","product_receive","Product Receive","Product Receive","Product Receive","Product Receive","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("906","20","product_receive_list","Product Receive List","Product Receive List","Product Receive List","Product Receive List","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("907","20","application","Application","Application","Application","Application","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("908","20","customers","Customers","Customers","Customers","Customers","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("909","20","vendors","Vendors","Vendors","Vendors","Vendors","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("910","20","stock","Stocks","Stocks","Stocks","Stocks","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("911","20","staffs","Staffs","Staffs","Staffs","Staffs","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("912","20","lead","Lead","Lead","Lead","Lead","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("913","20","add_lead","Add Lead","Add Lead","Add Lead","Add Lead","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("914","20","lead_generation","Lead Generation","Lead Generation","Lead Generation","Lead Generation","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("915","20","lead_setup","Lead Setup","Lead Setup","Lead Setup","Lead Setup","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("916","20","project","Project","Project","Project","Project","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("917","20","work_order","Work Order","Work Order","Work Order","Work Order","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("918","20","bank_details","Bank Details","Bank Details","Bank Details","Bank Details","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("919","20","online_payment_details","Payment Details","Payment Details","Payment Details","Payment Details","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("920","20","select_item_category","Select Product Category","Select Item Category","Select Item Category","Select Item Category","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("921","20","background","Background","Background","Background","Background","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("922","20","style","Style","Style","Style","Style","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("923","20","color","Color","Color","Color","Color","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("924","20","select_position","Select Position","Select Position","Select Position","Select Position","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("925","20","background_settings","Background Settings","Background Settings","Background Settings","Background Settings","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("926","20","background_type","Background Type","Background Type","Background Type","Background Type","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("927","20","course_list","Course List","Course List","Course List","Course List","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("928","20","about_us","About Us","About Us","About Us","About Us","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("929","20","custom_links","Custom Links","Custom Links","Custom Links","Custom Links","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("930","20","operation_success_message","Operation Successful","Operation Successful","Operation Successful","Operation Successful","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("931","20","home_page","Home Page","Home Page","Home Page","Home Page","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("932","20","payment_id","Payment ID","Payment ID","Payment ID","Payment ID","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("933","20","contact","Contact","Contact","Contact","Contact","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("934","20","page","Page","Page","Page","Page","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("935","20","SampleDataEmpty","Sample Data","Sample Data","Sample Data","Sample Data","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("936","20","date_formate","Date Format","Date Format","Date Format","Date Format","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("937","20","duration","Duration","Duration","Duration","Duration","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("938","20","subcategory","Subcategory","Subcategory","Subcategory","Subcategory","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("939","20","sub","Sub","Sub","Sub","Sub","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("940","20","section_sub_category","Section Subcategory","Section Subcategory","Section Subcategory","Section Subcategory","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("941","20","database","Database","Database","Database","Database","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("942","20","competitors","Competitors","Competitors","Competitors","Competitors","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("943","20","sub_chart_account","Sub Chart Account","Sub Chart Account","Sub Chart Account","Sub Chart Account","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("944","20","account_of_head","Account Of Head","Account Of Head","Account Of Head","Account Of Head","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("945","20","sub_chart_of_account","Sub Chart Of Account","Sub Chart Of Account","Sub Chart Of Account","Sub Chart Of Account","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("946","20","product","Product","Product","Product","Product","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("947","20","author_details","Author Details","Author Details","Author Details","Author Details","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("948","20","fixed","fixed","fixed","fixed","fixed","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("949","20","expired","Expired","Expired","Expired","মেয়াদোত্তীর্ণ","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("950","20","unit","Unit","Unit","Unit","Unit","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("951","20","won","Won","Won","Won","Won","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("952","20","short_form","Short Form","Short Form","Short Form","Short Form","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("953","20","unit_manage","Unit Manage","Unit Manage","Unit Manage","Unit Manage","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("954","20","brand_manage","Brand Manage","Brand Manage","Brand Manage","Brand Manage","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("955","20","manage","Manage","Manage","Manage","Manage","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("956","20","brand","Brand","Brand","Brand","Brand","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("957","20","make","Make","Make","Make","Make","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("958","20","accounts_name","Accounts Name","Accounts Name","Accounts Name","Accounts Name","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("959","20","shipping_tracking_number","Shipping Tracking Number","Shipping Tracking Number","Shipping Tracking Number","Shipping Tracking Number","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("960","20","shipping_mode","Shipping Mode","Shipping Mode","Shipping Mode","Shipping Mode","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("961","20","destination","Destination","Destination","Destination","Destination","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("962","20","tenders","Tenders","Tenders","Tenders","Tenders","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("963","20","lowest_bidder","Lowest Bidder","Lowest Bidder","Lowest Bidder","Lowest Bidder","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("964","20","user_activities","User Activities","User Activities","User Activities","User Activities","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("965","20","work_orders","Work Orders","Work Orders","Work Orders","Work Orders","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("966","20","enlisted_suppliers","Enlisted Suppliers","Enlisted Suppliers","Enlisted Suppliers","Enlisted Suppliers","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("967","20","contact_person","Contact Person","Contact Person","Contact Person","Contact Person","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("968","20","inspecting_departments","Inspecting Departments","Inspecting Departments","Inspecting Departments","Inspecting Departments","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("969","20","end_user_name","End User Name","End User Name","End User Name","End User Name","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("970","20","ticket_system","Ticket","Ticket","Ticket","Ticket","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("971","20","ticket_category","Category","Category","Category","Category","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("972","20","ticket_comment","Comment","Comment","Comment","Comment","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("973","20","ticket_priority","Priority","Priority","Priority","Priority","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("974","20","category_list","Category List","Category List","Category List","Category List","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("975","20","ticket_list","Ticket List","Ticket List","Ticket List","Ticket List","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("976","20","priority_list","Priority List","Priority List","Priority List","Priority List","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("977","20","add_category","Add Category","Add Category","Add Category","Add Category","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("978","20","add_ticket","Add Ticket","Add Ticket","Add Ticket","Add Ticket","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("979","20","add_priority","Add Priority","Add Priority","Add Priority","Add Priority","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("980","20","comment_reply","Reply","Reply","Reply","Reply","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("981","7","cash_issue","Cash Issue","Cash Issue","Cash Issue","Cash Issue","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("982","20","select","Select","Select","Select","Select","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("983","7","cash_issue","Cash Issue","Cash Issue","Cash Issue","Cash Issue","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("984","7","advance_salary","Advance Salary","Advance Salary","Advance Salary","Advance Salary","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("985","7","loan","Loan","Loan","Loan","Loan","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("986","7","deduct","Deduct","Deduct","Deduct","Deduct","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("987","7","deduct","Deduct","Deduct","Deduct","Deduct","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("988","7","advance","Advance","Advance","Advance","Advance","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("989","7","quotations","Quotations","Quotations","Quotations","Quotations","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("990","7","quotation","Quotation","Quotation","Quotation","Quotation","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("991","7","quotation_type","Quotation Type","Quotation Type","Quotation Type","Quotation Type","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("992","7","quotation_type","Quotation Type","Quotation Type","Quotation Type","Quotation Type","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("993","7","sign_up","Sign up","Sign up","Sign up","Sign up","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("994","7","login","Login","Login","Login","Login","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("995","7","profile","Profile","Profile","Profile","Profile","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("996","7","task","Task","Task","Task","Task","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("997","7","member","Member","Member","Member","Member","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("998","7","petty_cash","Petty Cash","Petty Cash","Petty Cash","Petty Cash","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("999","7","remaining_balance","Remaining Balance","Remaining Balance","Remaining Balance","Remaining Balance","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1000","7","details_report","Details Report","Details Report","Details Report","Details Report","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1001","7","invoice","Invoice","Invoice","Invoice","Invoice","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1002","7","statement","Statement","Statement","Statement","Statement","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1003","7","ledger","Ledger","Ledger","Ledger","Ledger","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1004","7","created","Created","Created","Created","Created","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1005","7","closing","Closing","Closing","Closing","Closing","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1006","7","sales","Sales","Sales","Sales","Sales","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1007","7","billing","Billing","Billing","Billing","Billing","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1008","7","shipping","Shipping","Shipping","Shipping","Shipping","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1009","7","investment","Investment","Investment","Investment","Investment","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1010","7","transfer","Transfer","Transfer","Transfer","Transfer","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1011","7","management","Management","Management","Management","Management","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1012","7","team","Team","Team","Team","Team","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1013","7","Are_you_sure_to_detete_this_item","Are you sure to detete this item","Are you sure to detete this item","Are you sure to detete this item","Are you sure to detete this item","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1014","7","owner","Owner","Owner","Owner","Owner","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1015","7","basic","Basic","Basic","Basic","Basic","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1016","7","no_","No.","No.","No.","No.","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1017","7","tasks","Tasks","Tasks","Tasks","Tasks","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1018","7","priority","Priority","Priority","Priority","Priority","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1019","7","Responsible","Responsible","Responsible","Responsible","Responsible","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1020","7","Online_Payment_Info_Details","Online Payment Info Details","Online Payment Info Details","Online Payment Info Details","Online Payment Info Details","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1021","7","my","My","My","My","My","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1022","7","marital","Marital","Marital","Marital","Marital","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1023","7","Addresses","Addresses","Addresses","Addresses","Addresses","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1024","7","ongoing","Ongoing","Ongoing","Ongoing","Ongoing","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1025","7","Attachment","Attachment","Attachment","Attachment","Attachment","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1026","7","confirm","Confirm","Confirm","Confirm","Confirm","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1027","7","change","Change","Change","Change","Change","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1028","7","close","Close","Close","Close","Close","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1029","7","client","Client","Client","Client","Client","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1030","7","setting","Setting","Setting","Setting","Setting","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1031","7","users","Users","Users","Users","Users","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1032","7","To_Do","To Do","To Do","To Do","To Do","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1033","7","Recent_project","Recent project","Recent project","Recent project","Recent project","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1034","7","time_zone","Time Zone","Time Zone","Time Zone","Time Zone","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1035","7","Recent_Upcoming_Tenders","Recent Upcoming Tenders","Recent Upcoming Tenders","Recent Upcoming Tenders","Recent Upcoming Tenders","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1036","7","Running_Projects","Running Projects","Running Projects","Running Projects","Running Projects","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1037","7","Complete_Projects","Complete Projects","Complete Projects","Complete Projects","Complete Projects","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1038","7","Work_Orders_This_Year","Work Orders This Year","Work Orders This Year","Work Orders This Year","Work Orders This Year","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1039","7","Yearly_Work_Order","Yearly Work Order","Yearly Work Order","Yearly Work Order","Yearly Work Order","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1040","7","Tenders_Won_This_Year","Tenders Won This Year","Tenders Won This Year","Tenders Won This Year","Tenders Won This Year","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1041","7","Yearly_Won_Tenders","Yearly Won Tenders","Yearly Won Tenders","Yearly Won Tenders","Yearly Won Tenders","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1042","7","Completed_Tenders","Completed Tenders","Completed Tenders","Completed Tenders","Completed Tenders","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1043","7","Work_Orders","Work Orders","Work Orders","Work Orders","Work Orders","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1044","7","Tenders_Won","Tenders Won","Tenders Won","Tenders Won","Tenders Won","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1045","7","Upcoming_Tenders","Upcoming Tenders","Upcoming Tenders","Upcoming Tenders","Upcoming Tenders","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1046","7","Task_Not_Assigned","Task Not Assigned","Task Not Assigned","Task Not Assigned","Task Not Assigned","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1047","7","projects","Projects","Projects","Projects","Projects","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_language_phrases VALUES("1048","7","teams","Teams","Teams","Teams","Teams","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");



DROP TABLE sm_languages;

CREATE TABLE `sm_languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `native` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_universal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT '1',
  `updated_by` int(11) DEFAULT '1',
  `lang_id` int(11) DEFAULT '1',
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_languages VALUES("1","English","English","en","1","1","1","1","1","2019-12-15 14:02:59","2019-12-17 14:08:15");
INSERT INTO sm_languages VALUES("2","Bengali","বাংলা","bn","0","1","1","9","1","2019-12-17 14:06:38","2019-12-17 14:07:20");
INSERT INTO sm_languages VALUES("3","Spanish","Español","es","0","1","1","20","1","2019-12-17 14:06:59","2019-12-17 14:08:15");



DROP TABLE sm_leave_defines;

CREATE TABLE `sm_leave_defines` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(4) DEFAULT NULL,
  `type_id` tinyint(4) DEFAULT NULL,
  `days` int(11) DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_leave_defines VALUES("1","1","1","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("2","1","2","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("3","1","3","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("4","1","4","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("5","1","5","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("6","1","6","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("7","1","7","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("8","2","1","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("9","2","2","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("10","2","3","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("11","2","4","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("12","2","5","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("13","2","6","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("14","2","7","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("15","3","1","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("16","3","2","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("17","3","3","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("18","3","4","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("19","3","5","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("20","3","6","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("21","3","7","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("22","4","1","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("23","4","2","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("24","4","3","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("25","4","4","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("26","4","5","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("27","4","6","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("28","4","7","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("29","5","1","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("30","5","2","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("31","5","3","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("32","5","4","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("33","5","5","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("34","5","6","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("35","5","7","10","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_leave_defines VALUES("36","3","8","100","1","1","1","2019-12-17 12:02:05","2019-12-17 12:02:05");



DROP TABLE sm_leave_requests;

CREATE TABLE `sm_leave_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `leave_define_id` int(11) DEFAULT NULL,
  `staff_id` int(10) unsigned DEFAULT NULL,
  `role_id` int(10) unsigned DEFAULT NULL,
  `apply_date` date DEFAULT NULL,
  `type_id` tinyint(4) DEFAULT NULL,
  `leave_from` date DEFAULT NULL,
  `leave_to` date DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `note` text COLLATE utf8mb4_unicode_ci,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approve_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'P for Pending, A for Approve, R for reject',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_leave_types;

CREATE TABLE `sm_leave_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_days` int(10) unsigned DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_leave_types VALUES("1","Casual Leave","10","1","1","","","","");
INSERT INTO sm_leave_types VALUES("2","Sick Leave","14","1","1","","","","");
INSERT INTO sm_leave_types VALUES("3","Annual/Vacation Leave","10","1","1","","","","");
INSERT INTO sm_leave_types VALUES("4","Earned Leave","10","1","1","","","","");
INSERT INTO sm_leave_types VALUES("5","Public holidays","20","1","1","","","","");
INSERT INTO sm_leave_types VALUES("6","Maternity/Paternity","7","1","1","","","","");
INSERT INTO sm_leave_types VALUES("7","Administrative leave","5","1","1","","","","");
INSERT INTO sm_leave_types VALUES("8","test 99","","1","1","","","2019-12-17 12:01:02","2019-12-17 12:01:02");



DROP TABLE sm_module_links;

CREATE TABLE `sm_module_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` tinyint(4) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT '1',
  `updated_by` int(11) DEFAULT '1',
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=362 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_module_links VALUES("1","1","Dashboard Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("2","1","➡ Number of Staff Section","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("3","1","➡ Number of Tenders Won","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("4","1","➡ Number of upcoming Tender","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("5","1","➡ Number of Complete Tender","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("6","1","➡ Recent Tender List","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("7","1","➡ Monthly Income and Expenses Chart","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("8","1","➡ Yearly Income and Expenses Chart","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("9","1","➡ Notice Board Section","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("10","1","➡ Calendar Section","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("11","1","➡ To Do list","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("12","2","Lead Generation Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("13","2","Create Lead Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("14","2","➡ Lead Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("15","2","➡ Lead Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("16","2","➡ Lead Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("25","2","Lead Setup Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("26","2","➡ Lead Setup Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("27","2","➡ Lead Setup Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("28","2","➡ Lead Setup Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("41","3","Accounts Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("42","3","Profit Report Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("43","3","➡ Profit Search","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("44","3","➡ Profit Edit","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("45","3","➡ Profit Delete","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("46","3","Income Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("47","3","➡ Income Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("48","3","➡ Income Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("49","3","➡ Income Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("50","3","Expense Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("51","3","➡ Expense Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("52","3","➡ Expense Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("53","3","➡ Expense Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("54","3","Search","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("55","3","Chart of Account Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("56","3","➡ Chart of Account Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("57","3","➡ Chart of Account Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("58","3","➡ Chart of Account Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("59","3","Cost Center Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("60","3","➡ Cost Center Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("61","3","➡ Cost Center Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("62","3","➡ Cost Center Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("63","3","Payment Method Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("64","3","➡ Payment Method Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("65","3","➡ Payment Method Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("66","3","➡ Payment Method Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("67","3","Bank Account Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("68","3","➡ Bank Account Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("69","3","➡ Bank Account Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("70","3","➡ Bank Account Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("71","3","Debit Credit Voucher Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("72","3","➡ Debit Credit Voucher Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("73","3","➡ Debit Credit Voucher Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("74","3","➡ Debit Credit Voucher Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("75","4","Tender Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("76","4","Work Orders Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("77","4","➡ Work Orders Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("78","4","➡ Work Orders Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("79","4","➡ Work Orders Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("80","4","Upcoming Tender Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("81","4","➡ Upcoming Tender Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("82","4","➡ Upcoming Tender Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("83","4","➡ Upcoming Tender Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("84","5","Human Resource Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("85","5","Staff Directory Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("86","5","➡ Staff Directory Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("87","5","➡ Staff Directory Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("88","5","➡ Staff Directory Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("89","5","Staff Attendance Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("90","5","➡ Staff Attendance Search","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("91","5","➡ Staff Attendance Save","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("92","5","➡ Staff Attendance Delete","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("93","5","Staff Attendance Report Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("94","5","Payroll Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("95","5","➡ Payroll Edit","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("96","5","➡ Payroll Delete","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("97","5","➡ Payroll Search","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("98","5","➡ Generate Payroll","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("99","5","➡ Payroll Create","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("100","5","➡ Payroll Proceed To Pay","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("101","5","➡ View Payslip","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("102","5","Payroll Report Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("103","5","➡ Payroll Report Search","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("104","5","Designations Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("105","5","➡ Designations Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("106","5","➡ Designations Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("107","5","➡ Designations Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("108","5","Departments Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("109","5","➡ Departments Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("110","5","➡ Departments Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("111","5","➡ Departments Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("112","6","Leave Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("113","6","Approve Leave Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("114","6","➡ Approve Leave Add","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("115","6","➡ Approve Leave view-Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("116","6","➡ Approve Leave Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("117","6","Apply Leave Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("118","6","➡ Apply Leave Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("119","6","➡ Apply Leave Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("120","6","➡ Apply Leave Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("121","6","Apply Define Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("122","6","➡ Apply Define Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("123","6","➡ Apply Define Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("124","6","➡ Apply Define Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("125","6","Apply Type Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("126","6","➡ Apply Type Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("127","6","➡ Apply Type Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("128","6","➡ Apply Type Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("129","7","Communicate Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("130","7","Notice Board Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("131","7","➡ Notice Board Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("132","7","➡ Notice Board Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("133","7","➡ Notice Board Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("134","7","Send Message Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("135","7","Send Email Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("136","7","➡ Send Email","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("137","7","Email-Sms Log List Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("138","7","Event List Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("139","7","➡ Event List Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("140","7","➡ Event List Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("141","7","➡ Event List Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("142","8","User Info Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("143","8","Create Customer Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("144","8","➡ Customer Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("145","8","➡ Customer Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("146","8","➡ Customer Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("147","8","➡ Customer Profile View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("148","9","Inventory Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("149","9","Product Category Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("150","9","➡ Product Category Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("151","9","➡ Product Category Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("152","9","➡ Product Category Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("153","9","Product List Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("154","9","➡ Product Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("155","9","➡ Product Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("156","9","➡ Product Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("157","9","Product Store Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("158","9","➡ Product Store Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("159","9","➡ Product Store Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("160","9","➡ Product Store Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("161","9","Supplier Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("162","9","➡ Supplier Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("163","9","➡ Supplier Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("164","9","➡ Supplier Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("165","9","Product Receive List Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("166","9","➡ Product Receive List Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("167","9","➡ Product Receive List Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("168","9","➡ Product Receive List Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("169","9","Product Sale Menu","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("170","9","➡ Product Sale Add","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("171","9","➡ Product Sale Edit","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("172","9","➡ Product Sale Delete","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("173","9","Product Issue Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("174","9","➡ Product Issue Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("175","9","➡ Product Issue Return","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("176","9","➡ Product Issue Delete","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("177","10","Reports Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("178","10","Transaction Report Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("179","10","➡ Transaction Report","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("180","10","➡ User Log","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("181","11","System Settings Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("182","11","General Setting Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("183","11","➡ Change Logo","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("184","11","➡ Change Favicon","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("185","11","➡ General Setting View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("186","11","➡ General Setting Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("187","11","Email Settings Menu","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("188","11","➡ Email Settings Update","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("189","11","Payment Method Settings Menu","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("190","11","➡ Payment Method Update","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("191","11","Role Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("192","11","➡ Role Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("193","11","➡ Role Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("194","11","➡ Role Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("195","11","➡ Role Permission Assign","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("196","11","Base Group Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("197","11","➡ Base Group Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("198","11","➡ Base Group Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("199","11","➡ Base Group Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("200","11","Base Setup Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("201","11","➡ Base Setup Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("202","11","➡ Base Setup Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("203","11","➡ Base Setup Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("204","11","Holiday Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("205","11","➡ Holiday Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("206","11","➡ Holiday Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("207","11","➡ Holiday Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("208","11","SMS Setting Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("209","11","➡ SMS Setting Update","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("210","11","Weekend Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("211","11","➡ Weekend Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("212","11","➡ Weekend Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("213","11","➡ Weekend Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("214","11","Language Settings Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("215","11","➡ Language Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("216","11","➡ Language Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("217","11","➡ Language Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("218","11","➡ Language Setup","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("219","11","Backup Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("220","11","➡ Backup upload","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("221","11","➡ Backup image","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("222","11","➡ Backup Project","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("223","11","➡ Backup Database","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("224","1","➡ Pending Leave Application","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("225","1","➡ Number of Vendor","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("226","1","➡ Number of customer","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("227","1","➡ Number of stocks","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("228","3","Daily Expense Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("229","3","➡ Daily Expense Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("230","3","➡ Daily Expense Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("231","3","➡ Daily Expense Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("232","3","➡ Approve expense status","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("233","3","Details Report Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("234","3","➡ Details Report Search","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("235","3","Ledger Report menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("236","3","➡ Ledger Report Search","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("237","3","Accounts Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("238","3","➡ Accounts Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("239","3","➡ Accounts Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("240","3","➡ Accounts Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("241","3","Bank Legder Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("242","3","➡ Bank Ledger Search","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("243","3","Transfer Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("244","3","➡ Transfer Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("245","3","➡ Transfer Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("246","3","➡ Transfer Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("247","3","Investment Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("248","3","➡ Investment Search","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("249","3","➡ Investment Add New Button","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("250","3","➡ Investment Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("251","3","➡ Investment Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("252","3","➡ Investment Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("253","12","Invoice Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("254","12","Invoice Create","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("255","12","Invoice List","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("256","12","➡ Invoice View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("257","12","➡ Invoice Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("258","12","➡ Invoice Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("259","12","➡ Invoice Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("260","12","Invoice Setting","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("261","12","➡ Invoice Setting Update","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("262","13","Advance/loan Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("263","13","Add Loan Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("264","13","➡ Loan Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("265","13","➡ Loan Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("266","13","➡ Loan Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("267","13","Total Loan Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("268","13","Total Loan View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("269","8","Enlisted Supplier Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("270","8","➡ Enlisted Supplier Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("271","8","➡ Enlisted Supplier Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("272","8","➡ Enlisted Supplier Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("273","10","Cost Center","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("274","10","Income Statement","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("275","10","Sales Report","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("276","14","Ticket Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("277","14","Category Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("278","14","➡ Category Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("279","14","➡ Category Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("280","14","➡ Category Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("281","14","Priority Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("282","14","➡ Priority Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("283","14","➡ Priority Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("284","14","➡ Priority Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("285","14","Ticket List Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("286","14","➡ Ticket List Search","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("287","14","➡ Ticket Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("288","14","➡ Ticket View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("289","14","➡ Ticket Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("290","14","➡ Ticket Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("291","5","Cash Issue Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("292","5","➡ Cash Return Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("293","1","➡ Account Widget","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("294","1","➡ Tender Widget","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("295","10","Bank Book","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("296","10","Purchase Report","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("297","4","Expired Tenders Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("298","4","➡ Expired Tenders Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("299","4","➡ Expired Tenders Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("300","4","Tenders Won","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("301","4","Orders Shipped Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("302","4","➡ View Status","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("303","4","➡ View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("304","4","➡ Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("305","4","➡ Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("306","4","Delivered Orders Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("307","4","➡ View Status","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("308","4","➡ View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("309","4","➡ Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("310","4","➡ Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("311","4","Inspected Orders Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("312","4","➡ View Status","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("313","4","➡ View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("314","4","➡ Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("315","4","➡ Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("316","4","Completed Orders Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("317","4","➡ View Status","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("318","4","➡ View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("319","4","➡ Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("320","4","➡ Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("321","4","Inspecting Department Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("322","4","➡ Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("323","4","➡ Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("324","4","➡ Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("325","1","➡ bank account balance","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("326","1","➡ Yearly Won Tenders","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("327","1","➡ Yearly Work Orders","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("328","4","➡ Upcoming tenders competitors","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("329","4","➡ Upcoming tenders add result","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("330","4","➡ Work orders view","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("331","4","➡ Work orders view status","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("332","5","➡ Staff Directory view","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("333","5","➡ Payslip Print","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("334","5","➡ Payslip Send Mail","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("335","5","➡ Payroll Report Print","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("336","6","➡ Apply Leave view","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("337","6","➡ Apply Leave My remain leaves","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("338","9","Product Sub Category View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("339","9","➡ Product Sub Category Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("340","9","➡ Product Sub Category Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("341","9","➡ Product Sub Category Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("342","11","Background Settings Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("343","11","➡ Background Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("344","11","➡ Background Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("345","11","➡ Background Make Default","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("346","11","➡ Language Make Default","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("347","11","➡ Backup Database Download","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("348","11","➡ Backup Database Restore","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("349","11","➡ Backup Database Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("350","11","➡ Backup Database Delete","0","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("351","11","User Activities","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("352","11","➡ Activities details View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("353","1","➡ Number Of Work Order","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("354","15","Quotations Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("355","15","Quotations list Menu","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("356","15","➡ Quotation Add","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("357","15","➡ Quotation Edit","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("358","15","➡ Quotation Delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("359","15","➡ Quotation View","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("360","4","➡ Tenders Won competitors","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");
INSERT INTO sm_module_links VALUES("361","4","➡ Tenders Won delete","1","1","1","1","2019-07-25 02:21:21","2019-07-25 04:24:22");



DROP TABLE sm_modules;

CREATE TABLE `sm_modules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `order` int(11) NOT NULL,
  `created_by` int(11) DEFAULT '1',
  `updated_by` int(11) DEFAULT '1',
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_modules VALUES("1","Dashboard","1","0","1","1","1","","");
INSERT INTO sm_modules VALUES("2","Lead Generation","1","1","1","1","1","","");
INSERT INTO sm_modules VALUES("3","Accounts","1","2","1","1","1","","");
INSERT INTO sm_modules VALUES("4","Tender","1","3","1","1","1","","");
INSERT INTO sm_modules VALUES("5","Human Resource","1","4","1","1","1","","");
INSERT INTO sm_modules VALUES("6","Leave Application","1","5","1","1","1","","");
INSERT INTO sm_modules VALUES("7","Communicate","1","6","1","1","1","","");
INSERT INTO sm_modules VALUES("8","User Info","1","7","1","1","1","","");
INSERT INTO sm_modules VALUES("9","Inventory","1","8","1","1","1","","");
INSERT INTO sm_modules VALUES("10","Reports","1","9","1","1","1","","");
INSERT INTO sm_modules VALUES("11","System Settings","1","10","1","1","1","","");
INSERT INTO sm_modules VALUES("12","Invoice","1","11","1","1","1","","");
INSERT INTO sm_modules VALUES("13","Advance/Loan","1","12","1","1","1","","");
INSERT INTO sm_modules VALUES("14","Ticket","1","13","1","1","1","","");
INSERT INTO sm_modules VALUES("15","Quotations","1","14","1","1","1","","");



DROP TABLE sm_news;

CREATE TABLE `sm_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `view_count` int(11) DEFAULT NULL,
  `active_status` int(11) DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_thumb` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `news_body` text COLLATE utf8mb4_unicode_ci,
  `publish_date` date DEFAULT NULL,
  `order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_news VALUES("1","Aut qui quia ipsum aut adipisci error.","9","1","public/uploads/news/news1.jpg","","Et earum maiores est accusamus. Est quia quia et exercitationem dolorem praesentium. Aliquam tenetur aut debitis dolorem dolores rerum repellat. Magni similique quam in ut quod error natus. Aut laboriosam eius consequatur. Quidem est nihil totam hic minus aperiam. Sunt minima velit est eos doloribus. Est neque illo eum rem officia et deserunt. Veniam ratione ut exercitationem. Porro sunt et harum veritatis dolorem rem.","2019-06-02","1","1","","","1","","");
INSERT INTO sm_news VALUES("2","Optio quis aut totam sequi et eligendi.","5","1","public/uploads/news/news2.jpg","","Nihil et ut reiciendis adipisci rem sed fugiat. Quis a reiciendis qui totam fuga ut vitae modi. Rem sit dolore qui. Mollitia aut architecto ea consequatur voluptates provident praesentium fuga. Beatae et officia et. Est enim deleniti iure. Totam et quia et repudiandae ratione et aspernatur. Qui impedit iure adipisci unde. Enim excepturi cumque excepturi praesentium doloremque ea. Voluptatem recusandae nobis repellendus repellendus error et. Aut ut totam culpa fugiat et est reiciendis fugit.","2019-06-02","2","1","","","1","","");
INSERT INTO sm_news VALUES("3","Et libero ex rerum est et quidem.","1","1","public/uploads/news/news3.jpg","","Ea debitis eius placeat ut ut eius nulla. Quasi nihil quo ut non reprehenderit aut. Aliquid sint sed et quia odit ullam. Impedit sint qui deleniti iusto adipisci molestias. Corrupti eos asperiores consequatur tempore et vero. Quia incidunt sed est aliquam et. Dignissimos molestias deserunt numquam. Ut dolor quo dolor ut voluptatum non. Facilis ducimus deserunt et necessitatibus nobis possimus deleniti. Accusamus rerum nesciunt veritatis. Ex quia quia at sed non nemo eaque.","2019-06-02","3","1","","","1","","");
INSERT INTO sm_news VALUES("4","Placeat rerum repellat in ut.","3","1","public/uploads/news/news4.jpg","","Illo nam nemo maxime repudiandae necessitatibus. Dicta quo doloremque vel eligendi magni tenetur ratione. Nesciunt in praesentium in. Libero non debitis doloremque occaecati tempore aut esse. Facere quisquam culpa corporis est placeat molestiae dicta. Asperiores placeat ipsum sint amet. Est ut placeat pariatur nostrum nulla. Et voluptates ipsum sit ipsum. Fugiat ullam vel quia eos error.","2019-06-02","4","1","","","1","","");
INSERT INTO sm_news VALUES("5","Aut et dolor temporibus aliquid.","9","1","public/uploads/news/news5.jpg","","Est eius vel ex debitis harum optio laudantium voluptate. Aperiam quibusdam deleniti quos et. Quaerat voluptate rerum et ut illum excepturi. Ad et magni est cupiditate id. Velit ipsum maiores sint quaerat et explicabo nemo. Iure sapiente dolor quis ipsam molestias. Sequi aut error id alias aut. Nam vitae vitae nihil sint ea autem expedita quos. Assumenda quaerat accusantium dolores. Sint facere consequatur aut illo autem. Id ut blanditiis et incidunt maxime fugit neque.","2019-06-02","5","2","","","1","","");
INSERT INTO sm_news VALUES("6","Dolorem provident ad doloribus sint.","6","1","public/uploads/news/news6.jpg","","Dolorum hic aut corporis placeat. Quo in sunt quibusdam perspiciatis occaecati. Odio modi rerum vitae tempore. Quae eius aut doloribus sit. Eos nobis officiis quam ut. Eaque rerum minus consectetur consequatur a. Nam voluptas ipsum ut sit quae. Corrupti dolorem esse consequatur.","2019-06-02","6","2","","","1","","");
INSERT INTO sm_news VALUES("7","Neque et officia placeat dolorem.","6","1","public/uploads/news/news7.jpg","","Aut eaque velit neque quia. Iusto suscipit voluptas aut id in. Est nesciunt facilis illo quod ut adipisci. Consequatur culpa expedita cum culpa omnis in voluptas. Expedita laudantium architecto unde. Eveniet eligendi ea doloribus dignissimos distinctio et architecto. Quas dolorem impedit ut ipsa corrupti.","2019-06-02","7","2","","","1","","");
INSERT INTO sm_news VALUES("8","Ad nihil debitis sit et fugit quia.","8","1","public/uploads/news/news8.jpg","","In architecto incidunt dicta fuga debitis quia perferendis eum. Consequatur illum non ex occaecati qui qui pariatur. Vero libero voluptatibus labore excepturi suscipit quod omnis. Placeat optio ut dolore. Exercitationem architecto ducimus hic excepturi. Quae officiis minus hic ratione. Aut veniam omnis iure neque nulla. Et quae in vel nulla libero. Quisquam eaque qui sed et.","2019-06-02","8","2","","","1","","");
INSERT INTO sm_news VALUES("9","Aut suscipit distinctio sequi.","4","1","public/uploads/news/news9.jpg","","Excepturi dolores ab libero nesciunt tempore et. Praesentium dolorum autem nihil aspernatur nesciunt cum. Voluptas et cum deserunt optio nulla voluptates quia sapiente. Earum asperiores a et repudiandae. Nostrum facere quo et. Dolorem quis aut voluptatibus. Odio numquam officia aut facilis soluta expedita ut error. Eos et nihil sunt facilis at provident. At amet vero ex cum illo aut. Dolores in ut voluptas inventore. Ratione enim quia a impedit repellendus non debitis.","2019-06-02","9","3","","","1","","");
INSERT INTO sm_news VALUES("10","Ut nulla fuga magni ad ut.","9","1","public/uploads/news/news10.jpg","","Error quo vel officia molestiae fugiat accusamus eos. Perferendis voluptas aut eos id facilis nesciunt et natus. Aut quis aliquam et et autem qui est. Incidunt at aut quas placeat. Est laboriosam quia delectus est eum expedita. Nemo earum ex fugiat. Facere ipsam aut nostrum doloremque qui asperiores dolorem. Iste quod facilis dolores quod delectus. Sequi labore qui aliquid non tempore laudantium aut. Ab tempore earum suscipit maiores. Velit earum reprehenderit facilis dolore id similique.","2019-06-02","10","3","","","1","","");
INSERT INTO sm_news VALUES("11","Cupiditate ad ullam qui modi porro sed.","9","1","public/uploads/news/news11.jpg","","Voluptatem quia veniam excepturi tempora iure. Suscipit nobis officiis in earum eligendi aut. Nemo non optio voluptatem vel. Distinctio consectetur fugiat inventore. A quam in sit animi doloribus ullam. Eum cupiditate modi dolor est sint dolore. Alias at vel voluptas culpa iusto. Dolorum maxime nobis aut nobis. Eius dolor architecto in perspiciatis. Sed dignissimos odio quod veniam harum autem officia itaque. A velit voluptatem rerum ut tempore.","2019-06-02","11","3","","","1","","");
INSERT INTO sm_news VALUES("12","In harum voluptatum delectus corporis.","9","1","public/uploads/news/news12.jpg","","Ratione consequatur repellat reprehenderit vitae et culpa et. Cupiditate iusto tempore quae. Mollitia dolorem vel eos dolor tempore nemo officiis. Fugit iste necessitatibus autem veritatis numquam saepe accusamus repellat. Doloribus explicabo voluptas possimus consequatur voluptas ea quo et. Eos consequatur porro sint delectus eos repellendus quis. Earum nisi ab incidunt iure. Esse exercitationem et ipsum odit.","2019-06-02","12","3","","","1","","");



DROP TABLE sm_news_categories;

CREATE TABLE `sm_news_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_news_categories VALUES("1","International","1","","");
INSERT INTO sm_news_categories VALUES("2","Our history","1","","");
INSERT INTO sm_news_categories VALUES("3","Our mission and vision","1","","");
INSERT INTO sm_news_categories VALUES("4","National","1","","");
INSERT INTO sm_news_categories VALUES("5","Sports","1","","");



DROP TABLE sm_notice_boards;

CREATE TABLE `sm_notice_boards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `notice_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notice_message` text COLLATE utf8mb4_unicode_ci,
  `notice_date` date DEFAULT NULL,
  `publish_on` date DEFAULT NULL,
  `inform_to` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Notice message sent to these roles',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_notice_boards VALUES("1","Supply of Disc Insulator with Fittings for 11 KV Line.","Supply of Disc Insulator with Fittings for 11 KV Line.","2019-06-11","2019-06-12","1,3,4,5","1","","","1","","");
INSERT INTO sm_notice_boards VALUES("2","Notice For the Issuance of Bid Documents For Selection of the Project","Notice For the Issuance of Bid Documents For Selection of the Project Sponsors For Implementation of a 5 MW+-20% Grid Connected Waste to Power Project on Build, Own and Operate (BOO) Basis.","2019-06-10","2019-06-11","1,3,4,5","1","","","1","","");
INSERT INTO sm_notice_boards VALUES("3","Purchase of Grass Cutter Machine Backpack, Rechargeable Plant Trimmer (with Lithium Battery) and Electric Grass Cutter Machine.","Purchase of Grass Cutter Machine Backpack, Rechargeable Plant Trimmer (with Lithium Battery) and Electric Grass Cutter Machine.","2019-06-10","2019-06-11","1,3,4,5","1","","","1","","");
INSERT INTO sm_notice_boards VALUES("4","Supply and installation of farm machinery and similar equipment under DPP","Supply and installation of farm machinery and similar equipment under DPP line item of Procurement of Scientific equipment and farm machineries.","2019-06-10","2019-06-11","1,3,4,5","1","","","1","","");



DROP TABLE sm_notifications;

CREATE TABLE `sm_notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_id` int(11) DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(4) NOT NULL DEFAULT '0',
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_payment_gateway_settings;

CREATE TABLE `sm_payment_gateway_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gateway_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_signature` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_client_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_mode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_secret_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_secret_word` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_publisher_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_private_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_payment_gateway_settings VALUES("1","PayPal","demo@paypal.com","","","AVZdghanegaOjiL6DPXd0XwjMGEQ2aXc58z1-qNwv_Wz9mI_6MKSW5dS9uPAha3rd7eB82ToOCQLp31c","","EMgxBzeJ9By7D0xvkSUblDd_GW99WvK0DDNyvkGn7rBikvjPw46xz9Plozp4jl7AOsx","","","","0","","","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_payment_gateway_settings VALUES("2","Stripe","","","","","","AVZdghanegaOjiL6DPXd0XwjMGEQ2aXc58z1-qNwv_Wz9mI_6MKSW5dS9uPAha3rd7eB82ToOCQLp31c","EMgxBzeJ9By7D0xvkSUblDd_GW99WvK0DDNyvkGn7rBikvjPw46xz9Plozp4jl7AOsx","","","0","","","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_payment_gateway_settings VALUES("3","Paystack","","","","","","sk_live_2679322872013c265e161bc8ea11efc1e822bce1","","pk_live_e5738ce9aade963387204f1f19bee599176e7a71","","0","","","2019-12-15 14:03:00","2019-12-15 14:03:00");



DROP TABLE sm_payment_methhods;

CREATE TABLE `sm_payment_methhods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `gateway_id` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_payment_methhods VALUES("1","Cash","System","1","0","1","1","","");
INSERT INTO sm_payment_methhods VALUES("2","Cheque","System","1","0","1","1","","");
INSERT INTO sm_payment_methhods VALUES("3","Bank","System","1","0","1","1","","");
INSERT INTO sm_payment_methhods VALUES("4","Paypal","System","1","1","1","1","","");
INSERT INTO sm_payment_methhods VALUES("5","Stripe","System","1","2","1","1","","");
INSERT INTO sm_payment_methhods VALUES("6","Paystack","System","1","3","1","1","","");



DROP TABLE sm_phone_call_logs;

CREATE TABLE `sm_phone_call_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `next_follow_up_date` date DEFAULT NULL,
  `call_duration` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_type` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_postal_dispatches;

CREATE TABLE `sm_postal_dispatches` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `to_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_postal_receives;

CREATE TABLE `sm_postal_receives` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_product_partnumbers;

CREATE TABLE `sm_product_partnumbers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_receive_id` int(11) DEFAULT NULL,
  `part_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_part_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_product_purchases;

CREATE TABLE `sm_product_purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `purchase_date` date NOT NULL,
  `expaire_date` date NOT NULL,
  `price` double(8,2) NOT NULL,
  `paid_amount` double(8,2) NOT NULL,
  `due_amount` double(8,2) NOT NULL,
  `package` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_product_purchases VALUES("1","159","31","2019-12-15","2022-12-15","200.00","130.50","69.50","INFIX EDU","1","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_question_bank_mu_options;

CREATE TABLE `sm_question_bank_mu_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_bank_id` tinyint(4) DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '0 = false, 1 = correct',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_question_banks;

CREATE TABLE `sm_question_banks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `q_group_id` tinyint(4) DEFAULT NULL,
  `class_id` tinyint(4) DEFAULT NULL,
  `section_id` tinyint(4) DEFAULT NULL,
  `type` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'M for multi ans, T for trueFalse, F for fill in the blanks',
  `question` text COLLATE utf8mb4_unicode_ci,
  `marks` int(11) DEFAULT NULL,
  `trueFalse` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'F = false, T = true ',
  `suitable_words` text COLLATE utf8mb4_unicode_ci,
  `number_of_option` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_question_groups;

CREATE TABLE `sm_question_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_question_levels;

CREATE TABLE `sm_question_levels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_quotation_products;

CREATE TABLE `sm_quotation_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `product_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qnt` int(11) DEFAULT NULL,
  `unit_price` double(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sm_quotation_products_quotation_id_foreign` (`quotation_id`),
  CONSTRAINT `sm_quotation_products_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `sm_quotations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_quotation_products VALUES("1","1","1","23","3","1280.00","2019-08-27 05:40:59","2019-08-27 05:40:59");
INSERT INTO sm_quotation_products VALUES("2","1","2","4","5","2560.00","2019-08-27 05:40:59","2019-08-27 05:40:59");
INSERT INTO sm_quotation_products VALUES("3","1","3","5","5","3840.00","2019-08-27 05:40:59","2019-08-27 05:40:59");
INSERT INTO sm_quotation_products VALUES("4","11","1","sds","3","1280.00","2019-12-15 16:43:45","2019-12-15 16:43:45");
INSERT INTO sm_quotation_products VALUES("5","12","1","1","1","1280.00","2019-12-17 13:54:23","2019-12-17 13:54:23");
INSERT INTO sm_quotation_products VALUES("6","12","2","qdsas","3","2560.00","2019-12-17 13:54:23","2019-12-17 13:54:23");



DROP TABLE sm_quotations;

CREATE TABLE `sm_quotations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quotation_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_id` int(10) unsigned NOT NULL,
  `vendor_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` double(15,2) NOT NULL,
  `discount_amount` double(15,2) DEFAULT NULL,
  `discount_type` enum('P','A') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'P = percentage, A= amount',
  `tax_amount` double(15,2) DEFAULT NULL,
  `payment_status` enum('UP','P','PP','PR') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'UP= UNPAID , P= PAID , PP= PARTIALLY PAID, PR= PROFORMA',
  `partial_paymemt` double(15,2) DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `private_note` text COLLATE utf8mb4_unicode_ci,
  `public_note` text COLLATE utf8mb4_unicode_ci,
  `terms_note` text COLLATE utf8mb4_unicode_ci,
  `footer_note` text COLLATE utf8mb4_unicode_ci,
  `signature_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 = no, 1= yes',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_quotations VALUES("1","","Title 1","456461","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","45789.67","","","private_note 1","public_note 1","terms_note 1","","signature_person 1","signature_company 1","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("2","","Title 2","456462","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","91579.34","","","private_note 2","public_note 2","terms_note 2","","signature_person 2","signature_company 2","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("3","","Title 3","456463","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","137369.01","","","private_note 3","public_note 3","terms_note 3","","signature_person 3","signature_company 3","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("4","","Title 4","456464","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","183158.68","","","private_note 4","public_note 4","terms_note 4","","signature_person 4","signature_company 4","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("5","","Title 5","456465","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","228948.35","","","private_note 5","public_note 5","terms_note 5","","signature_person 5","signature_company 5","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("6","","Title 6","456466","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","274738.02","","","private_note 6","public_note 6","terms_note 6","","signature_person 6","signature_company 6","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("7","","Title 7","456467","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","320527.69","","","private_note 7","public_note 7","terms_note 7","","signature_person 7","signature_company 7","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("8","","Title 8","456468","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","366317.36","","","private_note 8","public_note 8","terms_note 8","","signature_person 8","signature_company 8","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("9","","Title 9","456469","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","412107.03","","","private_note 9","public_note 9","terms_note 9","","signature_person 9","signature_company 9","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("10","","Title 10","4564610","2019-12-15","","1","Rashed Zaman","1","Google Inc.","5515.89","15.89","P","5.00","PP","457896.70","","","private_note 10","public_note 10","terms_note 10","","signature_person 10","signature_company 10","1","1","1","1","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_quotations VALUES("11","equipment","asd","asd","2019-12-04","asd","5","Darwin Kris","2","kianna.morar","3840.00","0.00","A","","UP","","","","","","","","","","1","1","1","1","2019-12-15 16:43:45","2019-12-15 16:43:45");
INSERT INTO sm_quotations VALUES("12","equipment","sadsad","11576569139","2019-12-17","asdsd","3","Davion Runolfsson","2","kianna.morar","8960.00","0.00","A","","UP","","","","","","","","","","1","1","1","1","2019-12-17 13:54:23","2019-12-17 13:54:23");



DROP TABLE sm_role_permissions;

CREATE TABLE `sm_role_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(4) DEFAULT NULL,
  `module_link_id` int(11) DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_role_permissions VALUES("76","3","1","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("77","3","2","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("78","3","3","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("79","3","4","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("80","3","5","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("81","3","7","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("82","3","8","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("83","3","59","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("84","3","60","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("85","3","61","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("86","3","307","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("87","3","308","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("88","3","309","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("89","3","310","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("90","3","84","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("91","3","85","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("92","3","86","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("93","3","87","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("94","3","88","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("95","3","89","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("96","3","90","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("97","3","91","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("98","3","93","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("99","3","94","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("100","3","97","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("101","3","98","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("102","3","99","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("103","3","100","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("104","3","101","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("105","3","102","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("106","3","104","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("107","3","105","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("108","3","106","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("109","3","107","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("110","3","108","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("111","3","109","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("112","3","110","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("113","3","111","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("114","3","112","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("115","3","113","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("116","3","115","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("117","3","116","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("118","3","117","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("119","3","118","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("120","3","119","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("121","3","120","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("122","3","121","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("123","3","122","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("124","3","123","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("125","3","124","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("126","3","125","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("127","3","126","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("128","3","127","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("129","3","128","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("130","3","139","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("131","3","140","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("132","3","141","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("133","3","142","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("134","3","143","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("135","3","144","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("136","3","145","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("137","3","146","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("138","3","147","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("139","3","338","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("140","3","339","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("141","3","340","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("142","3","178","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("143","3","179","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("144","3","180","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("145","3","191","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("146","3","192","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("147","3","193","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("148","3","194","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("149","3","195","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("150","3","196","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("151","3","197","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("152","3","198","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("153","3","199","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("154","3","200","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("155","3","201","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("156","3","202","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("157","3","203","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("158","3","204","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("159","3","205","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("160","3","206","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("161","3","207","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("162","3","210","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("163","3","211","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("164","3","212","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("165","3","213","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("166","3","214","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("167","3","215","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("168","3","216","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("169","3","217","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("170","3","218","1","","","1","2019-12-17 14:02:19","2019-12-17 14:02:19");
INSERT INTO sm_role_permissions VALUES("171","4","1","1","","","1","2019-12-17 14:02:53","2019-12-17 14:02:53");
INSERT INTO sm_role_permissions VALUES("172","4","2","1","","","1","2019-12-17 14:02:53","2019-12-17 14:02:53");
INSERT INTO sm_role_permissions VALUES("173","4","3","1","","","1","2019-12-17 14:02:53","2019-12-17 14:02:53");



DROP TABLE sm_room_lists;

CREATE TABLE `sm_room_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dormitory_id` tinyint(4) NOT NULL,
  `room_type_id` tinyint(4) NOT NULL,
  `number_of_bed` tinyint(4) NOT NULL,
  `cost_per_bed` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_room_lists VALUES("1","1001","1","1","1","400","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("2","1001","1","2","1","400","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("3","1001","1","3","1","400","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("4","1001","1","4","1","400","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("5","2002","2","1","2","800","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("6","2002","2","2","2","800","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("7","2002","2","3","2","800","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("8","2002","2","4","2","800","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("9","3003","3","1","3","1200","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("10","3003","3","2","3","1200","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("11","3003","3","3","3","1200","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("12","3003","3","4","3","1200","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("13","4004","4","1","4","1600","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("14","4004","4","2","4","1600","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("15","4004","4","3","4","1600","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_lists VALUES("16","4004","4","4","4","1600","","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");



DROP TABLE sm_room_types;

CREATE TABLE `sm_room_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_room_types VALUES("1","Single","A room assigned to one person. May have one or more beds.","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_types VALUES("2","Double","A room assigned to two people. May have one or more beds.","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_types VALUES("3","Triple","A room assigned to three people. May have two or more beds","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_types VALUES("4","Quad","A room assigned to four people. May have two or more beds.","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_types VALUES("5","Queen","A room with a queen-sized bed. May be occupied by one or more people","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_room_types VALUES("6","King","A room with a king-sized bed. May be occupied by one or more people.","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");



DROP TABLE sm_routes;

CREATE TABLE `sm_routes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `far` double(8,2) NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_routes VALUES("1","School To Shahabag","100.00","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_routes VALUES("2","School To Malibag","100.00","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_routes VALUES("3","School To Dhanmondhi","100.00","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO sm_routes VALUES("4","School To New Market","100.00","1","1","1","1","2019-12-15 14:02:57","2019-12-15 14:02:57");



DROP TABLE sm_schools;

CREATE TABLE `sm_schools` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` tinyint(4) DEFAULT NULL,
  `updated_by` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_send_messages;

CREATE TABLE `sm_send_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `message_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_des` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notice_date` date DEFAULT NULL,
  `publish_on` date DEFAULT NULL,
  `message_to` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'message sent to these roles',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_sessions;

CREATE TABLE `sm_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_setup_admins;

CREATE TABLE `sm_setup_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL COMMENT '1 purpose, 2 complaint type, 3 source, 4 Reference',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT '1',
  `updated_by` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_setup_admins VALUES("1","3","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("2","3","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("3","3","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("4","1","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("5","1","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("6","1","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("7","1","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("8","1","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("9","1","Lorem Ipsum is simply dummy text ","YLorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("10","1","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("11","2","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("12","2","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("13","2","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("14","2","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("15","2","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("16","2","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("17","2","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("18","4","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("19","4","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");
INSERT INTO sm_setup_admins VALUES("20","4","Lorem Ipsum is simply dummy text ","Lorem Ipsum is simply dummy text ","1","1","1","2019-12-15 14:03:00","2019-12-15 14:03:00");



DROP TABLE sm_sms_gateways;

CREATE TABLE `sm_sms_gateways` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gateway_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clickatell_username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clickatell_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clickatell_api_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twilio_account_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twilio_authentication_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twilio_registered_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `msg91_authentication_key_sid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `msg91_sender_id` int(11) DEFAULT NULL,
  `msg91_route` int(11) DEFAULT NULL,
  `msg91_country_code` int(11) DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_sms_gateways VALUES("1","Clickatell","demo1","122334","123123","","","","","","","","1","","","1","","2019-12-17 14:05:46");
INSERT INTO sm_sms_gateways VALUES("2","Twilio","demo2","12336","","123","123123","123123123123","","","","","0","","","1","","2019-12-17 14:05:53");
INSERT INTO sm_sms_gateways VALUES("3","Msg91","demo3","23445","","","","","","","","","0","","","1","","2019-12-17 14:05:38");



DROP TABLE sm_staff_attendences;

CREATE TABLE `sm_staff_attendences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` int(10) unsigned NOT NULL,
  `attendence_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Present: P Late: L Absent: A Holiday: H Half Day: F',
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendence_date` date DEFAULT NULL,
  `in_time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `out_time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` tinyint(4) DEFAULT NULL,
  `updated_by` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_staff_attendences VALUES("1","1","P","Office In Time From Login at 16:43 PM & Office Out Time From Logout at 18:35 PM","2019-12-15","16:43 PM","18:35 PM","1","1","2019-12-15 16:43:08","2019-12-15 18:35:34");
INSERT INTO sm_staff_attendences VALUES("2","18","P","Office In Time From Login at 18:35 PM & Office Out Time From Logout at 18:42 PM","2019-12-15","18:35 PM","18:42 PM","18","18","2019-12-15 18:35:39","2019-12-15 18:42:48");
INSERT INTO sm_staff_attendences VALUES("3","1","P","Office In Time From Login at 11:26 AM & Office Out Time From Logout at 11:51 AM & Office Out Time From Logout at 12:22 PM","2019-12-17","11:26 AM","12:22 PM","1","1","2019-12-17 11:26:43","2019-12-17 12:22:00");
INSERT INTO sm_staff_attendences VALUES("4","11","P","Office In Time From Login at 12:22 PM & Office Out Time From Logout at 12:22 PM","2019-12-17","12:22 PM","12:22 PM","11","11","2019-12-17 12:22:02","2019-12-17 12:22:13");



DROP TABLE sm_staffs;

CREATE TABLE `sm_staffs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '1',
  `role_id` int(11) DEFAULT '1',
  `staff_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation_id` int(10) unsigned DEFAULT '1',
  `department_id` int(10) unsigned DEFAULT '1',
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fathers_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mothers_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `gender_id` int(10) unsigned DEFAULT '1',
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_mobile` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merital_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `epf_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basic_salary` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_type` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `casual_leave` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_leave` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metarnity_leave` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_brach` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payoneer_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skrill_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wepay_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amazon_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_url` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twiteer_url` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instragram_url` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_letter` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resume` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_document` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 = inactive, 1 = active',
  `delete_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 = yes, 1 = no',
  `driving_license` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driving_license_ex_date` date DEFAULT NULL,
  `created_by` tinyint(4) DEFAULT NULL,
  `updated_by` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_staffs VALUES("1","1","1","1","1","1","","Super","Admin","Super Admin","","","","","1","spn5@spondonit.com","","","","","public/uploads/peoples/1.jpg","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","1","1","","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO sm_staffs VALUES("2","2","2","4","1","1","","Joan","Blick","Joan Blick","Chet","Catharine","","","1","joan_blick@demo.com","","","","","public/uploads/peoples/2.jpg","60638 McClure Corners
Adamsberg, VT 52067","","","","","","","","","","","Joan Blick","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("3","3","2","6","1","1","","Davion","Runolfsson","Davion Runolfsson","Edward","Fay","","","1","davion_runolfsson@demo.com","","","","","public/uploads/peoples/3.jpg","64140 Oberbrunner Fields
East Georgiana, WV 96419-4889","","","","","","","","","","","Davion Runolfsson","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("4","4","2","8","1","1","","Chadrick","Wiegand","Chadrick Wiegand","Ahmed","Katrine","","","1","chadrick_wiegand@demo.com","","","","","public/uploads/peoples/4.jpg","9998 Stroman Walks Apt. 437
Ulisesview, AZ 74232-3394","","","","","","","","","","","Chadrick Wiegand","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("5","5","2","10","1","1","","Darwin","Kris","Darwin Kris","Willis","Breana","","","1","darwin_kris@demo.com","","","","","public/uploads/peoples/5.jpg","525 Chesley Junctions
Kellimouth, MO 02540-5765","","","","","","","","","","","Darwin Kris","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("6","6","2","12","1","1","","Napoleon","Hyatt","Napoleon Hyatt","Rick","Esperanza","","","1","napoleon_hyatt@demo.com","","","","","public/uploads/peoples/6.jpg","2962 Reid Shores
East Lilyanville, DE 99163","","","","","","","","","","","Napoleon Hyatt","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("7","7","2","14","1","1","","Laron","Kris","Laron Kris","Gregorio","Zelda","","","1","laron_kris@demo.com","","","","","public/uploads/peoples/7.jpg","5643 Brown Trail
Port Reece, ME 98258","","","","","","","","","","","Laron Kris","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("8","8","2","16","1","1","","Matteo","Kunde","Matteo Kunde","Alvah","Maryam","","","1","matteo_kunde@demo.com","","","","","public/uploads/peoples/8.jpg","6934 Alysha Mountains
Isaiahfort, ND 40454","","","","","","","","","","","Matteo Kunde","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("9","9","2","18","1","1","","Travon","Ledner","Travon Ledner","Jaron","Lottie","","","1","travon_ledner@demo.com","","","","","public/uploads/peoples/9.jpg","987 Kyler Circles
Lake Rosalinda, NE 23781","","","","","","","","","","","Travon Ledner","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("10","10","2","20","1","1","","Lorenz","Abbott","Lorenz Abbott","Rogelio","Annabelle","","","1","lorenz_abbott@demo.com","","","","","public/uploads/peoples/10.jpg","1016 Frami Courts
North Glennaport, KS 21241","","","","","","","","","","","Lorenz Abbott","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("11","11","3","6","1","1","","Charlie","Labadie","Charlie Labadie","Dagmar","Vena","","","1","charlie_labadie@demo.com","","","","","public/uploads/peoples/2.jpg","449 Stoltenberg Court
South Daisychester, WY 82090-1856","","","","","","","","","","","Charlie Labadie","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("12","12","3","9","1","1","","Jerry","Schulist","Jerry Schulist","Karson","Isabel","","","1","jerry_schulist@demo.com","","","","","public/uploads/peoples/3.jpg","267 King Crest Apt. 739
Hackettland, LA 25985","","","","","","","","","","","Jerry Schulist","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("13","13","3","12","1","1","","Norbert","Wolff","Norbert Wolff","Jarvis","Coralie","","","1","norbert_wolff@demo.com","","","","","public/uploads/peoples/4.jpg","24956 Cole Street
Leonorshire, KS 73550-0886","","","","","","","","","","","Norbert Wolff","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("14","14","3","15","1","1","","Jarrett","Tremblay","Jarrett Tremblay","Laverna","Eleonore","","","1","jarrett_tremblay@demo.com","","","","","public/uploads/peoples/5.jpg","228 Elva Mews
Karsonshire, MN 57993-6103","","","","","","","","","","","Jarrett Tremblay","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("15","15","3","18","1","1","","Kaleb","Ankunding","Kaleb Ankunding","Elias","Maida","","","1","kaleb_ankunding@demo.com","","","","","public/uploads/peoples/6.jpg","29461 Donald Flat
Franeckichester, AR 29277","","","","","","","","","","","Kaleb Ankunding","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("16","16","3","21","1","1","","Davin","Paucek","Davin Paucek","Mohamed","Caterina","","","1","davin_paucek@demo.com","","","","","public/uploads/peoples/7.jpg","949 Dillan Village Suite 280
Jackelinetown, NM 79080-3926","","","","","","","","","","","Davin Paucek","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("17","17","3","24","1","1","","Vern","Pollich","Vern Pollich","Kevin","Janet","","","1","vern_pollich@demo.com","","","","","public/uploads/peoples/8.jpg","5007 Christa Turnpike Suite 436
South Veronaview, MS 19652","","","","","","","","","","","Vern Pollich","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("18","18","3","27","1","1","","Kennith","Heathcote","Kennith Heathcote","Dalton","Flo","","","1","kennith_heathcote@demo.com","","","","","public/uploads/peoples/9.jpg","41628 Adrienne Expressway
Torpmouth, DC 58864","","","","","","","","","","","Kennith Heathcote","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");
INSERT INTO sm_staffs VALUES("19","19","3","30","1","1","","Rodrick","Hayes","Rodrick Hayes","Rhett","Madalyn","","","1","rodrick_hayes@demo.com","","","","","public/uploads/peoples/10.jpg","6490 Dorris Points
Buckside, AZ 40578-6226","","","","","","","","","","","Rodrick Hayes","456456456345789","DBBL","","demo@paypal.com","demo@payoneer.com","demo@skrill.com","","","","","","","","","","","","1","1","","","","","","");



DROP TABLE sm_student_documents;

CREATE TABLE `sm_student_documents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_staff_id` int(11) DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'stu=student,stf=staff',
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT '1',
  `updated_by` int(10) unsigned DEFAULT '1',
  `school_id` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sm_student_documents_created_by_foreign` (`created_by`),
  KEY `sm_student_documents_updated_by_foreign` (`updated_by`),
  KEY `sm_student_documents_school_id_foreign` (`school_id`),
  CONSTRAINT `sm_student_documents_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `sm_student_documents_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `sm_schools` (`id`),
  CONSTRAINT `sm_student_documents_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_student_timelines;

CREATE TABLE `sm_student_timelines` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staff_student_id` int(11) NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'stu=student,stf=staff',
  `visible_to_student` int(11) NOT NULL DEFAULT '0' COMMENT '0 = no, 1 = yes',
  `active_status` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT '1',
  `updated_by` int(10) unsigned DEFAULT '1',
  `school_id` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `sm_student_timelines_created_by_foreign` (`created_by`),
  KEY `sm_student_timelines_updated_by_foreign` (`updated_by`),
  KEY `sm_student_timelines_school_id_foreign` (`school_id`),
  CONSTRAINT `sm_student_timelines_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `sm_student_timelines_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `sm_schools` (`id`),
  CONSTRAINT `sm_student_timelines_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_sub_accounts;

CREATE TABLE `sm_sub_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `head_id` int(11) NOT NULL,
  `sub_head` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` int(11) NOT NULL DEFAULT '1',
  `is_approved` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_suppliers;

CREATE TABLE `sm_suppliers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cotact_person_address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_quantity` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_suppliers VALUES("1","eterry","wolff.antonietta","khermann","0197823649234","josephine84@hotmail.com","","","yokuneva","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("2","kianna.morar","jaleel.ratke","raltenwerth","0197823649234","ullrich.hector@gmail.com","","","tyrique.goodwin","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("3","harvey.lilliana","erik78","legros.ruby","0197823649234","hertha.abshire@romaguera.com","","","greenholt.jesus","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("4","qfranecki","camryn52","rod.watsica","0197823649234","price.isabella@yahoo.com","","","greta41","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("5","lbeier","mack.rogahn","hunter67","0197823649234","hodkiewicz.alvina@gmail.com","","","hgerhold","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("6","buddy.emmerich","asa.kuhic","jamar.bruen","0197823649234","block.kayleigh@pouros.com","","","okuneva.van","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("7","kyla65","vrice","vandervort.hipolito","0197823649234","thurman.ziemann@hotmail.com","","","ziemann.river","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("8","kautzer.jeffry","gardner.wiegand","florence32","0197823649234","will34@yahoo.com","","","shanahan.araceli","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("9","huels.chaya","cordie54","eleonore.welch","0197823649234","jbogan@morar.com","","","ispencer","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("10","okeefe.jordi","madelyn.gerhold","yrunte","0197823649234","aliya.grady@leannon.net","","","hane.koby","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("11","schmidt.ebba","harvey01","collins.aimee","0197823649234","omoore@hotmail.com","","","rschamberger","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("12","corene85","aimee.legros","pkovacek","0197823649234","mathew.kshlerin@beer.com","","","parisian.stuart","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("13","ogorczany","beryl37","johnathan70","0197823649234","leonel.schmeler@walker.net","","","corn","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("14","buster.feil","gmcglynn","shegmann","0197823649234","alessandra.orn@ruecker.com","","","hayes.adela","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("15","marks.damien","kertzmann.ilene","kayli67","0197823649234","grimes.walker@hotmail.com","","","heidenreich.ulices","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("16","elisa02","leannon.june","elinore.crist","0197823649234","camren40@gmail.com","","","hhowe","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("17","zulauf.elza","bergstrom.jalon","nona.pouros","0197823649234","tia.cassin@gmail.com","","","gking","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("18","kling.earline","jordyn.king","jtowne","0197823649234","ettie31@damore.biz","","","korey67","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("19","lois.bahringer","muhammad.cremin","rbarton","0197823649234","ryost@wuckert.com","","","phoeger","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO sm_suppliers VALUES("20","may59","jackeline.effertz","lilian72","0197823649234","don.bruen@lockman.com","","","mcglynn.carmine","1","1","1","2019-12-15 14:02:59","2019-12-15 14:02:59");



DROP TABLE sm_tender_products;

CREATE TABLE `sm_tender_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tender_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `product_model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qnt` int(11) DEFAULT NULL,
  `unit_price` double(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sm_tender_products_tender_id_foreign` (`tender_id`),
  KEY `sm_tender_products_product_id_foreign` (`product_id`),
  CONSTRAINT `sm_tender_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `sm_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sm_tender_products_tender_id_foreign` FOREIGN KEY (`tender_id`) REFERENCES `sm_tenders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_tender_products VALUES("1","1","1","23","3","1280.00","2019-08-27 05:40:59","2019-08-27 05:40:59");
INSERT INTO sm_tender_products VALUES("2","1","2","4","5","2560.00","2019-08-27 05:40:59","2019-08-27 05:40:59");
INSERT INTO sm_tender_products VALUES("3","1","3","5","5","3840.00","2019-08-27 05:40:59","2019-08-27 05:40:59");



DROP TABLE sm_tender_statuses;

CREATE TABLE `sm_tender_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = no, 1= yes',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_tender_statuses VALUES("1","Running","0","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_tender_statuses VALUES("2","Shipment","0","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_tender_statuses VALUES("3","Delivered","0","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_tender_statuses VALUES("4","Inspection Complete","0","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_tender_statuses VALUES("5","Completed","0","1","1","1","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_tenders;

CREATE TABLE `sm_tenders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `work_order_mode` enum('equipment','spareparts') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tender_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tender_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_order_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `letter_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_order_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `open_date` date DEFAULT NULL,
  `customer_id` tinyint(4) DEFAULT NULL,
  `vendor_id` tinyint(4) DEFAULT NULL,
  `department_id` tinyint(4) DEFAULT NULL,
  `bid_amount` double(10,2) DEFAULT NULL,
  `discount_amount` double(10,2) DEFAULT NULL,
  `discount_type` enum('P','A') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'P = percentage, A= amount',
  `description` text COLLATE utf8mb4_unicode_ci,
  `note` text COLLATE utf8mb4_unicode_ci,
  `end_user_name` text COLLATE utf8mb4_unicode_ci,
  `shipment_work_order_date` date DEFAULT NULL,
  `shipping_mode` enum('AIR','SEA','LAND') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_carrier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_delivery_date` date DEFAULT NULL,
  `status_cr` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_destination` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_completion_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `cheque_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` double(15,2) DEFAULT NULL,
  `file1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = no, 1= yes',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `stage_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = running, 1= shipment, 2= delivered, 3= inspection, 4=completed',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_tenders VALUES("1","equipment","40 HP OUT BOARD ENGINE (MERCURY,EVINRUDE,YAMAHA)","23.02.2608.212.53.364.19-20.0102","345","345","2019-08-27","2019-08-27","2019-08-27","2","","1","35840.00","0.00","A","","The Tender Title has an alignment problem. Inspecting Department, End User Name is missing from edit mode of a work order. Edit mode is still showing the Signature field.","Here the vendor","","","","","","","","","","","","","","","","0","1","0","1","","2019-08-27 05:40:59","2019-08-27 05:40:59");



DROP TABLE sm_time_zones;

CREATE TABLE `sm_time_zones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_zone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=425 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_time_zones VALUES("1","AD","Europe/Andorra","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("2","AE","Asia/Dubai","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("3","AF","Asia/Kabul","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("4","AG","America/Antigua","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("5","AI","America/Anguilla","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("6","AL","Europe/Tirane","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("7","AM","Asia/Yerevan","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("8","AO","Africa/Luanda","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("9","AQ","Antarctica/McMurdo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("10","AQ","Antarctica/Casey","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("11","AQ","Antarctica/Davis","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("12","AQ","Antarctica/DumontDUrville","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("13","AQ","Antarctica/Mawson","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("14","AQ","Antarctica/Palmer","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("15","AQ","Antarctica/Rothera","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("16","AQ","Antarctica/Syowa","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("17","AQ","Antarctica/Troll","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("18","AQ","Antarctica/Vostok","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("19","AR","America/Argentina/Buenos_Aires","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("20","AR","America/Argentina/Cordoba","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("21","AR","America/Argentina/Salta","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("22","AR","America/Argentina/Jujuy","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("23","AR","America/Argentina/Tucuman","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("24","AR","America/Argentina/Catamarca","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("25","AR","America/Argentina/La_Rioja","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("26","AR","America/Argentina/San_Juan","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("27","AR","America/Argentina/Mendoza","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("28","AR","America/Argentina/San_Luis","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("29","AR","America/Argentina/Rio_Gallegos","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("30","AR","America/Argentina/Ushuaia","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("31","AS","Pacific/Pago_Pago","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("32","AT","Europe/Vienna","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("33","AU","Australia/Lord_Howe","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("34","AU","Antarctica/Macquarie","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("35","AU","Australia/Hobart","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("36","AU","Australia/Currie","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("37","AU","Australia/Melbourne","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("38","AU","Australia/Sydney","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("39","AU","Australia/Broken_Hill","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("40","AU","Australia/Brisbane","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("41","AU","Australia/Lindeman","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("42","AU","Australia/Adelaide","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("43","AU","Australia/Darwin","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("44","AU","Australia/Perth","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("45","AU","Australia/Eucla","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("46","AW","America/Aruba","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("47","AX","Europe/Mariehamn","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("48","AZ","Asia/Baku","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("49","BA","Europe/Sarajevo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("50","BB","America/Barbados","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("51","BD","Asia/Dhaka","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("52","BE","Europe/Brussels","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("53","BF","Africa/Ouagadougou","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("54","BG","Europe/Sofia","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("55","BH","Asia/Bahrain","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("56","BI","Africa/Bujumbura","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("57","BJ","Africa/Porto-Novo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("58","BL","America/St_Barthelemy","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("59","BM","Atlantic/Bermuda","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("60","BN","Asia/Brunei","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("61","BO","America/La_Paz","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("62","BQ","America/Kralendijk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("63","BR","America/Noronha","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("64","BR","America/Belem","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("65","BR","America/Fortaleza","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("66","BR","America/Recife","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("67","BR","America/Araguaina","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("68","BR","America/Maceio","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("69","BR","America/Bahia","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("70","BR","America/Sao_Paulo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("71","BR","America/Campo_Grande","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("72","BR","America/Cuiaba","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("73","BR","America/Santarem","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("74","BR","America/Porto_Velho","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("75","BR","America/Boa_Vista","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("76","BR","America/Manaus","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("77","BR","America/Eirunepe","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("78","BR","America/Rio_Branco","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("79","BS","America/Nassau","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("80","BT","Asia/Thimphu","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("81","BW","Africa/Gaborone","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("82","BY","Europe/Minsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("83","BZ","America/Belize","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("84","CA","America/St_Johns","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("85","CA","America/Halifax","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("86","CA","America/Glace_Bay","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("87","CA","America/Moncton","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("88","CA","America/Goose_Bay","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("89","CA","America/Blanc-Sablon","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("90","CA","America/Toronto","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("91","CA","America/Nipigon","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("92","CA","America/Thunder_Bay","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("93","CA","America/Iqaluit","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("94","CA","America/Pangnirtung","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("95","CA","America/Atikokan","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("96","CA","America/Winnipeg","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("97","CA","America/Rainy_River","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("98","CA","America/Resolute","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("99","CA","America/Rankin_Inlet","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("100","CA","America/Regina","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("101","CA","America/Swift_Current","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("102","CA","America/Edmonton","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("103","CA","America/Cambridge_Bay","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("104","CA","America/Yellowknife","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("105","CA","America/Inuvik","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("106","CA","America/Creston","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("107","CA","America/Dawson_Creek","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("108","CA","America/Fort_Nelson","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("109","CA","America/Vancouver","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("110","CA","America/Whitehorse","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("111","CA","America/Dawson","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("112","CC","Indian/Cocos","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("113","CD","Africa/Kinshasa","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("114","CD","Africa/Lubumbashi","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("115","CF","Africa/Bangui","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("116","CG","Africa/Brazzaville","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("117","CH","Europe/Zurich","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("118","CI","Africa/Abidjan","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("119","CK","Pacific/Rarotonga","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("120","CL","America/Santiago","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("121","CL","America/Punta_Arenas","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("122","CL","Pacific/Easter","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("123","CM","Africa/Douala","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("124","CN","Asia/Shanghai","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("125","CN","Asia/Urumqi","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("126","CO","America/Bogota","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("127","CR","America/Costa_Rica","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("128","CU","America/Havana","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("129","CV","Atlantic/Cape_Verde","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("130","CW","America/Curacao","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("131","CX","Indian/Christmas","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("132","CY","Asia/Nicosia","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("133","CY","Asia/Famagusta","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("134","CZ","Europe/Prague","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("135","DE","Europe/Berlin","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("136","DE","Europe/Busingen","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("137","DJ","Africa/Djibouti","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("138","DK","Europe/Copenhagen","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("139","DM","America/Dominica","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("140","DO","America/Santo_Domingo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("141","DZ","Africa/Algiers","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("142","EC","America/Guayaquil","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("143","EC","Pacific/Galapagos","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("144","EE","Europe/Tallinn","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("145","EG","Africa/Cairo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("146","EH","Africa/El_Aaiun","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("147","ER","Africa/Asmara","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("148","ES","Europe/Madrid","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("149","ES","Africa/Ceuta","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("150","ES","Atlantic/Canary","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("151","ET","Africa/Addis_Ababa","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("152","FI","Europe/Helsinki","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("153","FJ","Pacific/Fiji","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("154","FK","Atlantic/Stanley","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("155","FM","Pacific/Chuuk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("156","FM","Pacific/Pohnpei","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("157","FM","Pacific/Kosrae","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("158","FO","Atlantic/Faroe","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("159","FR","Europe/Paris","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("160","GA","Africa/Libreville","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("161","GB","Europe/London","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("162","GD","America/Grenada","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("163","GE","Asia/Tbilisi","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("164","GF","America/Cayenne","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("165","GG","Europe/Guernsey","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("166","GH","Africa/Accra","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("167","GI","Europe/Gibraltar","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("168","GL","America/Godthab","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("169","GL","America/Danmarkshavn","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("170","GL","America/Scoresbysund","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("171","GL","America/Thule","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("172","GM","Africa/Banjul","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("173","GN","Africa/Conakry","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("174","GP","America/Guadeloupe","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("175","GQ","Africa/Malabo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("176","GR","Europe/Athens","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("177","GS","Atlantic/South_Georgia","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("178","GT","America/Guatemala","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("179","GU","Pacific/Guam","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("180","GW","Africa/Bissau","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("181","GY","America/Guyana","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("182","HK","Asia/Hong_Kong","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("183","HN","America/Tegucigalpa","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("184","HR","Europe/Zagreb","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("185","HT","America/Port-au-Prince","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("186","HU","Europe/Budapest","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("187","ID","Asia/Jakarta","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("188","ID","Asia/Pontianak","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("189","ID","Asia/Makassar","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("190","ID","Asia/Jayapura","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("191","IE","Europe/Dublin","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("192","IL","Asia/Jerusalem","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("193","IM","Europe/Isle_of_Man","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("194","IN","Asia/Kolkata","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("195","IO","Indian/Chagos","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("196","IQ","Asia/Baghdad","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("197","IR","Asia/Tehran","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("198","IS","Atlantic/Reykjavik","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("199","IT","Europe/Rome","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("200","JE","Europe/Jersey","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("201","JM","America/Jamaica","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("202","JO","Asia/Amman","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("203","JP","Asia/Tokyo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("204","KE","Africa/Nairobi","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("205","KG","Asia/Bishkek","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("206","KH","Asia/Phnom_Penh","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("207","KI","Pacific/Tarawa","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("208","KI","Pacific/Enderbury","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("209","KI","Pacific/Kiritimati","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("210","KM","Indian/Comoro","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("211","KN","America/St_Kitts","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("212","KP","Asia/Pyongyang","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("213","KR","Asia/Seoul","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("214","KW","Asia/Kuwait","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("215","KY","America/Cayman","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("216","KZ","Asia/Almaty","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("217","KZ","Asia/Qyzylorda","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("218","KZ","Asia/Aqtobe","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("219","KZ","Asia/Aqtau","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("220","KZ","Asia/Atyrau","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("221","KZ","Asia/Oral","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("222","LA","Asia/Vientiane","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("223","LB","Asia/Beirut","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("224","LC","America/St_Lucia","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("225","LI","Europe/Vaduz","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("226","LK","Asia/Colombo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("227","LR","Africa/Monrovia","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("228","LS","Africa/Maseru","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("229","LT","Europe/Vilnius","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("230","LU","Europe/Luxembourg","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("231","LV","Europe/Riga","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("232","LY","Africa/Tripoli","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("233","MA","Africa/Casablanca","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("234","MC","Europe/Monaco","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("235","MD","Europe/Chisinau","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("236","ME","Europe/Podgorica","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("237","MF","America/Marigot","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("238","MG","Indian/Antananarivo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("239","MH","Pacific/Majuro","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("240","MH","Pacific/Kwajalein","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("241","MK","Europe/Skopje","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("242","ML","Africa/Bamako","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("243","MM","Asia/Yangon","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("244","MN","Asia/Ulaanbaatar","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("245","MN","Asia/Hovd","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("246","MN","Asia/Choibalsan","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("247","MO","Asia/Macau","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("248","MP","Pacific/Saipan","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("249","MQ","America/Martinique","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("250","MR","Africa/Nouakchott","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("251","MS","America/Montserrat","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("252","MT","Europe/Malta","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("253","MU","Indian/Mauritius","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("254","MV","Indian/Maldives","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("255","MW","Africa/Blantyre","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("256","MX","America/Mexico_City","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("257","MX","America/Cancun","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("258","MX","America/Merida","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("259","MX","America/Monterrey","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("260","MX","America/Matamoros","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("261","MX","America/Mazatlan","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("262","MX","America/Chihuahua","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("263","MX","America/Ojinaga","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("264","MX","America/Hermosillo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("265","MX","America/Tijuana","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("266","MX","America/Bahia_Banderas","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("267","MY","Asia/Kuala_Lumpur","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("268","MY","Asia/Kuching","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("269","MZ","Africa/Maputo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("270","NA","Africa/Windhoek","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("271","NC","Pacific/Noumea","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("272","NE","Africa/Niamey","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("273","NF","Pacific/Norfolk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("274","NG","Africa/Lagos","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("275","NI","America/Managua","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("276","NL","Europe/Amsterdam","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("277","NO","Europe/Oslo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("278","NP","Asia/Kathmandu","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("279","NR","Pacific/Nauru","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("280","NU","Pacific/Niue","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("281","NZ","Pacific/Auckland","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("282","NZ","Pacific/Chatham","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("283","OM","Asia/Muscat","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("284","PA","America/Panama","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("285","PE","America/Lima","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("286","PF","Pacific/Tahiti","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("287","PF","Pacific/Marquesas","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("288","PF","Pacific/Gambier","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("289","PG","Pacific/Port_Moresby","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("290","PG","Pacific/Bougainville","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("291","PH","Asia/Manila","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("292","PK","Asia/Karachi","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("293","PL","Europe/Warsaw","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("294","PM","America/Miquelon","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("295","PN","Pacific/Pitcairn","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("296","PR","America/Puerto_Rico","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("297","PS","Asia/Gaza","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("298","PS","Asia/Hebron","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("299","PT","Europe/Lisbon","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("300","PT","Atlantic/Madeira","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("301","PT","Atlantic/Azores","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("302","PW","Pacific/Palau","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("303","PY","America/Asuncion","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("304","QA","Asia/Qatar","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("305","RE","Indian/Reunion","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("306","RO","Europe/Bucharest","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("307","RS","Europe/Belgrade","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("308","RU","Europe/Kaliningrad","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("309","RU","Europe/Moscow","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("310","RU","Europe/Simferopol","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("311","RU","Europe/Volgograd","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("312","RU","Europe/Kirov","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("313","RU","Europe/Astrakhan","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("314","RU","Europe/Saratov","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("315","RU","Europe/Ulyanovsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("316","RU","Europe/Samara","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("317","RU","Asia/Yekaterinburg","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("318","RU","Asia/Omsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("319","RU","Asia/Novosibirsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("320","RU","Asia/Barnaul","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("321","RU","Asia/Tomsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("322","RU","Asia/Novokuznetsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("323","RU","Asia/Krasnoyarsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("324","RU","Asia/Irkutsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("325","RU","Asia/Chita","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("326","RU","Asia/Yakutsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("327","RU","Asia/Khandyga","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("328","RU","Asia/Vladivostok","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("329","RU","Asia/Ust-Nera","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("330","RU","Asia/Magadan","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("331","RU","Asia/Sakhalin","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("332","RU","Asia/Srednekolymsk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("333","RU","Asia/Kamchatka","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("334","RU","Asia/Anadyr","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("335","RW","Africa/Kigali","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("336","SA","Asia/Riyadh","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("337","SB","Pacific/Guadalcanal","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("338","SC","Indian/Mahe","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("339","SD","Africa/Khartoum","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("340","SE","Europe/Stockholm","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("341","SG","Asia/Singapore","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("342","SH","Atlantic/St_Helena","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("343","SI","Europe/Ljubljana","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("344","SJ","Arctic/Longyearbyen","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("345","SK","Europe/Bratislava","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("346","SL","Africa/Freetown","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("347","SM","Europe/San_Marino","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("348","SN","Africa/Dakar","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("349","SO","Africa/Mogadishu","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("350","SR","America/Paramaribo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("351","SS","Africa/Juba","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("352","ST","Africa/Sao_Tome","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("353","SV","America/El_Salvador","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("354","SX","America/Lower_Princes","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("355","SY","Asia/Damascus","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("356","SZ","Africa/Mbabane","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("357","TC","America/Grand_Turk","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("358","TD","Africa/Ndjamena","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("359","TF","Indian/Kerguelen","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("360","TG","Africa/Lome","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("361","TH","Asia/Bangkok","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("362","TJ","Asia/Dushanbe","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("363","TK","Pacific/Fakaofo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("364","TL","Asia/Dili","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("365","TM","Asia/Ashgabat","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("366","TN","Africa/Tunis","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("367","TO","Pacific/Tongatapu","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("368","TR","Europe/Istanbul","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("369","TT","America/Port_of_Spain","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("370","TV","Pacific/Funafuti","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("371","TW","Asia/Taipei","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("372","TZ","Africa/Dar_es_Salaam","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("373","UA","Europe/Kiev","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("374","UA","Europe/Uzhgorod","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("375","UA","Europe/Zaporozhye","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("376","UG","Africa/Kampala","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("377","UM","Pacific/Midway","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("378","UM","Pacific/Wake","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("379","US","America/New_York","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("380","US","America/Detroit","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("381","US","America/Kentucky/Louisville","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("382","US","America/Kentucky/Monticello","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("383","US","America/Indiana/Indianapolis","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("384","US","America/Indiana/Vincennes","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("385","US","America/Indiana/Winamac","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("386","US","America/Indiana/Marengo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("387","US","America/Indiana/Petersburg","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("388","US","America/Indiana/Vevay","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("389","US","America/Chicago","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("390","US","America/Indiana/Tell_City","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("391","US","America/Indiana/Knox","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("392","US","America/Menominee","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("393","US","America/North_Dakota/Center","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("394","US","America/North_Dakota/New_Salem","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("395","US","America/North_Dakota/Beulah","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("396","US","America/Denver","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("397","US","America/Boise","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("398","US","America/Phoenix","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("399","US","America/Los_Angeles","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("400","US","America/Anchorage","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("401","US","America/Juneau","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("402","US","America/Sitka","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("403","US","America/Metlakatla","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("404","US","America/Yakutat","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("405","US","America/Nome","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("406","US","America/Adak","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("407","US","Pacific/Honolulu","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("408","UY","America/Montevideo","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("409","UZ","Asia/Samarkand","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("410","UZ","Asia/Tashkent","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("411","VA","Europe/Vatican","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("412","VC","America/St_Vincent","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("413","VE","America/Caracas","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("414","VG","America/Tortola","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("415","VI","America/St_Thomas","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("416","VN","Asia/Ho_Chi_Minh","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("417","VU","Pacific/Efate","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("418","WF","Pacific/Wallis","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("419","WS","Pacific/Apia","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("420","YE","Asia/Aden","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("421","YT","Indian/Mayotte","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("422","ZA","Africa/Johannesburg","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("423","ZM","Africa/Lusaka","2019-12-15 14:03:02","2019-12-15 14:03:02");
INSERT INTO sm_time_zones VALUES("424","ZW","Africa/Harare","2019-12-15 14:03:02","2019-12-15 14:03:02");



DROP TABLE sm_to_dos;

CREATE TABLE `sm_to_dos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `todo_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `complete_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'P' COMMENT 'C for complete, N for not Complete, P Pending',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT '1',
  `updated_by` int(11) DEFAULT '1',
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_unit_manages;

CREATE TABLE `sm_unit_manages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_form` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_upcoming_tenders;

CREATE TABLE `sm_upcoming_tenders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tender_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tender_result` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `open_date` date DEFAULT NULL,
  `is_winner` int(11) NOT NULL DEFAULT '0' COMMENT '0 no, 1 yes',
  `is_expired` int(11) NOT NULL DEFAULT '0' COMMENT '0 no, 1 yes',
  `winner_compititor_id` int(11) DEFAULT NULL,
  `notice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specifications` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_order_status` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_upcoming_tenders VALUES("1","1","Vel tenetur a quia sint quas.","76735347","","2019-12-09","0","1","","","","","2019-12-15 14:03:01","2019-12-15 14:03:51");
INSERT INTO sm_upcoming_tenders VALUES("2","2","Qui similique architecto repellendus maxime dolorum qui error.","23030015","","2019-12-10","0","1","","","","","2019-12-15 14:03:01","2019-12-15 14:03:51");
INSERT INTO sm_upcoming_tenders VALUES("3","3","Corrupti culpa sunt debitis voluptatem officiis.","47363189","","2019-12-11","0","1","","","","","2019-12-15 14:03:01","2019-12-15 14:03:51");
INSERT INTO sm_upcoming_tenders VALUES("4","4","Repellendus deleniti nobis dicta rerum labore.","52291039","","2019-12-12","0","1","","","","","2019-12-15 14:03:01","2019-12-15 14:03:51");
INSERT INTO sm_upcoming_tenders VALUES("5","5","Provident dolore delectus qui aspernatur impedit.","44204498","","2019-12-13","0","1","","","","","2019-12-15 14:03:01","2019-12-15 14:03:51");
INSERT INTO sm_upcoming_tenders VALUES("6","6","Eligendi magni dolor maiores repellendus illo hic amet.","31741709","","2019-12-14","0","1","","","","","2019-12-15 14:03:01","2019-12-15 14:03:51");
INSERT INTO sm_upcoming_tenders VALUES("7","7","Sed saepe nisi illum et delectus quos.","14013777","","2019-12-15","0","1","","","","","2019-12-15 14:03:01","2019-12-17 11:26:44");
INSERT INTO sm_upcoming_tenders VALUES("8","8","Eos in nisi deserunt ullam ipsam repellendus esse.","99758329","","2019-12-16","0","1","","","","","2019-12-15 14:03:01","2019-12-17 11:26:44");
INSERT INTO sm_upcoming_tenders VALUES("9","9","Dolor molestiae dolores rerum totam rerum.","79756552","","2019-12-17","0","0","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");
INSERT INTO sm_upcoming_tenders VALUES("10","10","Sequi vitae similique aut ex quidem.","36292242","","2019-12-18","0","0","","","","","2019-12-15 14:03:01","2019-12-15 14:03:01");



DROP TABLE sm_upload_contents;

CREATE TABLE `sm_upload_contents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_type` int(11) DEFAULT NULL,
  `available_for_role` int(11) DEFAULT NULL,
  `available_for_class` int(11) DEFAULT NULL,
  `available_for_section` int(11) DEFAULT NULL,
  `upload_date` date DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_file` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_user_logs;

CREATE TABLE `sm_user_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `role_id` tinyint(4) DEFAULT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_user_logs VALUES("1","1","1","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-15 16:43:08","2019-12-15 16:43:08");
INSERT INTO sm_user_logs VALUES("2","18","3","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-15 18:35:39","2019-12-15 18:35:39");
INSERT INTO sm_user_logs VALUES("3","1","1","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-15 18:42:51","2019-12-15 18:42:51");
INSERT INTO sm_user_logs VALUES("4","1","1","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-17 11:26:43","2019-12-17 11:26:43");
INSERT INTO sm_user_logs VALUES("5","1","1","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-17 11:49:55","2019-12-17 11:49:55");
INSERT INTO sm_user_logs VALUES("6","1","1","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-17 11:55:50","2019-12-17 11:55:50");
INSERT INTO sm_user_logs VALUES("7","1","1","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-17 11:55:50","2019-12-17 11:55:50");
INSERT INTO sm_user_logs VALUES("8","1","1","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-17 11:55:58","2019-12-17 11:55:58");
INSERT INTO sm_user_logs VALUES("9","11","3","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-17 12:22:02","2019-12-17 12:22:02");
INSERT INTO sm_user_logs VALUES("10","1","1","::1","Chrome 78.0.3904, Mac 10.13.6","1","2019-12-17 12:24:10","2019-12-17 12:24:10");



DROP TABLE sm_visitors;

CREATE TABLE `sm_visitors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitor_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_person` tinyint(4) DEFAULT NULL,
  `purpose` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `in_time` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `out_time` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` int(11) DEFAULT '1',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE sm_weekends;

CREATE TABLE `sm_weekends` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `is_weekend` int(11) DEFAULT NULL,
  `active_status` int(11) NOT NULL DEFAULT '1',
  `school_id` int(11) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sm_weekends VALUES("1","Saturday","1","0","1","1","","");
INSERT INTO sm_weekends VALUES("2","Sunday","2","0","1","1","","");
INSERT INTO sm_weekends VALUES("3","Monday","3","0","1","1","","");
INSERT INTO sm_weekends VALUES("4","Tuesday","4","0","1","1","","");
INSERT INTO sm_weekends VALUES("5","Wednesday","5","0","1","1","","");
INSERT INTO sm_weekends VALUES("6","Thursday","6","0","1","1","","");
INSERT INTO sm_weekends VALUES("7","Friday","7","1","1","1","","");



DROP TABLE tickets;

CREATE TABLE `tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assign_user` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `priority_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tickets VALUES("1","2","1","Odit id cumque sint sequi modi distinctio aspernatur.","","I must have him Tortoise, if you doing our breath.\" \"I\'ll try if I\'m afraid,\' said the Hatter shook the jury-box, and hurried off. The other side of the part about in Wonderland, though she had to.","1","0","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO tickets VALUES("2","3","2","Expedita voluptates ut aliquam nam rerum voluptatem.","","Rabbit came up in a regular rule: you dry enough!\' They all comfortable, and don\'t know why do that had gone. \'Well! I\'ve tried to find that there was obliged to rise like that!\' But at the pool.","2","0","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO tickets VALUES("3","4","3","Ratione quasi magnam fugiat tenetur quam.","","That he went to sell you more than nine feet in confusion, as she remarked. \'There might be the strange at the first saw maps and once or twice she swallowed one arm affectionately into his nose.","3","0","2019-12-15 14:03:12","2019-12-15 14:03:12");
INSERT INTO tickets VALUES("4","5","4","Odio quod deserunt autem nisi similique suscipit aut.","","He only one on the window, and the jurors were followed him to the reason is--\' here and conquest. Edwin and they went down it. \'They all anxious to see some severity; \'it\'s very well as much.","4","0","2019-12-15 14:03:12","2019-12-15 14:03:12");



DROP TABLE users;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(4) DEFAULT NULL,
  `full_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usertype` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 = off, 1 = on',
  `active_status` tinyint(4) NOT NULL DEFAULT '1',
  `created_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users VALUES("1","1","Super Admin","spn5@spondonit.com","spn5@spondonit.com","$2y$10$.rlcR8CLBgIzbfkU/lKAOOxA/UPKMdu/8bcsP7l4auyGU4bN8TROe","","1","1","","","","2019-12-15 14:02:57","2019-12-15 14:02:57");
INSERT INTO users VALUES("2","2","Joan Blick","joan_blick@demo.com","joan_blick@demo.com","$2y$10$SZB5MgO8.saiCtXViGE1B.TjfrSo7JJixZW66IR4G4QbVfopxZSJ2","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("3","2","Davion Runolfsson","davion_runolfsson@demo.com","davion_runolfsson@demo.com","$2y$10$01c86553613EX1fUEklLsOgHCpjrHTyvb0KJEB8W0ojBmEfzHUxI6","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("4","2","Chadrick Wiegand","chadrick_wiegand@demo.com","chadrick_wiegand@demo.com","$2y$10$PR1H3f/WFJ7kg/VF7NfGoOUyd0LDv6ip0Xf68w//qjdovn4RUtUta","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("5","2","Darwin Kris","darwin_kris@demo.com","darwin_kris@demo.com","$2y$10$TdF.UhYEWmBbZvwLeQKDH.bBwhCI66qG/hy2Ep/0UEQrf.h735Ipy","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("6","2","Napoleon Hyatt","napoleon_hyatt@demo.com","napoleon_hyatt@demo.com","$2y$10$UjsDbp84rmoDpxJZQ1FPVugY9CKP9MxulBykzvWC.b7uNGvzBmJqe","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("7","2","Laron Kris","laron_kris@demo.com","laron_kris@demo.com","$2y$10$kdQ.RV9Ja4HO83B59DTPAuq2jvryeh55.Wz9WZlVGgcnqOXaGaT32","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("8","2","Matteo Kunde","matteo_kunde@demo.com","matteo_kunde@demo.com","$2y$10$2QQt7Cn9RQ2w.J3CGr/Cj.X/TTdKe98v7csmqdFLbZQLhcSZ/BUAW","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("9","2","Travon Ledner","travon_ledner@demo.com","travon_ledner@demo.com","$2y$10$ld0BopCPkzspb40B.I1om.PfZL.r1MprLDXMJFjoO/Gc3jXa.ahnK","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("10","2","Lorenz Abbott","lorenz_abbott@demo.com","lorenz_abbott@demo.com","$2y$10$yA5T04jufdy5o64gBGK2x.yatOUqgx/odihDzTFh9Qzm5zvl0LThS","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("11","3","Charlie Labadie","charlie_labadie@demo.com","charlie_labadie@demo.com","$2y$10$bnVEwUr73XbIvGISg6mBNuFo5U5DK3EzQO7bP7twoc0GTjHKsEU7m","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("12","3","Jerry Schulist","jerry_schulist@demo.com","jerry_schulist@demo.com","$2y$10$mHq7awBI9T1.h8hAwdJXHunuG66S9TbVIoDGh3qpAD/ms6VabO1pS","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("13","3","Norbert Wolff","norbert_wolff@demo.com","norbert_wolff@demo.com","$2y$10$LWA/CcTUaaD2RB4BAqDCXOSZcYWfLe6DpfMe10iXirqVfCW3yAyji","","1","1","","","","2019-12-15 14:02:58","2019-12-15 14:02:58");
INSERT INTO users VALUES("14","3","Jarrett Tremblay","jarrett_tremblay@demo.com","jarrett_tremblay@demo.com","$2y$10$4beVSAJJarzAQ8ku6Ob4yOexNX/to6dFQOX6I0OXnmi5L3rx5Em/m","","1","1","","","","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO users VALUES("15","3","Kaleb Ankunding","kaleb_ankunding@demo.com","kaleb_ankunding@demo.com","$2y$10$YpQCBCmpa9MI1cYQaKz4w.LsoBrCS2xYcUSZ8oYqh.jmUVCfWl3f.","","1","1","","","","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO users VALUES("16","3","Davin Paucek","davin_paucek@demo.com","davin_paucek@demo.com","$2y$10$M1IgfvD57Kv32Ztv4g.aU./lgLqisrt2pkfB9RCmzksVx/Tlpg1RO","","1","1","","","","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO users VALUES("17","3","Vern Pollich","vern_pollich@demo.com","vern_pollich@demo.com","$2y$10$BRWzbQcXZLAfdhyGnWZiZOQOky5d.i1TJHo6IlT6Giwv.s2qSvOoO","","1","1","","","","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO users VALUES("18","3","Kennith Heathcote","kennith_heathcote@demo.com","kennith_heathcote@demo.com","$2y$10$nca15ITLlE0IrqEYKCIZ7uf9o8W4JFYhreYTOoBdrjXHanbtCj40W","","1","1","","","","2019-12-15 14:02:59","2019-12-15 14:02:59");
INSERT INTO users VALUES("19","3","Rodrick Hayes","rodrick_hayes@demo.com","rodrick_hayes@demo.com","$2y$10$pZ45Uo89JrRhDWPWVFulgeanHevt3/7IxKImyIkEzLSpp.hVexday","","1","1","","","","2019-12-15 14:02:59","2019-12-15 14:02:59");



