
/*Table structure for table `sys_company` */

DROP TABLE IF EXISTS `sys_company`;

CREATE TABLE `sys_company` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sort_id` int DEFAULT '0',
  `company_name` varchar(100) DEFAULT NULL,
  `company_address` varchar(200) DEFAULT NULL,
  `country` int DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `vat_number` varchar(50) DEFAULT NULL,
  `trade_license_no` varchar(50) DEFAULT NULL,
  `trade_license_exp_date` date DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `iban_no` varchar(200) DEFAULT NULL,
  `branch_swift_code` varchar(200) DEFAULT NULL,
  `company_logo` varchar(100) DEFAULT NULL,
  `digital_stamp` varchar(100) DEFAULT NULL,
  `net_vat` int DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `created_by` tinyint DEFAULT NULL,
  `updated_by` tinyint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pdf_header` varchar(50) DEFAULT NULL,
  `pdf_footer` varchar(50) DEFAULT NULL,
  `pdf_watermark` varchar(50) DEFAULT NULL,
  `pdf_first_page` varchar(50) DEFAULT NULL,
  `sales_code` varchar(10) DEFAULT NULL,
  `other_code` varchar(10) DEFAULT NULL,
  `currency_id` int DEFAULT '1',
  `decimal_point` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Data for the table `sys_company` */

insert  into `sys_company`(`id`,`sort_id`,`company_name`,`company_address`,`country`,`city`,`email`,`website`,`telephone`,`fax`,`mobile`,`vat_number`,`trade_license_no`,`trade_license_exp_date`,`bank_name`,`account_number`,`iban_no`,`branch_swift_code`,`company_logo`,`digital_stamp`,`net_vat`,`status`,`created_by`,`updated_by`,`created_at`,`updated_at`,`pdf_header`,`pdf_footer`,`pdf_watermark`,`pdf_first_page`,`sales_code`,`other_code`,`currency_id`,`decimal_point`) values 

(1,1,'SYSCOM','Office 102, Easa Saleh Al Gurg Building, \r\nKhalid Bin Waleed Street, \r\nDubai, United Arab Emirates',231,'DUBAI','sales@sysllc.com','sysllc.com','+971 04 3522433','+971 04 3522432','+971528863140','100014280000003','696506','2024-10-29','National Bank Of Ras Al Khaimah (RAK bank)','0332136326001',NULL,NULL,'public/uploads/staff/b529e19c5bd88e7a4b50a3eafefe289c.png','',5,1,1,1,'2025-04-29 09:59:41','2024-06-05 16:37:23','syscom-pdf-header.jpg','syscom-pdf-footer.jpg','syscom-watermark-sm.png','syscom-pdf-first-page.jpg','SID','S',1,2),

(2,3,'SYSCOM FZE','RA08FD03, \r\nJebel Ali Freezone, \r\nJebel Ali, Dubai, UAE',231,'Dubai','sales@sysllc.com','www.sysllc.com','043522433','043522433','+971 50 424 8573','104144838000003','12449432','2024-03-23','National Bank Of Ras Al Khaimah (RAK bank)','0183072086001',NULL,NULL,'public/uploads/staff/e982424bdcf3a71bf5045ee71af0f238.png','',NULL,1,1,1,'2025-04-29 09:59:42','2024-06-05 16:38:19','fze-pdf-header.jpg','fze-pdf-footer.jpg','syscom-watermark-sm.png','syscom-pdf-first-page.jpg','SYZ','F',1,2),

(3,4,'SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1','5th Floor 501, Lulu Exchange Building, \r\nElectra Street, Opposite Royal Rose Hotel, \r\nAbu Dhabi, United Arab Emirates.',231,'Abu Dhabi','sales@sysllc.com','www.sysllc.com','+971 2 446 6125','.','+971 56 591 9519','100014280000003','CN-2547358','2024-03-22','ADCB','11377750820001',NULL,NULL,'public/uploads/staff/8aa77ecab5ca71f2d0e40c963cb00b72.png','',NULL,1,1,1,'2025-04-29 09:59:42','2024-05-29 21:31:13','syscom-auh-pdf-header.jpg','syscom-auh-pdf-footer.jpg','syscom-watermark-sm.png','syscom-pdf-first-page.jpg','SIA','A',1,2),

