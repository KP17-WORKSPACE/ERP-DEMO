-- Adminer 4.8.1 MySQL 8.0.30 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DELIMITER ;;

DROP PROCEDURE IF EXISTS `get_bank_payment_adjestments`;;
CREATE PROCEDURE `get_bank_payment_adjestments`(IN customerId INT(10),IN companyId INT(10))
BEGIN

SELECT doc_number, pi_date as doc_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.pi_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount) + SUM(t2.vatamount) total, (SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_payment_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number AND t3.status=1) paid
FROM sys_purchase_invoice t1
JOIN sys_purchase_invoice_items t2
ON t1.id = t2.pi_id
WHERE t1.vendors=customerId and t1.company_id=companyId AND t1.status=1
GROUP BY t1.id) tbl WHERE total > paid;

END;;

DROP PROCEDURE IF EXISTS `get_bank_payment_adjestments_edit`;;
CREATE PROCEDURE `get_bank_payment_adjestments_edit`(IN customerId INT(10),IN companyId INT(10))
BEGIN

SELECT doc_number, pi_date AS doc_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.pi_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount) + SUM(t2.vatamount) total, 
(SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_payment_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number AND t3.status=1) paid
FROM sys_purchase_invoice t1
JOIN sys_purchase_invoice_items t2
ON t1.id = t2.pi_id
WHERE t1.vendors=customerId AND t1.company_id=companyId AND t1.status=1
GROUP BY t1.id) tbl; /*WHERE total > paid;*/

END;;

DROP PROCEDURE IF EXISTS `get_bank_payment_adjestments_edit_jv`;;
CREATE PROCEDURE `get_bank_payment_adjestments_edit_jv`(IN customerId INT(10),IN companyId INT(10),IN docNo varchar(20))
BEGIN
SELECT doc_number, pi_date AS doc_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.pi_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount) + SUM(t2.vatamount) total, 
(SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_payment_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number AND t3.status=1) paid
FROM sys_purchase_invoice t1
JOIN sys_purchase_invoice_items t2
ON t1.id = t2.pi_id
WHERE t1.vendors=customerId AND t1.company_id=companyId AND t1.status=1 /*and t1.doc_number in ( select bi_doc_no from sys_payment_adjustments where bi_doc_number= docNo)*/
GROUP BY t1.id) tbl; /*WHERE total > paid;*/
END;;

DROP PROCEDURE IF EXISTS `get_bank_receipt_adjestments`;;
CREATE PROCEDURE `get_bank_receipt_adjestments`(IN customerId INT(10),IN companyId INT(10))
BEGIN

SELECT doc_number, doc_date, lpo_number, lpo_date, ROUND(total, 2) total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.doc_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount) + SUM(t2.vatamount) - (t1.deal_discount+ (t1.deal_discount*IFNULL(MAX(t2.tax), 0)/100)) total, (SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number and t3.status=1) paid
FROM sys_sales_invoice t1
JOIN sys_sales_invoice_items t2
ON t1.id = t2.si_id
WHERE t1.customer=customerId AND t1.company_id=companyId AND t1.status=1
GROUP BY t1.id) tbl WHERE total > paid or paid=0;

END;;

DROP PROCEDURE IF EXISTS `get_bank_receipt_adjestments_edit`;;
CREATE PROCEDURE `get_bank_receipt_adjestments_edit`(IN customerId INT(10),IN companyId INT(10))
BEGIN

SELECT doc_number, doc_date, lpo_number, lpo_date, ROUND(total, 2) total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.doc_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount) + SUM(t2.vatamount) - (t1.deal_discount+ (t1.deal_discount*IFNULL(MAX(t2.tax), 0)/100)) total, 
(SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number AND t3.status=1) paid
FROM sys_sales_invoice t1
JOIN sys_sales_invoice_items t2
ON t1.id = t2.si_id
WHERE t1.customer=customerId AND t1.company_id=companyId AND t1.status=1
GROUP BY t1.id) tbl; /*WHERE total > paid;*/

END;;

DROP PROCEDURE IF EXISTS `get_bank_receipt_adjestments_edit_jv`;;
CREATE PROCEDURE `get_bank_receipt_adjestments_edit_jv`(IN customerId INT(10),IN companyId INT(10),IN docNo VARCHAR(20))
BEGIN
SELECT doc_number, doc_date, lpo_number, lpo_date, ROUND(total, 2) total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.doc_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount) + SUM(t2.vatamount) - (t1.deal_discount+ (t1.deal_discount*IFNULL(MAX(t2.tax), 0)/100)) total, 
(SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number AND t3.status=1) paid
FROM sys_sales_invoice t1
JOIN sys_sales_invoice_items t2
ON t1.id = t2.si_id
WHERE t1.customer=customerId AND t1.company_id=companyId AND t1.status=1 /*AND t1.doc_number IN ( SELECT bi_doc_no FROM sys_receipt_adjustments WHERE bi_doc_number= docNo)*/
GROUP BY t1.id) tbl; /*WHERE total > paid;*/
END;;

