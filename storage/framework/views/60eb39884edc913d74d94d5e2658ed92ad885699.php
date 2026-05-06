
<?php
    $base = trim((string) ($reportBaseTitle ?? 'Report'));
    $parts = $base !== '' ? [$base] : [];

    $pickTitle = function ($collection, $id, $key = 'id', $titleKey = 'title') {
        if ($collection === null || $id === '' || $id === null) {
            return null;
        }
        $id = is_numeric($id) ? (int) $id : $id;
        $row = $collection->firstWhere($key, $id);

        return $row ? (string) data_get($row, $titleKey) : null;
    };

    if (!empty($ctrlBrand) && (string) $ctrlBrand !== 'all' && !empty($brands)) {
        $t = $pickTitle($brands, $ctrlBrand, 'id', 'title');
        if ($t !== null && $t !== '') {
            $parts[] = $t;
        }
    }
    if (!empty($ctrlCategory) && !empty($categories)) {
        $t = $pickTitle($categories, $ctrlCategory, 'id', 'category_name');
        if ($t !== null && $t !== '') {
            $parts[] = $t;
        }
    }
    if (!empty($ctrlSubCategory) && !empty($subCategories)) {
        $t = $pickTitle($subCategories, $ctrlSubCategory, 'id', 'sub_category_name');
        if ($t !== null && $t !== '') {
            $parts[] = $t;
        }
    }
    if (!empty($ctrlCompany) && $ctrlCompany !== '' && !empty($companies)) {
        $t = $pickTitle($companies, $ctrlCompany, 'id', 'company_name');
        if ($t !== null && $t !== '') {
            $parts[] = $t;
        }
    }
    if (!empty($ctrlSupplier) && !empty($suppliers)) {
        $t = $pickTitle($suppliers, $ctrlSupplier, 'id', 'account_name');
        if ($t !== null && $t !== '') {
            $parts[] = $t;
        }
    }
    if (!empty($ctrlSalesPerson) && !empty($salesPersons)) {
        $t = $pickTitle($salesPersons, $ctrlSalesPerson, 'user_id', 'full_name');
        if ($t !== null && $t !== '') {
            $parts[] = $t;
        }
    }
    if (!empty($ctrlPartNumber)) {
        $pn = trim((string) $ctrlPartNumber);
        if ($pn !== '') {
            $parts[] = $pn;
        }
    }
    if (!empty($extraHeadingSegment)) {
        $ex = trim((string) $extraHeadingSegment);
        if ($ex !== '') {
            $parts[] = $ex;
        }
    }

    $reportPageHeading = implode(' - ', array_filter($parts));
?>

    <span class=" m-0"><?php echo e($reportPageHeading); ?></span>