(4,6,'SYSCOM DISTRIBUTION LTD','65, Black Rod Close, \r\nHayes, Middlesex, UB3 4QL, \r\nUNITED KINGDOM',232,'LONDON','sales@sysllc.co.uk','https://sysllc.co.uk/','020 8163 2949','.','+44 7404 919 156','431036050','14416570','2028-10-12','Lloyds Bank','48695660',NULL,NULL,'public/uploads/staff/d3fe37f9ebcd76e919540706a60c4c72.png','',NULL,1,1,1,'2025-04-29 09:59:44','2024-06-05 21:05:10','syscom-ltd-pdf-header.jpg','syscom-ltd-pdf-footer.jpg','syscom-watermark-sm.png','syscom-ltd-pdf-first-page.jpg','SIVUK','UK',9,2),

(5,5,'SYSCOM IT SOLUTIONS LLC','Office # 2, Easa Saleh Al Gurg Buiding,\r\nKhalid Bin Walid Street, Bur Dubai,\r\nDubai, United Arab Emirates.',231,'Dubai','sales@sysllc.com','www.sysllc.com','043522433','.','+971 52 886 3140','104345660500003','1263889','2024-11-29','National Bank Of Ras Al Khaimah (RAK bank)','0333251112001',NULL,NULL,'public/uploads/company/87ce86d3c40fec8ebf671a052bbac3b9.png','',NULL,1,1,1,'2025-04-29 09:59:45','2024-05-29 14:04:06','syscom-it-solutions-pdf-header.jpg','syscom-it-solutions-pdf-footer.jpg','syscom-watermark-sm.png','syscom-it-solutions-pdf-first-page.jpg','SIS','IT',1,2),

(6,2,'SYSCOM DISTRIBUTIONS LLC','Office # 2, Easa Saleh Al Gurg Buiding,\r\nKhalid Bin Walid Street, Bur Dubai,\r\nDubai, United Arab Emirates.',231,'Dubai','sales@sysllc.com','www.sysllc.com','+971 04 3522433','+971 04 3522432','+971528863140','100014280000003','696506','2024-10-29','National Bank of Ras Al Khaimah (RAK bank)','0332136326001','AE650400000332136326001','NRAKAEAK','public/uploads/company/3906805be7fb5655f3cdae6deab0b311.png','',NULL,1,1,1,'2025-04-29 09:59:45','2025-01-24 13:46:14','syscom-pdf-header.jpg','syscom-pdf-footer.jpg','syscom-watermark-sm.png','syscom-pdf-first-page.jpg','SIV','D',1,2),

(7,7,'STACK LINK UK LTD','268 Bath Road, Slough, \r\nBerkshire, SL1 4DX,\r\nUNITED KINGDOM',232,'England','sales@stacklink.co.uk','https://stacklink.co.uk/','+447440669339','.','+447440669339','436 6804 77',',','2024-09-30','LLOYDS BANK','32756168',NULL,NULL,'public/uploads/staff/0a19fcb2d3cf1c3e3d013f961bf42ad6.png','',NULL,1,1,47,'2025-04-29 09:59:46','2024-07-19 19:04:57','stacklink-pdf-header.jpg','stacklink-pdf-footer.jpg','stacklink-watermark-sm.png','stacklink-pdf-first-page.jpg','SIVSL','USL',9,2),

