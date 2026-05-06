<tfoot>
    <tr class="table-light">
        <th colspan="2" class="text-end">Sales</th>
        <th class="text-center"><?php echo e($grand_qty_si); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_avg_rate_si, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_value_si, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_discount_si, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_taxable_si, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_vat_si, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_total_amount_si, 2, '.', ',')); ?></th>
        <th class="text-center"> <?php echo e($grand_si_doc_count); ?> </th>
    </tr>
    <tr class="table-light">
        <th colspan="2" class="text-end">Sales return</th>
        <th class="text-center"><?php echo e($grand_qty_sr); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_avg_rate_sr, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_value_sr, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_discount_sr, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_taxable_sr, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_vat_sr, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_total_amount_sr, 2, '.', ',')); ?></th>
        <th class="text-center"> <?php echo e($grand_sr_doc_count); ?> </th>
    </tr>
    <tr>
        <th colspan="2" class="text-end">Net Sales</th>
        <th class="text-center"><?php echo e($grand_qty); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_avg_rate, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_value, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_discount, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_taxableamount, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_vatamount, 2, '.', ',')); ?></th>
        <th class="text-end"><?php echo e(@App\SysHelper::com_curr_format($grand_total_amount, 2, '.', ',')); ?></th>
        <th class="text-center"><?php echo e($grand_si_doc_count); ?> / <?php echo e($grand_sr_doc_count); ?></th>
    </tr>
</tfoot>

