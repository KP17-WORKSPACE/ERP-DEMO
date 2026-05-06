<script>
// ====== ROUTES ==============================================================
const URLS = {
  basic  : "<?php echo e(route('company.basic.store')); ?>",
  comp   : "<?php echo e(route('company.compliance.store')); ?>",
  docs   : "<?php echo e(route('company.docs.store')); ?>",
  bank   : "<?php echo e(route('company.banking.store')); ?>",
  hr     : "<?php echo e(route('company.hrpayroll.store')); ?>",
  policy : "<?php echo e(route('company.hrpolicy.store')); ?>"
};
const CSRF = "<?php echo e(csrf_token()); ?>";

// ====== VUE APP =============================================================
new Vue({
  el: '#companyApp',

  data: {
    loading:false, flash:'', errors:{},

    form: {
      company_id: (typeof IS_EDIT !== 'undefined' && IS_EDIT && typeof EDIT_ID !== 'undefined' && EDIT_ID) ? EDIT_ID : <?php echo e($nextId ?? 'null'); ?>,
      company_name: '', trade_name:'', business_entity_type_id:'', industry_type_id:'', parent_company:'',
      date_of_incorporation:'', country:'', city:'', company_address:'', sales_code:'', other_code:'',
      currency:'', currency_digit:'', book_closed:'',
      company_logo:null, digital_stamp:null, company_profile:null,

      email:'', website:'', telephone:'', fax:'', mobile:'', contact_sections:[],
      owner:{ name:'', mobile:'', email:'', files:{ passport_copy:null, emirates_id:null, visa_copy:null } },
      sponsor:{ name:'', mobile:'', email:'', files:{ passport_copy:null, emirates_id:null, visa_copy:null } },
      contact:{ name:'', mobile:'', email:'', designation:'', files:{ passport_copy:null, emirates_id:null, visa_copy:null } },

      compliance:{
        business_license_number:'', license_issue_date:'', license_expiry_date:'',
        issuing_authority:'', tax_applicable:'',
        vat_registration_number:'', vat_percentage:'', vat_date:'',
        corporate_tax_number:'', corporate_tax_vat:'', corporate_tax_date:'',
        business_license_upload:null, vat_certificate:null, corporate_tax_certificate:null,
        // optional preview urls (only for UI badges, not required)
        business_license_url:null, vat_certificate_url:null, corporate_tax_certificate_url:null
      },

      hr:{
        wps_establishment_id:'', wps_bank:'', wps_salary_file_code:'',
        payroll_cycle:'', weekly_off:'', gratuity_method:'',
        insurance_provider:'', insurance_policy_number:'', insurance_policy_expiry:'',
      }
    },

    // Policies (id supports update)
    policies: [
      { uid:'pol_'+Date.now(), id:null, policy_date:'', policy_name:'', policy_category:'',
        policy_valid:'', view_to_employees:1, policy_details:'', policy_file:null, policy_file_url:null }
    ],
    editors:{},

    // Documents (edit: existing file URLs hold karein)
    docs:{
      establishment:{ number:'', expiry:'', file:null, url:null },
      immigration:{ number:'', expiry:'', file:null, url:null },
      labour:{ number:'', expiry:'', file:null, url:null },
      chamber:{ number:'', expiry:'', file:null, url:null },
      insurance:{ number:'', expiry:'', file:null, url:null },
      moa_aoa:{ file:null, url:null },
      board_resolution:{ file:null, url:null },
      poa:{ file:null, url:null },
    }
  },

  mounted(){
    // select2
    const $sel = $('#contactSections');
    if ($sel.length) {
      $sel.select2({ theme:'bootstrap-5', placeholder:'Select', allowClear:true, width:'100%', dropdownParent:$('#contactinfo') });
      $sel.val(this.form.contact_sections).trigger('change');
      $sel.on('change', ()=> this.form.contact_sections = $sel.val() || []);
    }

    // CKEditor init (first row)
    this.$nextTick(()=> this.policies.forEach((p,i)=> this.initPolicyEditor(p.uid, i)));

    // Banking wiring
    this.initBankRows();

    // EDIT MODE: hydrate from SEED
    if (typeof IS_EDIT !== 'undefined' && IS_EDIT && typeof SEED !== 'undefined' && SEED) {
      this.hydrateFromSeed(SEED);
    }
  },

  watch:{
    'form.contact_sections'(val){
      const $sel = $('#contactSections');
      if ($sel.length) {
        const cur = $sel.val() || [], next = val || [];
        if (cur.join(',') !== next.join(',')) $sel.val(next).trigger('change.select2');
      }
    }
  },

  computed:{
    showVAT(){ return ['vat','both'].includes(this.form.compliance.tax_applicable); },
    showCT(){  return ['ct','both'].includes(this.form.compliance.tax_applicable); }
  },

  methods:{
    // ---------- Utils ----------
    toYMD(val){
      if (!val) return '';
      const s = String(val).trim();
      let m = s.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/);
      if (m) return `${m[3]}-${m[2]}-${m[1]}`;
      m = s.match(/^(\d{4})[\/\-](\d{2})[\/\-](\d{2})$/);
      if (m) return `${m[1]}-${m[2]}-${m[3]}`;
      return s;
    },
    activateTab(sel){
      try { const t=document.querySelector(`[data-bs-target="${sel}"]`); if (t) new bootstrap.Tab(t).show(); } catch(e){}
    },

    // ---------- Files ----------
    onFile(e,key){ this.form[key] = e.target.files[0] || null; },
    onComplianceFile(e,key){ this.form.compliance[key] = e.target.files[0] || null; },
    onDocFile(e,key){ const f=e.target.files?.[0]||null; if(!f) return; if(this.docs[key]) this.docs[key].file=f; },

    // ---------- CKEditor / Policies ----------
    async initPolicyEditor(uid, idx){
      const el = document.getElementById('policy_details_'+uid);
      if (!el || !window.ClassicEditor) return;
      try{
        const editor = await ClassicEditor.create(el, { toolbar:['heading','bold','italic','bulletedList','numberedList','blockQuote','undo','redo','link'] });
        this.$set(this.editors, uid, editor);
        editor.setData(this.policies[idx].policy_details || '');
        editor.model.document.on('change:data', ()=>{
          const i = this.policies.findIndex(x=>x.uid===uid);
          if (i!==-1) this.policies[i].policy_details = editor.getData();
        });
      }catch(err){ console.error('CK init fail', err); }
    },
    destroyPolicyEditor(uid){ const ed=this.editors[uid]; if(ed&&ed.destroy) ed.destroy(); if(this.editors[uid]) this.$delete(this.editors, uid); },
    addPolicy(){
      const uid='pol_'+Date.now()+'_'+Math.floor(Math.random()*1e6);
      this.policies.push({ uid, id:null, policy_date:'', policy_name:'', policy_category:'', policy_valid:'', view_to_employees:1, policy_details:'', policy_file:null, policy_file_url:null });
      this.$nextTick(()=> this.initPolicyEditor(uid, this.policies.length-1));
    },
    removePolicy(idx){ const uid=this.policies[idx].uid; this.destroyPolicyEditor(uid); this.policies.splice(idx,1); },
    onPolicyFile(e, idx){ const f=e.target.files?.[0]||null; this.$set(this.policies[idx], 'policy_file', f); },

    // ---------- Banking inline errors ----------
    clearBankErrors(){
      const $tbody=$('#bankTable').find('tbody');
      $tbody.find('input, select').removeClass('is-invalid');
      $tbody.find('.invalid-feedback.bank-feedback').remove();
    },
    renderBankErrors(serverErrors){
      const $tbody=$('#bankTable').find('tbody');
      const mapSelector={
        bank_name:'input[name*="[bank_name]"]', branch_name:'input[name*="[branch_name]"]',
        account_number:'input[name*="[account_number]"]', iban_number:'input[name*="[iban_number]"]',
        swift_code:'input[name*="[swift_code]"]', finance_code:'input[name*="[finance_code]"]',
        currency:'select[name*="[currency]"]', bank_letter:'input[type="file"][name*="[bank_letter]"]'
      };
      let firstInvalidEl=null;
      Object.keys(serverErrors||{}).forEach(key=>{
        if(!key.startsWith('banks.')) return;
        const parts=key.split('.'); const idx=parseInt(parts[1],10); const field=parts[2];
        const $row=$tbody.find('tr').eq(idx); if(!$row.length) return;
        const sel=mapSelector[field]; if(!sel) return;
        const $el=$row.find(sel).first(); if(!$el.length) return;
        const msg=(serverErrors[key]&&serverErrors[key][0])?serverErrors[key][0]:'Invalid';
        $el.addClass('is-invalid'); $el.after($('<div class="invalid-feedback d-block bank-feedback"></div>').text(msg));
        if(!firstInvalidEl) firstInvalidEl=$el[0];
      });
      if(firstInvalidEl) firstInvalidEl.scrollIntoView({behavior:'smooth',block:'center'});
    },

    // ---------- Banking rows (prefill support) ----------
    initBankRows(){
      const $tbody=$('#bankTable').find('tbody');

      // blank state (create)
      if($tbody.find('tr').length===0){
        this.addBankRow(0);
      }

      // edit prefill (if seed has banks)
      if (typeof IS_EDIT!=='undefined' && IS_EDIT && typeof SEED!=='undefined' && SEED && Array.isArray(SEED.banks) && SEED.banks.length){
        $tbody.empty();
        SEED.banks.forEach((b,i)=> this.addBankRow(i, b));
      }

      // add row
      $(document).off('click.addBankRow').on('click.addBankRow', '#addBankRow', (e)=>{
        e.preventDefault();
        const idx=$tbody.find('tr').length;
        this.addBankRow(idx);
      });

      // delete row
      $(document).off('click.delBankRow').on('click.delBankRow', '.delBankRow', (e)=>{
        e.preventDefault();
        const $tr=$(e.currentTarget).closest('tr');
        const $all=$tbody.find('tr');
        if($all.length===1){
          $tr.find('input[type="text"], input[type="file"]').val('');
          $tr.find('select').val('');
          return;
        }
        $tr.remove();
        this.reindexBankRows();
      });
    },

    addBankRow(index, row={}){
      const esc = s => (s==null?'':String(s).replace(/"/g,'&quot;'));
      const bank_name = esc(row.bank_name);
      const branch    = esc(row.branch_name);
      const acc       = esc(row.account_number);
      const iban      = esc(row.iban_number);
      const swift     = esc(row.swift_code);
      const fin       = esc(row.finance_code);
      const cur       = esc(row.currency);

      const rowHtml = `
        <tr>
          <td><input type="text" class="form-control" name="banks[${index}][bank_name]" value="${bank_name}" required></td>
          <td><input type="text" class="form-control" name="banks[${index}][branch_name]" value="${branch}"></td>
          <td><input type="text" class="form-control" name="banks[${index}][account_number]" value="${acc}" required></td>
          <td><input type="text" class="form-control" name="banks[${index}][iban_number]" value="${iban}" required></td>
          <td><input type="text" class="form-control" name="banks[${index}][swift_code]" value="${swift}"></td>
          <td><input type="text" class="form-control" name="banks[${index}][finance_code]" value="${fin}"></td>
          <td>
            <select class="form-control" name="banks[${index}][currency]">
              ${['','AED','USD','INR','EUR','GBP','SAR','QAR','OMR','KWD'].map(opt=>{
                const sel = (opt===cur)?'selected':'';
                const label = opt || '-Select-';
                const val = opt;
                return `<option value="${val}" ${sel}>${label}</option>`;
              }).join('')}
            </select>
          </td>
          <td>
            <input type="file" class="form-control" name="banks[${index}][bank_letter]" accept="image/*,.pdf" ${row && row.bank_letter_url ? '' : 'required'}>
            ${row && row.bank_letter_url ? `<a href="${row.bank_letter_url}" target="_blank" class="small d-inline-block mt-1">View current</a>` : ''}
          </td>
          <td class="text-center">
            <button type="button" class="btn btn-light text-dark btn-sm delBankRow">
              <i class="ico icon-bold-trash-bin-minimalistic-2"></i>
            </button>
          </td>
        </tr>
      `;
      $('#bankTable').find('tbody').append(rowHtml);
    },

    reindexBankRows(){
      const $tbody=$('#bankTable').find('tbody');
      $tbody.find('tr').each(function(i,tr){
        $(tr).find('input, select, a').each(function(_, el){
          const $el=$(el);
          const name=$el.attr('name');
          if(name){ $el.attr('name', name.replace(/banks\[\d+\]/, `banks[${i}]`)); }
        });
      });
    },

    // ---------- EDIT: hydrate ----------
    hydrateFromSeed(seed){
      // form
      Object.assign(this.form, {
        company_id: seed.company?.id ?? this.form.company_id,
        company_name: seed.company?.company_name ?? '',
        trade_name: seed.company?.trade_name ?? '',
        business_entity_id: seed.company?.business_entity_id ?? '',
        industry: seed.company?.industry ?? '',
        parent_company: seed.company?.parent_company ?? '',
        date_of_incorporation: seed.company?.date_of_incorporation ?? '',
        country: seed.company?.country ?? '',
        city: seed.company?.city ?? '',
        company_address: seed.company?.company_address ?? '',
        sales_code: seed.company?.sales_code ?? '',
        other_code: seed.company?.other_code ?? '',
        currency: seed.company?.currency ?? '',
        currency_digit: seed.company?.currency_digit ?? '',
        book_closed: seed.company?.book_closed ?? '',
        email: seed.company?.email ?? '',
        website: seed.company?.website ?? '',
        telephone: seed.company?.telephone ?? '',
        fax: seed.company?.fax ?? '',
        mobile: seed.company?.mobile ?? '',
        contact_sections: seed.company?.contact_sections ?? [],
        owner: { name:seed.company?.owner?.name||'', mobile:seed.company?.owner?.mobile||'', email:seed.company?.owner?.email||'', files:{passport_copy:null, emirates_id:null, visa_copy:null} },
        sponsor:{ name:seed.company?.sponsor?.name||'', mobile:seed.company?.sponsor?.mobile||'', email:seed.company?.sponsor?.email||'', files:{passport_copy:null, emirates_id:null, visa_copy:null} },
        contact:{ name:seed.company?.contact?.name||'', mobile:seed.company?.contact?.mobile||'', email:seed.company?.contact?.email||'', designation:seed.company?.contact?.designation||'', files:{passport_copy:null, emirates_id:null, visa_copy:null} },
        compliance: Object.assign(this.form.compliance, seed.compliance||{}),
        hr: Object.assign(this.form.hr, seed.hr||{})
      });

      // policies
      if (Array.isArray(seed.policies) && seed.policies.length){
        // destroy existing editors
        this.policies.forEach(p=> this.destroyPolicyEditor(p.uid));
        this.policies = seed.policies.map(p=>({
          uid: 'pol_'+Date.now()+'_'+Math.floor(Math.random()*1e6),
          id: p.id,
          policy_date: p.policy_date || '',
          policy_name: p.policy_name || '',
          policy_category: p.policy_category || '',
          policy_valid: p.policy_valid || '',
          view_to_employees: Number(p.view_to_employees ?? 1),
          policy_details: p.policy_details || '',
          policy_file: null,
          policy_file_url: p.policy_file_url || null
        }));
        this.$nextTick(()=> this.policies.forEach((p,i)=> this.initPolicyEditor(p.uid, i)));
      }

      // documents
      if (seed.docs){
        Object.keys(this.docs).forEach(k=>{
          if (seed.docs[k]){
            this.docs[k].number = seed.docs[k].number || '';
            this.docs[k].expiry = seed.docs[k].expiry || '';
            this.docs[k].url    = seed.docs[k].url    || null;
            this.docs[k].file   = null;
          }
        });
      }

      // contactSections select2 reflect
      const $sel = $('#contactSections');
      if ($sel.length) $sel.val(this.form.contact_sections).trigger('change.select2');
    },

    // ---------- FormData builders ----------
    buildFdBasic(){
      const fd=new FormData();
      fd.append('date_of_incorporation', this.toYMD(this.form.date_of_incorporation));
      fd.append('book_closed', this.toYMD(this.form.book_closed));
      ['company_id','company_name','trade_name','business_entity_type_id','industry_type_id','parent_company','country','city','company_address','sales_code','other_code','currency','currency_digit'].forEach(k=> fd.append(k, this.form[k]??''));
      if (this.form.company_logo)    fd.append('company_logo', this.form.company_logo);
      if (this.form.digital_stamp)   fd.append('digital_stamp', this.form.digital_stamp);
      if (this.form.company_profile) fd.append('company_profile', this.form.company_profile);
      ['email','website','telephone','fax','mobile'].forEach(k=> fd.append(k, this.form[k]??''));
      (this.form.contact_sections||[]).forEach((v,i)=> fd.append(`contact_sections[${i}]`, v));
      [{key:'owner',prefix:'owner'},{key:'sponsor',prefix:'sponsor'},{key:'contact',prefix:'contact'}].forEach(p=>{
        const obj=this.form[p.key]||{};
        fd.append(`${p.prefix}_name`, obj.name||''); fd.append(`${p.prefix}_mobile`, obj.mobile||''); fd.append(`${p.prefix}_email`, obj.email||'');
        if(p.key==='contact') fd.append('contact_person_designation', obj.designation||'');
        // NOTE: edit mode me in teenon ke files optional rakho (sirf choose kare to bhejo)
        if (obj.files) {
          if (obj.files.passport_copy) fd.append(`${p.prefix}_passport_copy`, obj.files.passport_copy);
          if (obj.files.emirates_id)   fd.append(`${p.prefix}_emirates_id`,   obj.files.emirates_id);
          if (obj.files.visa_copy)     fd.append(`${p.prefix}_visa_copy`,     obj.files.visa_copy);
        }
      });
      return fd;
    },

    buildFdCompliance(){
      const c=this.form.compliance||{}; const fd=new FormData();
      fd.append('license_issue_date',  this.toYMD(c.license_issue_date));
      fd.append('license_expiry_date', this.toYMD(c.license_expiry_date));
      fd.append('vat_date',            this.toYMD(c.vat_date));
      fd.append('corporate_tax_date',  this.toYMD(c.corporate_tax_date));
      ['business_license_number','issuing_authority','tax_applicable','vat_registration_number','vat_percentage','corporate_tax_number','corporate_tax_vat'].forEach(k=> fd.append(k, c[k]??''));
      if (c.business_license_upload) fd.append('business_license_upload', c.business_license_upload);
      if (['vat','both'].includes(c.tax_applicable) && c.vat_certificate) fd.append('vat_certificate', c.vat_certificate);
      if (['ct','both'].includes(c.tax_applicable) && c.corporate_tax_certificate) fd.append('corporate_tax_certificate', c.corporate_tax_certificate);
      fd.append('company_id', this.form.company_id??'');
      return fd;
    },

    buildFdHRPayroll(){
      const h=this.form.hr||{}; const fd=new FormData();
      const d=this.toYMD(h.insurance_policy_expiry); if (d) fd.append('insurance_policy_expiry', d);
      fd.append('wps_establishment_id', h.wps_establishment_id??'');
      fd.append('wps_bank', h.wps_bank??'');
      fd.append('payroll_cycle', h.payroll_cycle??'');
      if (h.weekly_off)      fd.append('weekly_off', h.weekly_off);
      if (h.gratuity_method) fd.append('gratuity_method', h.gratuity_method);
      fd.append('wps_salary_file_code', h.wps_salary_file_code??'');
      fd.append('insurance_provider', h.insurance_provider??'');
      fd.append('insurance_policy_number', h.insurance_policy_number??'');
      fd.append('company_id', this.form.company_id??'');
      return fd;
    },

    buildFdBanking(){
      const fd=new FormData();
      fd.append('company_id', this.form.company_id??'');
      const $tbody=$('#bankTable').find('tbody'); const $rows=$tbody.find('tr');
      $rows.each(function(rowIdx,tr){
        const $tr=$(tr);
        const bank_name=$tr.find('input[name*="[bank_name]"]').val()||'';
        const branch=$tr.find('input[name*="[branch_name]"]').val()||'';
        const account=$tr.find('input[name*="[account_number]"]').val()||'';
        const iban=$tr.find('input[name*="[iban_number]"]').val()||'';
        const swift=$tr.find('input[name*="[swift_code]"]').val()||'';
        const finance=$tr.find('input[name*="[finance_code]"]').val()||'';
        const currency=$tr.find('select[name*="[currency]"]').val()||'';
        const letterInput=$tr.find('input[type="file"][name*="[bank_letter]"]')[0];
        const letter=letterInput && letterInput.files ? letterInput.files[0] : null;
        fd.append(`banks[${rowIdx}][bank_name]`, bank_name);
        fd.append(`banks[${rowIdx}][branch_name]`, branch);
        fd.append(`banks[${rowIdx}][account_number]`, account);
        fd.append(`banks[${rowIdx}][iban_number]`, iban);
        fd.append(`banks[${rowIdx}][swift_code]`, swift);
        fd.append(`banks[${rowIdx}][finance_code]`, finance);
        fd.append(`banks[${rowIdx}][currency]`, currency);
        if (letter) fd.append(`banks[${rowIdx}][bank_letter]`, letter); // edit: only if changed
      });
      return fd;
    },

    buildFdPolicies(){
      const fd=new FormData();
      fd.append('company_id', this.form.company_id??'');
      this.policies.forEach((p,i)=>{
        const d=this.toYMD(p.policy_date); if (d) fd.append(`policies[${i}][policy_date]`, d);
        const v=this.toYMD(p.policy_valid); if (v) fd.append(`policies[${i}][policy_valid]`, v);
        if (p.id!=null) fd.append(`policies[${i}][id]`, p.id); // EDIT: send id so backend updates
        fd.append(`policies[${i}][policy_name]`, p.policy_name||'');
        fd.append(`policies[${i}][policy_category]`, p.policy_category||'');
        fd.append(`policies[${i}][view_to_employees]`, p.view_to_employees??'');
        fd.append(`policies[${i}][policy_details]`, p.policy_details||'');
        if (p.policy_file) fd.append(`policies[${i}][policy_file]`, p.policy_file); // only if changed
      });
      return fd;
    },

    buildFdDocs(){
      const fd=new FormData(); fd.append('company_id', this.form.company_id??'');
      const norm=v=>this.toYMD(v); const p=this.docs;
      if (p.establishment.number) fd.append('establishment_number', p.establishment.number);
      if (p.establishment.expiry) fd.append('establishment_expiry', norm(p.establishment.expiry));
      if (p.establishment.file)   fd.append('establishment_file', p.establishment.file);

      if (p.immigration.number) fd.append('immigration_number', p.immigration.number);
      if (p.immigration.expiry) fd.append('immigration_expiry', norm(p.immigration.expiry));
      if (p.immigration.file)   fd.append('immigration_file', p.immigration.file);

      if (p.labour.number) fd.append('labour_number', p.labour.number);
      if (p.labour.expiry) fd.append('labour_expiry', norm(p.labour.expiry));
      if (p.labour.file)   fd.append('labour_file', p.labour.file);

      if (p.chamber.number) fd.append('chamber_number', p.chamber.number);
      if (p.chamber.expiry) fd.append('chamber_expiry', norm(p.chamber.expiry));
      if (p.chamber.file)   fd.append('chamber_file', p.chamber.file);

      if (p.insurance.number) fd.append('insurance_certificate_number', p.insurance.number);
      if (p.insurance.expiry) fd.append('insurance_certificate_expiry', norm(p.insurance.expiry));
      if (p.insurance.file)   fd.append('insurance_file', p.insurance.file);

      if (p.moa_aoa.file)          fd.append('moa_aoa_file', p.moa_aoa.file);
      if (p.board_resolution.file) fd.append('board_resolution_file', p.board_resolution.file);
      if (p.poa.file)              fd.append('poa_file', p.poa.file);
      return fd;
    },

    // ---------- Submit all ----------
    async submitAll(){
      this.loading=true; this.flash=''; this.errors={};

      try{
        await axios.post(URLS.basic, this.buildFdBasic(), { headers:{'X-CSRF-TOKEN':CSRF} });
      }catch(e){ this.errors=e.response?.data?.errors||{}; if(e.response?.status===422) this.activateTab('#contactinfo'); else alert('Server error (basic)'); this.loading=false; return; }

      try{
        await axios.post(URLS.comp, this.buildFdCompliance(), { headers:{'X-CSRF-TOKEN':CSRF} });
      }catch(e){ this.errors=e.response?.data?.errors||{}; if(e.response?.status===422) this.activateTab('#compliance-regulatory'); else alert('Server error (compliance)'); this.loading=false; return; }

      try{
        await axios.post(URLS.docs, this.buildFdDocs(), { headers:{'X-CSRF-TOKEN':CSRF} });
      }catch(e){ const errs=e.response?.data?.errors||{}; this.errors=errs; this.activateTab('#documentation'); if(e.response?.status===422) this.renderDocErrors(errs); else alert('Server error (documents)'); this.loading=false; return; }

      try{
        this.clearBankErrors();
        await axios.post(URLS.bank, this.buildFdBanking(), { headers:{'X-CSRF-TOKEN':CSRF} });
      }catch(e){ const errs=e.response?.data?.errors||null; this.errors=errs||{}; this.activateTab('#Banking-Finance'); if(errs) this.renderBankErrors(errs); else alert('Server error (banking)'); this.loading=false; return; }

      try{
        await axios.post(URLS.hr, this.buildFdHRPayroll(), { headers:{'X-CSRF-TOKEN':CSRF} });
      }catch(e){ this.errors=e.response?.data?.errors||{}; if(e.response?.status===422) this.activateTab('#hr-payroll'); else alert('Server error (HR & Payroll)'); this.loading=false; return; }

      try{
        await axios.post(URLS.policy, this.buildFdPolicies(), { headers:{'X-CSRF-TOKEN':CSRF} });
      }catch(e){ this.errors=e.response?.data?.errors||{}; if(e.response?.status===422) this.activateTab('#Policies'); else alert('Server error (Policies)'); this.loading=false; return; }

      this.flash='All data saved!';
      this.loading=false;
    }
  }
});

/* =====================================================================
   OWNER ADD/REMOVE
===================================================================== */
let ownerIndex = $("#ownerWrapper .ownerRow").length;

$(document).on("click", ".addOwner", function() {
    $("#ownerWrapper").append(`
    <div class="ownerRow row gy-2 p-2 mb-2 border rounded">
        <div class="col-lg-1">
            <label>Salutation</label>
            <select name="owners[${ownerIndex}][salutation]" class="form-select form-select-sm">
                <option value="">Select</option>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Miss">Miss</option>
                <option value="Ms">Ms</option>
                <option value="Dr">Dr</option>
            </select>
        </div>
        <div class="col-lg-2">
            <label>First Name</label>
            <input type="text" name="owners[${ownerIndex}][first_name]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-2">
            <label>Last Name</label>
            <input type="text" name="owners[${ownerIndex}][last_name]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-2">
            <label>Mobile</label>
            <input type="text" name="owners[${ownerIndex}][mobile]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-2">
            <label>Email</label>
            <input type="email" name="owners[${ownerIndex}][email]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Passport</label>
            <input type="file" name="owner_files[${ownerIndex}][passport_copy]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Emirates ID</label>
            <input type="file" name="owner_files[${ownerIndex}][emirates_id]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Visa</label>
            <div class="d-flex gap-1">
                <input type="file" name="owner_files[${ownerIndex}][visa_copy]" class="form-control form-control-sm">
                <button type="button" class="btn btn-danger btn-sm removeOwner">-</button>
            </div>
        </div>
    </div>
`);
    ownerIndex++;
});

$(document).on("click", ".removeOwner", function() {
    $(this).closest(".ownerRow").remove();
});

/* =====================================================================
   SPONSOR ADD/REMOVE
===================================================================== */
let sponsorIndex = $("#sponsorWrapper .sponsorRow").length;

$(document).on("click", ".addSponsor", function() {
    $("#sponsorWrapper").append(`
    <div class="sponsorRow row gy-2 p-2 mb-2 border rounded">
        <div class="col-lg-1">
            <label>Salutation</label>
            <select name="sponsors[${sponsorIndex}][salutation]" class="form-select form-select-sm">
                <option value="">Select</option>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Miss">Miss</option>
                <option value="Ms">Ms</option>
                <option value="Dr">Dr</option>
            </select>
        </div>
        <div class="col-lg-2">
            <label>First Name</label>
            <input type="text" name="sponsors[${sponsorIndex}][first_name]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-2">
            <label>Last Name</label>
            <input type="text" name="sponsors[${sponsorIndex}][last_name]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-2">
            <label>Mobile</label>
            <input type="text" name="sponsors[${sponsorIndex}][mobile]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-2">
            <label>Email</label>
            <input type="email" name="sponsors[${sponsorIndex}][email]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Passport</label>
            <input type="file" name="sponsor_files[${sponsorIndex}][passport_copy]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Emirates ID</label>
            <input type="file" name="sponsor_files[${sponsorIndex}][emirates_id]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Visa</label>
            <div class="d-flex gap-1">
                <input type="file" name="sponsor_files[${sponsorIndex}][visa_copy]" class="form-control form-control-sm">
                <button type="button" class="btn btn-danger btn-sm removeSponsor">-</button>
            </div>
        </div>
    </div>
`);
    sponsorIndex++;
});

$(document).on("click", ".removeSponsor", function() {
    $(this).closest(".sponsorRow").remove();
});

/* =====================================================================
   CONTACT PERSON ADD/REMOVE
===================================================================== */
let contactIndex = $("#contactWrapper .contactRow").length;

$(document).on("click", ".addContact", function() {
    $("#contactWrapper").append(`
    <div class="contactRow row gy-2 p-2 mb-2 border rounded">
        <div class="col-lg-1">
            <label>Salutation</label>
            <select name="contacts[${contactIndex}][salutation]" class="form-select form-select-sm">
                <option value="">Select</option>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Miss">Miss</option>
                <option value="Ms">Ms</option>
                <option value="Dr">Dr</option>
            </select>
        </div>
        <div class="col-lg-1">
            <label>First Name</label>
            <input type="text" name="contacts[${contactIndex}][first_name]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Last Name</label>
            <input type="text" name="contacts[${contactIndex}][last_name]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-2">
            <label>Mobile</label>
            <input type="text" name="contacts[${contactIndex}][mobile]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-2">
            <label>Email</label>
            <input type="email" name="contacts[${contactIndex}][email]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-2">
            <label>Designation</label>
            <input type="text" name="contacts[${contactIndex}][designation]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Passport</label>
            <input type="file" name="contact_files[${contactIndex}][passport_copy]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Emirates ID</label>
            <input type="file" name="contact_files[${contactIndex}][emirates_id]" class="form-control form-control-sm">
        </div>
        <div class="col-lg-1">
            <label>Visa</label>
            <div class="d-flex gap-1">
                <input type="file" name="contact_files[${contactIndex}][visa_copy]" class="form-control form-control-sm">
                <button type="button" class="btn btn-danger btn-sm removeContact">-</button>
            </div>
        </div>
    </div>
`);
    contactIndex++;
});

$(document).on("click", ".removeContact", function() {
    $(this).closest(".contactRow").remove();
});

</script>