(8,8,'SUPREME SYSTEM TRADING ESTABLISHMENT','6549, Wadi Al Shuara - \r\nAl Olaya District, Unit No 5569, \r\nRiyadh 12211 - 3805',194,'Riyadh','sales@supreme.sa','https://supreme.sa/','+966-11-210-9668','NA','+966-55-490-9327','302071027400003','1010694862','2025-02-07','Riyad Bank','3233731619940',NULL,NULL,'public/uploads/company/4a0e9e6fad11efcd92f2bccff75963b1.png','',NULL,1,1,1,'2025-04-29 09:59:47','2024-06-05 21:06:06','supreme-pdf-header.jpg','supreme-pdf-footer.jpg','syscom-watermark-sm.png','supreme-pdf-first-page.jpg','SUP','SA',4,2),

(9,9,'SYSCOM DISTRIBUTION WLL','Off. # 6, First Floor,\r\nAl Tadamon Complex, \r\nD ring road, Doha, Qatar',179,'Doha','sales@sysllc.com','www.sysllc.com','+97444906875','+97444906875','+97433450411','NA','200354','2024-09-28','Qatar National Bank','0250559112001',NULL,NULL,'public/uploads/company/009652d562097d5eea5bdead831c7782.jpg','',NULL,1,1,1,'2025-04-29 09:59:48','2024-06-05 21:06:40','syscom-qatar-pdf-header.jpg','syscom-qatar-pdf-footer.jpg','syscom-watermark-sm.png','syscom-qatar-pdf-first-page.jpg','SIQ','Q',2,2),

(10,10,'SUPREME SYSTEM DISTRIBUTORS SPC','Floor 6, Al Shumoor Building, \r\nOpp to Chamber of Commerce, CBD, \r\nMuscat, Sultanate of Oman',166,'Ruwi','sales@ssd.om','www.ssd.om','+968 24781836','+968 24781836','+968 92743953','OM1100238191','1401539','2024-10-20','Bank Muscat','0327061228940014','OM880270327061228940014','AL KHUWAIR 33','public/uploads/staff/9eaafc7f3b00d27c52a704566bef626b.png','',NULL,1,17,1,'2025-04-29 09:59:38','2024-11-06 14:12:16','supreme-spc-pdf-header.jpg','supreme-spc-pdf-footer.jpg','syscom-watermark-sm.png','supreme-spc-pdf-first-page.jpg','SSD-INV','O',3,3),

(11,11,'SYSCOM DISTRIBUTION LIMITED','A8, Willowdale, \r\nRhapta Road, \r\nWestlands, Nairobi',204,'Nairobi','sales@sysllc.ke','www.sysllc.ke','+254101944953','+000000','+971528863140','00001','PVT-EYU3MKR2','2025-02-13','Bank','0327061228940014','AE650400000332136326001','SWIFT1','','',NULL,1,17,1,'2025-04-29 09:59:49','2025-02-10 20:17:55','syscom-distribution-limited-pdf-header.jpg','syscom-distribution-limited-pdf-footer.jpg','syscom-watermark-sm.png','syscom-distribution-limited-pdf-first-page.jpg','SIK','K',1,2),

(12,12,'TRIANGLE SYSTEMS LLC','Art Tower 103 Al Mina Road\r\nBur Dubai\r\nDubai, United Arab Emirates',231,'Dubai','satya@trianglesystemsllc.com','www.trianglesystemsllc.com','+971 43393973','.','+971 55 8857868','100442513600003','1803660','2025-04-02','RAK BANK UMM HURAIR, BURDUBAI','0332667071001','AE33 0400 0003 3266 7071 001','NRAKAEAK','public/uploads/company/f6a923fdfbf26ca27a93a3c68c306e63.jpg','',NULL,1,1,NULL,'2025-04-29 09:59:50','2025-01-21 18:03:31','triangle-systems-pdf-header.jpg','triangle-systems-pdf-footer.jpg','triangle-watermark-sm.png','triangle-systems-pdf-first-page.jpg','SIT','T',1,2);