DROP PROCEDURE IF EXISTS `get_cash_payment_adjestments`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_cash_payment_adjestments`(IN customerId INT(10))
BEGIN

SELECT doc_number, pi_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.pi_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount)total, (SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number) paid
FROM sys_purchase_invoice t1
JOIN sys_purchase_invoice_items t2
ON t1.id = t2.pi_id
WHERE t1.vendors=customerId
GROUP BY t1.id) tbl WHERE total > paid;

END;;

DROP PROCEDURE IF EXISTS `get_cash_receipt_adjestments`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_cash_receipt_adjestments`(IN customerId INT(10))
BEGIN

SELECT doc_number, si_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.si_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount)total, (SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number) paid
FROM sys_sales_invoice t1
JOIN sys_sales_invoice_items t2
ON t1.id = t2.si_id
WHERE t1.customer=customerId
GROUP BY t1.id) tbl WHERE total > paid;

END;;

DROP PROCEDURE IF EXISTS `get_customer_ledger_entries`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_customer_ledger_entries`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
BEGIN

IF (search_case = 1) THEN

SELECT le.entry_date, le.entry_type, le.transaction_id, le.transaction_type, ca.account_name, ca.id, /*ra.bi_doc_no, ra.bi_doc_date,*/
(CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END) AS DebitAmount,
(CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END) AS CreditAmount
FROM sys_ledger_entries le
JOIN sys_chartofaccounts ca ON ca.id=le.account_id
/*JOIN sys_receipt_adjustments ra ON ra.bi_doc_number=le.transaction_id*/
WHERE le.transaction_type NOT IN('postdatedreceipt','postdatedpayment') AND le.account_id = accountid
GROUP BY le.id ORDER BY ca.account_name ASC, le.entry_date ASC;

END IF;

IF (search_case = 2) THEN

SELECT le.entry_date, le.entry_type, le.transaction_id, le.transaction_type, ca.account_name, ca.id, /*ra.bi_doc_no, ra.bi_doc_date,*/
(CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END) AS DebitAmount,
(CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END) AS CreditAmount
FROM sys_ledger_entries le
JOIN sys_chartofaccounts ca ON ca.id=le.account_id
/*JOIN sys_receipt_adjustments ra ON ra.bi_doc_number=le.transaction_id*/
WHERE le.transaction_type NOT IN('postdatedreceipt','postdatedpayment') AND le.entry_date BETWEEN from_date AND to_date
GROUP BY le.id ORDER BY ca.account_name ASC, le.entry_date ASC;

END IF;

IF (search_case = 3) THEN


SELECT le.entry_date, le.entry_type, le.transaction_id, le.transaction_type, ca.account_name, ca.id, /*ra.bi_doc_no, ra.bi_doc_date,*/
(CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END) AS DebitAmount,
(CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END) AS CreditAmount
FROM sys_ledger_entries le
JOIN sys_chartofaccounts ca ON ca.id=le.account_id
/*JOIN sys_receipt_adjustments ra ON ra.bi_doc_number=le.transaction_id*/
WHERE le.transaction_type NOT IN('postdatedreceipt','postdatedpayment') AND le.account_id = accountid AND le.entry_date BETWEEN from_date AND to_date
GROUP BY le.id ORDER BY ca.account_name ASC, le.entry_date ASC;

END IF;


/*SELECT doc_number, si_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.si_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount)total, (SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number) paid
FROM sys_sales_invoice t1
JOIN sys_sales_invoice_items t2
ON t1.id = t2.si_id
WHERE t1.customer=customerId
GROUP BY t1.id) tbl WHERE total > paid;*/

END;;

DROP PROCEDURE IF EXISTS `get_customer_opening_balance`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_customer_opening_balance`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
BEGIN

IF (search_case = 1) THEN
	SELECT le.account_id, ca.account_name,
	/*SUM((CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END)) AS DebitAmount,*/ '0.00' AS DebitAmount, '0.00' AS CreditAmount
	/*SUM((CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END)) AS CreditAmount*/  
	FROM sys_ledger_entries le
	JOIN sys_chartofaccounts ca ON ca.id=le.account_id
	WHERE le.account_id = accountid
	GROUP BY le.account_id;
