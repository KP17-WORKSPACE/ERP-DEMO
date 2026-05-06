<?php $__env->startSection('mainContent'); ?>
    <?php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>
<div id="companyApp">
  <?php echo $__env->make('backEnd.company._form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
</div>

<script>
  const IS_EDIT = true;
  const EDIT_ID = <?php echo e($company->id); ?>;
  const SEED    = <?php echo json_encode($seed, 15, 512) ?>; // controller se bheja hua payload
</script>


<?php echo $__env->make('backEnd.company._company_js', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>