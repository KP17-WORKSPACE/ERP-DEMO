-- Chart of Accounts Performance Optimization Indexes
-- Run these indexes to improve query performance without changing functionality

-- Index for sys_account_group table
CREATE INDEX IF NOT EXISTS idx_sys_account_group_status ON sys_account_group(status);

-- Index for sys_account_group_sub table  
CREATE INDEX IF NOT EXISTS idx_sys_account_group_sub_group_status ON sys_account_group_sub(group_id, status);
CREATE INDEX IF NOT EXISTS idx_sys_account_group_sub_status ON sys_account_group_sub(status);

-- Index for sys_account_group_sub2 table
CREATE INDEX IF NOT EXISTS idx_sys_account_group_sub2_sub_status ON sys_account_group_sub2(sub_id, status);
CREATE INDEX IF NOT EXISTS idx_sys_account_group_sub2_status ON sys_account_group_sub2(status);

-- Index for sys_chartofaccounts table
CREATE INDEX IF NOT EXISTS idx_sys_chartofaccounts_main_account_id ON sys_chartofaccounts(main_account_id);
CREATE INDEX IF NOT EXISTS idx_sys_chartofaccounts_subgroup2_main ON sys_chartofaccounts(subgroup2, main_account_id);
CREATE INDEX IF NOT EXISTS idx_sys_chartofaccounts_status ON sys_chartofaccounts(status);
CREATE INDEX IF NOT EXISTS idx_sys_chartofaccounts_account_name ON sys_chartofaccounts(account_name);
CREATE INDEX IF NOT EXISTS idx_sys_chartofaccounts_account_code ON sys_chartofaccounts(account_code);

-- Composite indexes for common query patterns used in the chart of accounts page
CREATE INDEX IF NOT EXISTS idx_sys_chartofaccounts_company_main ON sys_chartofaccounts(company_access, main_account_id);
CREATE INDEX IF NOT EXISTS idx_sys_chartofaccounts_search_name_code ON sys_chartofaccounts(account_name, account_code);

-- Index for faster ordering
CREATE INDEX IF NOT EXISTS idx_sys_chartofaccounts_ordering ON sys_chartofaccounts(main_account_id, account_name);