END IF;

IF (search_case = 2) THEN
	SELECT le.account_id, ca.account_name,
	SUM((CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END)) AS DebitAmount,
	SUM((CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END)) AS CreditAmount
	FROM sys_ledger_entries le
	JOIN sys_chartofaccounts ca ON ca.id=le.account_id
	WHERE le.entry_date > from_date AND ca.subgroup=1
	GROUP BY le.account_id;
END IF;

IF (search_case = 3) THEN
	SELECT le.account_id, ca.account_name,
	SUM((CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END)) AS DebitAmount,
	SUM((CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END)) AS CreditAmount
	FROM sys_ledger_entries le
	JOIN sys_chartofaccounts ca ON ca.id=le.account_id
	WHERE le.account_id = accountid AND le.entry_date > from_date
	GROUP BY le.account_id;
END IF;

END;;

DROP PROCEDURE IF EXISTS `get_ledger_entries`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_ledger_entries`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
BEGIN

IF (search_case = 1) THEN

SELECT le.entry_date, le.entry_type, le.transaction_id, le.transaction_type, ca.account_name, ca.id, /*ra.bi_doc_no, ra.bi_doc_date,*/
(CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END) AS DebitAmount,
(CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END) AS CreditAmount
FROM sys_ledger_entries le
JOIN sys_chartofaccounts ca ON ca.id=le.account_id
/*JOIN sys_receipt_adjustments ra ON ra.bi_doc_number=le.transaction_id*/
WHERE le.transaction_type NOT IN('postdatedreceipt','postdatedpayment') AND le.account_id = accountid
GROUP BY le.id ORDER BY ca.account_name ASC, le.entry_date ASC;

END IF;

IF (search_case = 2) THEN

SELECT le.entry_date, le.entry_type, le.transaction_id, le.transaction_type, ca.account_name, ca.id, /*ra.bi_doc_no, ra.bi_doc_date,*/
(CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END) AS DebitAmount,
(CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END) AS CreditAmount
FROM sys_ledger_entries le
JOIN sys_chartofaccounts ca ON ca.id=le.account_id
/*JOIN sys_receipt_adjustments ra ON ra.bi_doc_number=le.transaction_id*/
WHERE le.transaction_type NOT IN('postdatedreceipt','postdatedpayment') AND le.entry_date BETWEEN from_date AND to_date
GROUP BY le.id ORDER BY ca.account_name ASC, le.entry_date ASC;

END IF;

IF (search_case = 3) THEN


SELECT le.entry_date, le.entry_type, le.transaction_id, le.transaction_type, ca.account_name, ca.id, /*ra.bi_doc_no, ra.bi_doc_date,*/
(CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END) AS DebitAmount,
(CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END) AS CreditAmount
FROM sys_ledger_entries le
JOIN sys_chartofaccounts ca ON ca.id=le.account_id
/*JOIN sys_receipt_adjustments ra ON ra.bi_doc_number=le.transaction_id*/
WHERE le.transaction_type NOT IN('postdatedreceipt','postdatedpayment') AND le.account_id = accountid AND le.entry_date BETWEEN from_date AND to_date
GROUP BY le.id ORDER BY ca.account_name ASC, le.entry_date ASC;

END IF;


/*SELECT doc_number, si_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.si_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount)total, (SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number) paid
FROM sys_sales_invoice t1
JOIN sys_sales_invoice_items t2
ON t1.id = t2.si_id
WHERE t1.customer=customerId
GROUP BY t1.id) tbl WHERE total > paid;*/

END;;

DROP PROCEDURE IF EXISTS `get_opening_balance`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_opening_balance`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
BEGIN

IF (search_case = 1) THEN
	SELECT le.account_id, ca.account_name,
	/*SUM((CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END)) AS DebitAmount,*/ '0.00' AS DebitAmount, '0.00' AS CreditAmount
	/*SUM((CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END)) AS CreditAmount*/  
	FROM sys_ledger_entries le
	JOIN sys_chartofaccounts ca ON ca.id=le.account_id
	WHERE le.account_id = accountid
	GROUP BY le.account_id;
END IF;

IF (search_case = 2) THEN
	SELECT le.account_id, ca.account_name,
	SUM((CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END)) AS DebitAmount,
	SUM((CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END)) AS CreditAmount
	FROM sys_ledger_entries le
	JOIN sys_chartofaccounts ca ON ca.id=le.account_id
	WHERE le.entry_date > from_date AND ca.subgroup NOT IN(1,3)
	GROUP BY le.account_id;
