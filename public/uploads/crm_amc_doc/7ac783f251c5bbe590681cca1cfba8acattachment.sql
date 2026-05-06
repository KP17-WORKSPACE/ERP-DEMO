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

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `hrms_approver_chain_steps`;
CREATE TABLE `hrms_approver_chain_steps` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `approver_chain_id` int NOT NULL,
  `step_no` int NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approver_id` int DEFAULT NULL,
  `status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'P' COMMENT 'P=Pending, A=Approved, R=Rejected, S=Skipped',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `acted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `l1_workload` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_coverage` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_eligibility` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_duration_ok` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_notice_compliance` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_decision` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_remark` text COLLATE utf8mb4_unicode_ci,
  `l2_balance` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_unpaid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_encash` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_cost` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_policy` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_decision` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_remark` text COLLATE utf8mb4_unicode_ci,
  `l3_docs` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_policy` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_system` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_payroll` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_legal` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_decision` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_remark` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chain_step_unique` (`approver_chain_id`,`step_no`),
  KEY `chain_idx` (`approver_chain_id`),
  KEY `approver_idx` (`approver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hrms_approver_chain_steps` (`id`, `approver_chain_id`, `step_no`, `role`, `approver_id`, `status`, `comment`, `acted_at`, `created_at`, `updated_at`, `l1_workload`, `l1_coverage`, `l1_eligibility`, `l1_duration_ok`, `l1_notice_compliance`, `l1_decision`, `l1_remark`, `l2_balance`, `l2_unpaid`, `l2_encash`, `l2_cost`, `l2_policy`, `l2_decision`, `l2_remark`, `l3_docs`, `l3_policy`, `l3_system`, `l3_payroll`, `l3_legal`, `l3_decision`, `l3_remark`) VALUES
(1,	1,	1,	'Reporting Manager',	2,	'P',	NULL,	NULL,	'2025-10-04 16:00:32',	'2025-10-04 16:00:32',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	2,	1,	'Reporting Manager',	3,	'P',	NULL,	NULL,	'2025-10-04 16:14:56',	'2025-10-04 16:14:56',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(3,	2,	2,	'HR',	98,	'A',	'',	'2025-10-07 01:38:49',	'2025-10-04 16:14:56',	'2025-10-06 20:08:49',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'Valid',	'Compliant',	'Updated',	'Shared with Finance',	'Compliant',	'Approve',	'approved'),
(4,	2,	3,	'Finance',	27,	'P',	NULL,	NULL,	'2025-10-04 16:14:56',	'2025-10-04 16:14:56',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(5,	3,	1,	'Reporting Manager',	5,	'P',	NULL,	NULL,	'2025-10-06 21:00:51',	'2025-10-06 21:00:51',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(6,	3,	2,	'HR',	98,	'R',	'',	'2025-10-07 03:21:52',	'2025-10-06 21:00:51',	'2025-10-06 21:51:52',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'Not Submitted',	'Not Compliant',	'Pending',	'Not Applicable',	'Not Compliant',	'Reject',	NULL),
(7,	3,	3,	'Finance',	27,	'P',	NULL,	NULL,	'2025-10-06 21:00:51',	'2025-10-06 21:00:51',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `hrms_approver_chains`;
CREATE TABLE `hrms_approver_chains` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `leave_request_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `overall_status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'P' COMMENT 'P=Pending, A=Approved, R=Rejected',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_request_unique` (`leave_request_id`),
  KEY `staff_idx` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hrms_approver_chains` (`id`, `leave_request_id`, `staff_id`, `overall_status`, `created_at`, `updated_at`) VALUES
(1,	26,	109,	'P',	'2025-10-04 16:00:32',	'2025-10-04 16:00:32'),
(2,	27,	109,	'P',	'2025-10-04 16:14:56',	'2025-10-04 16:14:56'),
(3,	28,	109,	'P',	'2025-10-06 21:00:51',	'2025-10-06 21:00:51');

DROP TABLE IF EXISTS `sm_leave_defines`;
CREATE TABLE `sm_leave_defines` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint DEFAULT NULL,
  `type_id` tinyint DEFAULT NULL,
  `days` int DEFAULT NULL,
  `active_status` tinyint NOT NULL DEFAULT '1',
  `created_by` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `sm_leave_requests`;
CREATE TABLE `sm_leave_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `leave_define_id` int DEFAULT NULL,
  `staff_id` int unsigned DEFAULT NULL,
  `role_id` int unsigned DEFAULT NULL,
  `apply_date` date DEFAULT NULL,
  `leave_year` year DEFAULT NULL,
  `type_id` tinyint DEFAULT NULL,
  `leave_from` date DEFAULT NULL,
  `leave_to` date DEFAULT NULL,
  `days` decimal(5,2) DEFAULT NULL,
  `is_half_day` tinyint(1) NOT NULL DEFAULT '0',
  `half_session` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `file` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approve_status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'P for Pending, A for Approve, R for reject',
  `approver_chain` text COLLATE utf8mb4_unicode_ci,
  `current_index` tinyint NOT NULL DEFAULT '0',
  `approvals_json` text COLLATE utf8mb4_unicode_ci,
  `approved_by` int unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` int unsigned DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `active_status` tinyint NOT NULL DEFAULT '1',
  `created_by` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_slr_staff_dates` (`staff_id`,`leave_from`,`leave_to`),
  KEY `idx_slr_status` (`approve_status`),
  KEY `idx_slr_type_year` (`type_id`,`leave_year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sm_leave_requests` (`id`, `leave_define_id`, `staff_id`, `role_id`, `apply_date`, `leave_year`, `type_id`, `leave_from`, `leave_to`, `days`, `is_half_day`, `half_session`, `reason`, `note`, `file`, `approve_status`, `approver_chain`, `current_index`, `approvals_json`, `approved_by`, `approved_at`, `rejected_by`, `rejected_at`, `active_status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(22,	NULL,	109,	31,	'2025-10-04',	'2025',	1,	'2025-10-18',	'2025-10-21',	4.00,	0,	NULL,	'Hi [Manager’s Name], I would like to request leave on [date(s)] due to [reason]. Please let me know if you need any further details. Thank you.',	'Hi [Manager’s Name], I would like to request leave on [date(s)] due to [reason]. Please let me know if you need any further details. Thank you.',	NULL,	'P',	'[2,7,12]',	0,	'[{\"uid\":2,\"role\":\"RM\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":0},{\"uid\":7,\"role\":\"HR\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":1},{\"uid\":12,\"role\":\"ACC\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":2}]',	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-04 11:40:56',	'2025-10-04 11:40:56'),
(23,	NULL,	109,	31,	'2025-10-04',	'2025',	1,	'2025-10-18',	'2025-10-26',	9.00,	0,	NULL,	'sdf',	'sdfdsfs',	NULL,	'P',	'[4,7,12]',	0,	'[{\"uid\":4,\"role\":\"RM\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":0},{\"uid\":7,\"role\":\"HR\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":1},{\"uid\":12,\"role\":\"ACC\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":2}]',	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-04 11:43:10',	'2025-10-04 11:43:10'),
(20,	NULL,	109,	31,	'2025-10-04',	'2025',	1,	'2025-10-08',	'2025-10-11',	4.00,	0,	NULL,	'sdfds',	'sdfdsfdsf',	NULL,	'P',	'[2,7,12]',	0,	'[{\"uid\":2,\"role\":\"RM\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":0},{\"uid\":7,\"role\":\"HR\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":1},{\"uid\":12,\"role\":\"ACC\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":2}]',	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-04 11:39:44',	'2025-10-04 11:39:44'),
(21,	NULL,	109,	31,	'2025-10-04',	'2025',	1,	'2025-10-04',	'2025-10-05',	2.00,	0,	NULL,	'dfsdf',	'sdfdsfdsfdf',	NULL,	'P',	'[3,7,12]',	0,	'[{\"uid\":3,\"role\":\"RM\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":0},{\"uid\":7,\"role\":\"HR\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":1},{\"uid\":12,\"role\":\"ACC\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":2}]',	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-04 11:40:19',	'2025-10-04 11:40:19'),
(19,	NULL,	109,	31,	'2025-10-04',	'2025',	1,	'2025-10-08',	'2025-10-11',	4.00,	0,	NULL,	'sdfds',	'sdfdsfdsf',	NULL,	'P',	'[2,7,12]',	0,	'[{\"uid\":2,\"role\":\"RM\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":0},{\"uid\":7,\"role\":\"HR\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":1},{\"uid\":12,\"role\":\"ACC\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":2}]',	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-04 11:37:41',	'2025-10-04 11:37:41'),
(24,	NULL,	109,	31,	'2025-10-04',	'2025',	1,	'2025-10-04',	'2025-10-12',	9.00,	0,	NULL,	NULL,	NULL,	NULL,	'P',	'[1,7,12]',	0,	'[{\"uid\":1,\"role\":\"RM\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":0},{\"uid\":7,\"role\":\"HR\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":1},{\"uid\":12,\"role\":\"ACC\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":2}]',	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-04 11:43:55',	'2025-10-04 11:43:55'),
(25,	NULL,	109,	31,	'2025-10-04',	'2025',	1,	'2025-10-04',	'2025-10-09',	6.00,	0,	NULL,	NULL,	NULL,	NULL,	'P',	'[1,7,12]',	0,	'[{\"uid\":1,\"role\":\"RM\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":0},{\"uid\":7,\"role\":\"HR\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":1},{\"uid\":12,\"role\":\"ACC\",\"status\":\"pending\",\"acted_at\":null,\"comment\":null,\"index\":2}]',	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-04 12:26:04',	'2025-10-04 12:26:04'),
(26,	NULL,	109,	31,	'2025-10-04',	'2025',	1,	'2025-10-05',	'2025-10-08',	4.00,	0,	NULL,	'dsfs',	'sdfdfdsfsdf',	NULL,	'P',	NULL,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-04 16:00:32',	'2025-10-04 16:00:32'),
(27,	NULL,	109,	31,	'2025-10-04',	'2025',	1,	'2025-10-04',	'2025-10-05',	2.00,	0,	NULL,	'I am writing to request a leave of absence from [Start Date] to [End Date] due to [brief reason for leave].\r\n\r\nI have made arrangements for my ongoing projects and have briefed [Colleague\'s Name] on any urgent matters that may require attention during my absence.\r\n\r\nI will complete all pending tasks before my leave and will',	'sdfdsfdf',	NULL,	'P',	NULL,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-04 16:14:56',	'2025-10-04 16:14:56'),
(28,	NULL,	109,	31,	'2025-10-07',	'2025',	2,	'2025-10-08',	'2025-10-12',	5.00,	0,	NULL,	'dsfds',	'dsfdsf',	'leaves/MZ8yOHv4hZNO85PG9blGtoAhsmqram3BvmAkOE1m.png',	'P',	NULL,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'109',	'109',	'2025-10-06 21:00:51',	'2025-10-06 21:00:51');

DROP TABLE IF EXISTS `sm_leave_types`;
CREATE TABLE `sm_leave_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text,
  `max_days_per_year` decimal(5,2) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT '1',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `sm_leave_types` (`id`, `name`, `code`, `description`, `max_days_per_year`, `is_paid`, `is_active`, `created_at`, `updated_at`) VALUES
(1,	'Annual Leave',	'AL',	NULL,	30.00,	1,	1,	NULL,	NULL),
(2,	'Sick Leave',	'SL',	NULL,	12.00,	1,	1,	NULL,	NULL),
(3,	'Maternity Leave',	'ML',	NULL,	90.00,	1,	1,	NULL,	NULL),
(4,	'Paternity Leave',	'PL',	NULL,	15.00,	1,	1,	NULL,	NULL),
(5,	'Emergency Leave',	'EL',	NULL,	5.00,	1,	1,	NULL,	NULL),
(6,	'Hajj Leave',	'HL',	NULL,	30.00,	1,	1,	NULL,	NULL),
(7,	'Unpaid Leave',	'UL',	NULL,	NULL,	0,	1,	NULL,	NULL),
(8,	'Half-Day Leave / Early Logout',	'HD',	NULL,	NULL,	1,	1,	NULL,	NULL);

-- 2025-10-07 11:58:21
