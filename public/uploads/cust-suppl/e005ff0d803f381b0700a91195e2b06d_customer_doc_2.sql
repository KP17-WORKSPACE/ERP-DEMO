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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_cash_payment_adjestments`(IN customerId INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_cash_receipt_adjestments`(IN customerId INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_customer_ledger_entries`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_customer_opening_balance`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_ledger_entries`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_opening_balance`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_postdated_payment_adjestments`(IN customerId INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_postdated_receipt_adjestments`(IN customerId INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_supplier_ledger_entries`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_supplier_opening_balance`(IN search_case INT(10), IN from_date DATE, IN to_date DATE, IN accountid INT(10))
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `get_trialbalance`(IN from_date DATE, IN to_date DATE)
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
CREATE DEFINER=`venushrms_erpnewdesign`@`%` PROCEDURE `set_delivery_note`(IN docnumber INT(10))
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

DROP TABLE IF EXISTS `sys_company_settings`;
CREATE TABLE `sys_company_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `is_customer_code` tinyint(1) DEFAULT '0',
  `is_supplier_code` tinyint(1) DEFAULT '0',
  `is_account_code` tinyint(1) DEFAULT '0',
  `is_subaccount_code` tinyint(1) DEFAULT '0',
  `currency` varchar(10) DEFAULT NULL,
  `currency_digit` tinyint(1) DEFAULT '0',
  `book_closed` date DEFAULT NULL,
  `sales_code` varchar(50) DEFAULT NULL,
  `other_code` varchar(50) DEFAULT NULL,
  `hr_wps_establishment_id` varchar(100) DEFAULT NULL,
  `hr_wps_bank` varchar(100) DEFAULT NULL,
  `hr_wps_salary_file_code` varchar(50) DEFAULT NULL,
  `hr_payroll_cycle` enum('monthly','bi-weekly','weekly') DEFAULT NULL,
  `hr_payroll_start` tinyint DEFAULT NULL,
  `hr_payroll_end` tinyint DEFAULT NULL,
  `hr_weekly_off` varchar(15) DEFAULT NULL,
  `hr_gratuity_method` varchar(50),
  `hr_insurance_provider` varchar(100) DEFAULT NULL,
  `hr_insurance_policy_number` varchar(100) DEFAULT NULL,
  `hr_insurance_policy_expiry` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_company_settings_company` (`company_id`),
  CONSTRAINT `fk_company_settings_company` FOREIGN KEY (`company_id`) REFERENCES `sys_company` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `sys_company_settings` (`id`, `company_id`, `is_customer_code`, `is_supplier_code`, `is_account_code`, `is_subaccount_code`, `currency`, `currency_digit`, `book_closed`, `sales_code`, `other_code`, `hr_wps_establishment_id`, `hr_wps_bank`, `hr_wps_salary_file_code`, `hr_payroll_cycle`, `hr_payroll_start`, `hr_payroll_end`, `hr_weekly_off`, `hr_gratuity_method`, `hr_insurance_provider`, `hr_insurance_policy_number`, `hr_insurance_policy_expiry`, `created_at`, `updated_at`) VALUES
(4,	18,	1,	1,	1,	1,	'',	1,	'2025-05-20',	'12',	'1212',	'1',	'1',	'1',	'monthly',	1,	30,	'sunday',	'basic_salary',	'1',	'1',	'2025-05-20',	'2025-12-09 08:34:36',	'2025-12-09 08:39:28');

-- 2025-12-09 08:16:17