END IF;

IF (search_case = 3) THEN
	SELECT le.account_id, ca.account_name,
	SUM((CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END)) AS DebitAmount,
	SUM((CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END)) AS CreditAmount
	FROM sys_ledger_entries le
	JOIN sys_chartofaccounts ca ON ca.id=le.account_id
	WHERE le.account_id = accountid AND le.entry_date > from_date
	GROUP BY le.account_id;
END IF;

END;;

DROP PROCEDURE IF EXISTS `get_postdated_payment_adjestments`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_postdated_payment_adjestments`(IN customerId INT(10))
BEGIN

SELECT doc_number, pi_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.pi_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount)total, (SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number) paid
FROM sys_purchase_invoice t1
JOIN sys_purchase_invoice_items t2
ON t1.id = t2.pi_id
WHERE t1.vendors=customerId
GROUP BY t1.id) tbl WHERE total > paid;

END;;

DROP PROCEDURE IF EXISTS `get_postdated_receipt_adjestments`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_postdated_receipt_adjestments`(IN customerId INT(10))
BEGIN

SELECT doc_number, si_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.si_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount)total, (SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number) paid
FROM sys_sales_invoice t1
JOIN sys_sales_invoice_items t2
ON t1.id = t2.si_id
WHERE t1.customer=customerId
GROUP BY t1.id) tbl WHERE total > paid;

END;;

DROP PROCEDURE IF EXISTS `get_supplier_ledger_entries`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_supplier_ledger_entries`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
BEGIN

IF (search_case = 1) THEN

SELECT le.entry_date, le.entry_type, le.transaction_id, le.transaction_type, ca.account_name, ca.id, /*ra.bi_doc_no, ra.bi_doc_date,*/
(CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END) AS DebitAmount,
(CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END) AS CreditAmount
FROM sys_ledger_entries le
JOIN sys_chartofaccounts ca ON ca.id=le.account_id
/*JOIN sys_receipt_adjustments ra ON ra.bi_doc_number=le.transaction_id*/
WHERE le.transaction_type NOT IN('postdatedreceipt','postdatedpayment') AND le.account_id = accountid
GROUP BY le.id ORDER BY ca.account_name ASC, le.entry_date ASC;

END IF;

IF (search_case = 2) THEN

SELECT le.entry_date, le.entry_type, le.transaction_id, le.transaction_type, ca.account_name, ca.id, /*ra.bi_doc_no, ra.bi_doc_date,*/
(CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END) AS DebitAmount,
(CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END) AS CreditAmount
FROM sys_ledger_entries le
JOIN sys_chartofaccounts ca ON ca.id=le.account_id
/*JOIN sys_receipt_adjustments ra ON ra.bi_doc_number=le.transaction_id*/
WHERE le.transaction_type NOT IN('postdatedreceipt','postdatedpayment') AND le.entry_date BETWEEN from_date AND to_date
GROUP BY le.id ORDER BY ca.account_name ASC, le.entry_date ASC;

END IF;

IF (search_case = 3) THEN


SELECT le.entry_date, le.entry_type, le.transaction_id, le.transaction_type, ca.account_name, ca.id, /*ra.bi_doc_no, ra.bi_doc_date,*/
(CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END) AS DebitAmount,
(CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END) AS CreditAmount
FROM sys_ledger_entries le
JOIN sys_chartofaccounts ca ON ca.id=le.account_id
/*JOIN sys_receipt_adjustments ra ON ra.bi_doc_number=le.transaction_id*/
WHERE le.transaction_type NOT IN('postdatedreceipt','postdatedpayment') AND le.account_id = accountid AND le.entry_date BETWEEN from_date AND to_date
GROUP BY le.id ORDER BY ca.account_name ASC, le.entry_date ASC;

END IF;


/*SELECT doc_number, si_date, lpo_number, lpo_date, total, paid, (total - paid)balance FROM(
SELECT t1.doc_number, t1.si_date, t1.lpo_number,t1.lpo_date, SUM(t2.taxableamount)total, (SELECT IFNULL(SUM(t3.bi_paid), 0) FROM sys_receipt_adjustments t3 WHERE t3.bi_doc_no = t1.doc_number) paid
FROM sys_sales_invoice t1
JOIN sys_sales_invoice_items t2
ON t1.id = t2.si_id
WHERE t1.customer=customerId
GROUP BY t1.id) tbl WHERE total > paid;*/

END;;

DROP PROCEDURE IF EXISTS `get_supplier_opening_balance`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_supplier_opening_balance`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
BEGIN

IF (search_case = 1) THEN
	SELECT le.account_id, ca.account_name,
	/*SUM((CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END)) AS DebitAmount,*/ '0.00' AS DebitAmount, '0.00' AS CreditAmount
	/*SUM((CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END)) AS CreditAmount*/  
	FROM sys_ledger_entries le
	JOIN sys_chartofaccounts ca ON ca.id=le.account_id
	WHERE le.account_id = accountid
	GROUP BY le.account_id;
END IF;

IF (search_case = 2) THEN
	SELECT le.account_id, ca.account_name,
	SUM((CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END)) AS DebitAmount,
	SUM((CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END)) AS CreditAmount
	FROM sys_ledger_entries le
	JOIN sys_chartofaccounts ca ON ca.id=le.account_id
	WHERE le.entry_date > from_date AND ca.subgroup=3
	GROUP BY le.account_id;
END IF;

IF (search_case = 3) THEN
	SELECT le.account_id, ca.account_name,
	SUM((CASE WHEN le.entry_type=1 THEN le.amount ELSE 0 END)) AS DebitAmount,
	SUM((CASE WHEN le.entry_type=2 THEN le.amount ELSE 0 END)) AS CreditAmount
	FROM sys_ledger_entries le
	JOIN sys_chartofaccounts ca ON ca.id=le.account_id
	WHERE le.account_id = accountid AND le.entry_date > from_date
	GROUP BY le.account_id;
END IF;

END;;

DROP PROCEDURE IF EXISTS `get_trialbalance`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `get_trialbalance`(IN from_date DATE, IN to_date DATE)
BEGIN

	SELECT sg.id, sg.group_id, sg.title, (
	SELECT SUM(CASE WHEN entry_type=1 THEN amount ELSE 0 END) FROM sys_ledger_entries WHERE account_id IN (SELECT id FROM sys_chartofaccounts
		WHERE subgroup = sg.id) AND entry_date BETWEEN from_date AND to_date
	) dr_amount, (
	SELECT SUM(CASE WHEN entry_type=2 THEN amount ELSE 0 END) FROM sys_ledger_entries WHERE account_id IN (SELECT id FROM sys_chartofaccounts
		WHERE subgroup = sg.id) AND entry_date BETWEEN from_date AND to_date
	) cr_amount
	FROM sys_account_group_sub sg ORDER BY sg.title ASC;

END;;

DROP PROCEDURE IF EXISTS `set_delivery_note`;;
CREATE DEFINER=`erpuser`@`%` PROCEDURE `set_delivery_note`(IN docnumber INT(10))
BEGIN

SET @row_num = 0;

SELECT (@row_num:=@row_num + 1) AS num, sii.si_id, sii.qty, itm.part_number,itm.id part_number_id, itm.description, sii.unitprice,
IFNULL((SELECT qty FROM sys_delivery_note_items WHERE part_number = itm.id AND si_id=sii.si_id), 0) exeqty
FROM sys_sales_invoice_items sii
JOIN sm_items itm ON sii.part_number = itm.id
JOIN sys_sales_invoice si ON si.id = sii.si_id
WHERE sii.delivery_status = 0 AND si.id=docnumber;

END;;

DELIMITER ;

DROP TABLE IF EXISTS `sys_company`;
CREATE TABLE `sys_company` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `trade_name` varchar(255) DEFAULT NULL,
  `legal_entity_type` varchar(100) DEFAULT NULL,
  `industry` varchar(150) DEFAULT NULL,
  `parent_company` varchar(255) DEFAULT NULL,
  `date_of_incorporation` date DEFAULT NULL,
  `book_closed` date DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
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
  `finance_code` varchar(50) DEFAULT NULL,
  `branch_swift_code` varchar(200) DEFAULT NULL,
  `company_logo` varchar(100) DEFAULT NULL,
  `currency` varchar(50) DEFAULT NULL,
  `currency_digit` int DEFAULT NULL,
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
  `company_vat_rate` int DEFAULT '0',
  `owner_visa_copy` varchar(255) DEFAULT NULL,
  `owner_emirates_id` varchar(255) DEFAULT NULL,
  `owner_passport_copy` varchar(255) DEFAULT NULL,
  `owner_email` varchar(150) DEFAULT NULL,
  `sponsor_name` varchar(150) DEFAULT NULL,
  `sponsor_mobile` varchar(100) DEFAULT NULL,
  `sponsor_email` varchar(150) DEFAULT NULL,
  `sponsor_passport_copy` varchar(255) DEFAULT NULL,
  `sponsor_emirates_id` varchar(255) DEFAULT NULL,
  `sponsor_visa_copy` varchar(255) DEFAULT NULL,
  `contact_passport_copy` varchar(255) DEFAULT NULL,
  `contact_emirates_id` varchar(255) DEFAULT NULL,
  `contact_visa_copy` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(150) DEFAULT NULL,
  `contact_person_mobile` varchar(100) DEFAULT NULL,
  `contact_person_email` varchar(150) DEFAULT NULL,
  `contact_person_designation` varchar(150) DEFAULT NULL,
  `owner_mobile` varchar(20) DEFAULT NULL,
  `owner_name` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sys_company` (`id`, `trade_name`, `legal_entity_type`, `industry`, `parent_company`, `date_of_incorporation`, `book_closed`, `language`, `sort_id`, `company_name`, `company_address`, `country`, `city`, `email`, `website`, `telephone`, `fax`, `mobile`, `vat_number`, `trade_license_no`, `trade_license_exp_date`, `bank_name`, `account_number`, `iban_no`, `finance_code`, `branch_swift_code`, `company_logo`, `currency`, `currency_digit`, `digital_stamp`, `net_vat`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `pdf_header`, `pdf_footer`, `pdf_watermark`, `pdf_first_page`, `sales_code`, `other_code`, `currency_id`, `decimal_point`, `company_vat_rate`, `owner_visa_copy`, `owner_emirates_id`, `owner_passport_copy`, `owner_email`, `sponsor_name`, `sponsor_mobile`, `sponsor_email`, `sponsor_passport_copy`, `sponsor_emirates_id`, `sponsor_visa_copy`, `contact_passport_copy`, `contact_emirates_id`, `contact_visa_copy`, `contact_person_name`, `contact_person_mobile`, `contact_person_email`, `contact_person_designation`, `owner_mobile`, `owner_name`) VALUES
(1,	'233223',	'2323',	'2323',	'223',	'2025-09-12',	'2025-09-11',	NULL,	0,	'new company',	'sdfdsf',	3,	'sdfsdf',	'dssdf@gmail.com',	'https://google.com/',	'3343',	'3434',	'3434',	NULL,	NULL,	NULL,	'sfsdf',	'2323',	'232',	'23',	'23',	'uploads/company/banking_letters/58ed9bb0-7d4f-462b-af6f-7aff96acfb1d.png',	'SAR',	22,	'company/stamp/BvHJH7VppbZ6uLDA6vMsc2P3hbfDeaimGDcyrMA1.png',	NULL,	NULL,	NULL,	NULL,	'2025-09-26 09:06:09',	'2025-09-26 09:36:09',	NULL,	NULL,	NULL,	NULL,	'33223',	'2323',	1,	NULL,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	'sdfds',	'ksdfj',	'sdfsdfsdf',	'dfsdf',	'2025-09-05',	'2025-03-22',	NULL,	0,	'new onewww',	'dfsdf',	2,	'sdfdsf',	'sdfds@gmail.com',	'https://google.com/',	'3434',	'3434',	'3434343',	NULL,	NULL,	NULL,	'sdfsdf',	'2323',	'232',	'3434',	'323',	'uploads/company/banking_letters/511ab02c-67eb-4a4a-a458-2c89a5cf9e70.png',	'EUR',	1,	'company/stamp/0Ecf7mtOrDBwli49HdUGptzgec6znIRytP59Kx9g.jpeg',	NULL,	NULL,	NULL,	NULL,	'2025-09-26 09:59:01',	'2025-09-26 10:29:01',	NULL,	NULL,	NULL,	NULL,	'3323',	'2323',	1,	NULL,	0,	NULL,	NULL,	'C:\\Users\\DELL\\AppData\\Local\\Temp\\php43BE.tmp',	'dsfsdfsd@gmail.com',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'232323',	'afsdf'),
(3,	'sdfsd',	'sdfds',	'sdfds',	'3343',	'2025-09-12',	'2025-09-03',	NULL,	0,	'sdfdsf',	'sdfds',	2,	'sdfsd',	'dsfsd@gmail.com',	'https://google.com/',	'343434',	'3434',	'343434',	NULL,	NULL,	NULL,	'dfsdfs',	'232',	'2323',	'23',	'23',	'uploads/company/banking_letters/68468c3c-36e0-4e63-a4cf-4a6aa8134480.png',	'QAR',	2,	NULL,	NULL,	NULL,	NULL,	NULL,	'2025-09-26 10:02:19',	'2025-09-26 10:32:19',	NULL,	NULL,	NULL,	NULL,	'3434',	'3434',	1,	NULL,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(4,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	6,	'SYSCOM DISTRIBUTION LTD',	'65, Black Rod Close, \r\nHayes, Middlesex, UB3 4QL, \r\nUNITED KINGDOM',	232,	'LONDON',	'sales@sysllc.co.uk',	'https://sysllc.co.uk/',	'020 8163 2949',	'.',	'+44 7404 919 156',	'431036050',	'14416570',	'2028-10-12',	'Lloyds Bank',	'48695660',	NULL,	NULL,	NULL,	'public/uploads/staff/d3fe37f9ebcd76e919540706a60c4c72.png',	NULL,	NULL,	'',	NULL,	1,	1,	1,	'2025-04-29 04:29:44',	'2024-06-05 15:35:10',	'syscom-ltd-pdf-header.jpg',	'syscom-ltd-pdf-footer.jpg',	'syscom-watermark-sm.png',	'syscom-ltd-pdf-first-page.jpg',	'SIVUK',	'UK',	9,	2,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(5,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	5,	'SYSCOM IT SOLUTIONS LLC',	'Office # 2, Easa Saleh Al Gurg Buiding,\r\nKhalid Bin Walid Street, Bur Dubai,\r\nDubai, United Arab Emirates.',	231,	'Dubai',	'sales@sysllc.com',	'www.sysllc.com',	'043522433',	'.',	'+971 52 886 3140',	'104345660500003',	'1263889',	'2024-11-29',	'National Bank Of Ras Al Khaimah (RAK bank)',	'0333251112001',	NULL,	NULL,	NULL,	'public/uploads/company/87ce86d3c40fec8ebf671a052bbac3b9.png',	NULL,	NULL,	'',	NULL,	1,	1,	1,	'2025-04-29 04:29:45',	'2024-05-29 08:34:06',	'syscom-it-solutions-pdf-header.jpg',	'syscom-it-solutions-pdf-footer.jpg',	'syscom-watermark-sm.png',	'syscom-it-solutions-pdf-first-page.jpg',	'SIS',	'IT',	1,	2,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(6,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	2,	'SYSCOM DISTRIBUTIONS LLC',	'Office # 2, Easa Saleh Al Gurg Buiding,\r\nKhalid Bin Walid Street, Bur Dubai,\r\nDubai, United Arab Emirates.',	231,	'Dubai',	'sales@sysllc.com',	'www.sysllc.com',	'+971 04 3522433',	'+971 04 3522432',	'+971528863140',	'100014280000003',	'696506',	'2024-10-29',	'National Bank of Ras Al Khaimah (RAK bank)',	'0332136326001',	'AE650400000332136326001',	NULL,	'NRAKAEAK',	'public/uploads/company/3906805be7fb5655f3cdae6deab0b311.png',	NULL,	NULL,	'',	NULL,	1,	1,	1,	'2025-04-29 04:29:45',	'2025-01-24 08:16:14',	'syscom-pdf-header.jpg',	'syscom-pdf-footer.jpg',	'syscom-watermark-sm.png',	'syscom-pdf-first-page.jpg',	'SIV',	'D',	1,	2,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(7,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	7,	'STACK LINK UK LTD',	'268 Bath Road, Slough, \r\nBerkshire, SL1 4DX,\r\nUNITED KINGDOM',	232,	'England',	'sales@stacklink.co.uk',	'https://stacklink.co.uk/',	'+447440669339',	'.',	'+447440669339',	'436 6804 77',	',',	'2024-09-30',	'LLOYDS BANK',	'32756168',	NULL,	NULL,	NULL,	'public/uploads/staff/0a19fcb2d3cf1c3e3d013f961bf42ad6.png',	NULL,	NULL,	'',	NULL,	1,	1,	47,	'2025-04-29 04:29:46',	'2024-07-19 13:34:57',	'stacklink-pdf-header.jpg',	'stacklink-pdf-footer.jpg',	'stacklink-watermark-sm.png',	'stacklink-pdf-first-page.jpg',	'SIVSL',	'USL',	9,	2,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(8,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	8,	'SUPREME SYSTEM TRADING ESTABLISHMENT',	'6549, Wadi Al Shuara - \r\nAl Olaya District, Unit No 5569, \r\nRiyadh 12211 - 3805',	194,	'Riyadh',	'sales@supreme.sa',	'https://supreme.sa/',	'+966-11-210-9668',	'NA',	'+966-55-490-9327',	'302071027400003',	'1010694862',	'2025-02-07',	'Riyad Bank',	'3233731619940',	NULL,	NULL,	NULL,	'public/uploads/company/4a0e9e6fad11efcd92f2bccff75963b1.png',	NULL,	NULL,	'',	NULL,	1,	1,	1,	'2025-04-29 04:29:47',	'2024-06-05 15:36:06',	'supreme-pdf-header.jpg',	'supreme-pdf-footer.jpg',	'syscom-watermark-sm.png',	'supreme-pdf-first-page.jpg',	'SUP',	'SA',	4,	2,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(9,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	9,	'SYSCOM DISTRIBUTION WLL',	'Off. # 6, First Floor,\r\nAl Tadamon Complex, \r\nD ring road, Doha, Qatar',	179,	'Doha',	'sales@sysllc.com',	'www.sysllc.com',	'+97444906875',	'+97444906875',	'+97433450411',	'NA',	'200354',	'2024-09-28',	'Qatar National Bank',	'0250559112001',	NULL,	NULL,	NULL,	'public/uploads/company/009652d562097d5eea5bdead831c7782.jpg',	NULL,	NULL,	'',	NULL,	1,	1,	1,	'2025-04-29 04:29:48',	'2024-06-05 15:36:40',	'syscom-qatar-pdf-header.jpg',	'syscom-qatar-pdf-footer.jpg',	'syscom-watermark-sm.png',	'syscom-qatar-pdf-first-page.jpg',	'SIQ',	'Q',	2,	2,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(10,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	10,	'SUPREME SYSTEM DISTRIBUTORS SPC',	'Floor 6, Al Shumoor Building, \r\nOpp to Chamber of Commerce, CBD, \r\nMuscat, Sultanate of Oman',	166,	'Ruwi',	'sales@ssd.om',	'www.ssd.om',	'+968 24781836',	'+968 24781836',	'+968 92743953',	'OM1100238191',	'1401539',	'2024-10-20',	'Bank Muscat',	'0327061228940014',	'OM880270327061228940014',	NULL,	'AL KHUWAIR 33',	'public/uploads/staff/9eaafc7f3b00d27c52a704566bef626b.png',	NULL,	NULL,	'',	NULL,	1,	17,	1,	'2025-04-29 04:29:38',	'2024-11-06 08:42:16',	'supreme-spc-pdf-header.jpg',	'supreme-spc-pdf-footer.jpg',	'syscom-watermark-sm.png',	'supreme-spc-pdf-first-page.jpg',	'SSD-INV',	'O',	3,	3,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(11,	'd9898',	'SDF',	'SDF',	'DSF',	'2025-09-21',	'2025-09-11',	NULL,	0,	'new company added',	'DSFSDF',	4,	'DSSD',	'DFSDF@gmail.com',	'https://www.google.com/',	'343434',	'3434',	'343434',	NULL,	NULL,	NULL,	'sddsf',	'32323',	'2323',	'2323',	'232',	'uploads/company/banking_letters/1bb2d994-3f52-43da-b6f7-9735c0313af2.png',	'GBP',	3,	'public/uploads/company/stamp/a0e9a41c-3dc6-433c-aa79-35b060e173d4.jpg',	NULL,	NULL,	NULL,	NULL,	'2025-09-26 11:33:11',	'2025-09-26 12:03:11',	NULL,	NULL,	NULL,	NULL,	'3434',	'3434',	1,	NULL,	0,	NULL,	NULL,	'C:\\Users\\DELL\\AppData\\Local\\Temp\\phpA9F5.tmp',	'DFSDF@gmail.com',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2323',	'sdfsdf'),
(12,	'sdfdsf',	'sdfds',	'sdf',	'sdfsdf',	'2025-09-04',	'2025-09-20',	NULL,	0,	'eeeeee',	'dsedfds',	1,	'dsfds',	'dfsdf@gmail.com',	'https://www.google.com/',	'334',	'343',	'34',	NULL,	NULL,	NULL,	'sdfds',	'23',	'232',	'',	'23',	'uploads/company/banking_letters/c3913b98-5cea-4026-9b43-e9df37f5b575.png',	'INR',	2,	NULL,	NULL,	NULL,	NULL,	NULL,	'2025-09-26 12:00:53',	'2025-09-26 12:30:53',	NULL,	NULL,	NULL,	NULL,	'2',	'2',	1,	NULL,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

-- 2025-09-30 11:52:28
